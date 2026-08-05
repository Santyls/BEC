import os
import uuid
from datetime import date, datetime, timedelta, timezone
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, UploadFile, File, status
from pydantic import BaseModel, EmailStr, Field
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Albergue, Direccion, Donacion, Estado, Genero, InscripcionVoluntariado, Rol, Usuario
from app.security.security import (
    get_admin_user,
    get_current_user,
    get_recepcionista_or_admin,
    obtener_password_hash,
    verificar_password,
    ROL_ADMIN,
    ROL_CIUDADANO,
)

router = APIRouter(prefix="/usuarios", tags=["Usuarios"])

UPLOAD_DIR = "uploads/usuarios"
EXTENSIONES_PERMITIDAS = {"image/jpeg", "image/png", "image/webp"}
TELEFONO_PATTERN = r"^\d{10}$"  # 10 dígitos, formato mexicano sin espacios/guiones
PASSWORD_MIN_LENGTH = 6
DIAS_GRACIA_ELIMINACION = 30


# ======================================================================
# Schemas
# ======================================================================

class DireccionInput(BaseModel):
    estado_id: int
    municipio: str = Field(min_length=1, max_length=100)
    colonia: str = Field(min_length=1, max_length=150)
    calle: str = Field(min_length=1, max_length=150)
    numero_exterior: str = Field(min_length=1, max_length=20)
    numero_interior: Optional[str] = Field(default=None, max_length=20)
    codigo_postal: str = Field(pattern=r"^\d{5}$")


class DireccionResponse(DireccionInput):
    id: int

    class Config:
        from_attributes = True


class UsuarioResponse(BaseModel):
    id: int
    nombre: str
    apellido_paterno: str
    apellido_materno: str
    correo: Optional[EmailStr] = None
    # Nullable: un ciudadano autorregistrado desde el móvil (POST /auth/registro) no lo
    # captura hasta completarlo después en Mi Perfil.
    telefono: Optional[str] = None
    fecha_nacimiento: Optional[date] = None
    genero_id: Optional[int] = None
    rol_id: int
    albergue_id: Optional[int] = None
    foto_url: Optional[str] = None
    terminos_aceptados: bool
    activo: bool
    fecha_desactivacion: Optional[datetime] = None
    vetado: bool
    motivo_veto: Optional[str] = None
    # None si el usuario nunca la ha capturado (no se pide en /auth/registro, ver RF04:
    # se completa después en Mi Perfil).
    direccion: Optional[DireccionResponse] = None

    class Config:
        from_attributes = True


class UsuarioCreateStaff(BaseModel):
    """Usada por Recepcionista/Admin para dar de alta a alguien (mostrador o back-office).
    Recepcionista: solo nombre/apellidos/teléfono son obligatorios en la práctica —
    correo es opcional y, si no hay contraseña, la cuenta queda sin acceso propio."""

    nombre: str = Field(min_length=1, max_length=100)
    apellido_paterno: str = Field(min_length=1, max_length=100)
    apellido_materno: str = Field(min_length=1, max_length=100)
    telefono: str = Field(pattern=TELEFONO_PATTERN)
    correo: Optional[EmailStr] = None
    password: Optional[str] = Field(default=None, min_length=PASSWORD_MIN_LENGTH)
    fecha_nacimiento: Optional[date] = None
    genero_id: Optional[int] = None
    rol_id: int = ROL_CIUDADANO
    albergue_id: Optional[int] = None


class UsuarioUpdate(BaseModel):
    """Para PUT /usuarios/me — nunca incluye rol_id/albergue_id: un ciudadano
    no puede autoasignarse un rol distinto editando su propio perfil."""

    nombre: Optional[str] = Field(default=None, min_length=1, max_length=100)
    apellido_paterno: Optional[str] = Field(default=None, min_length=1, max_length=100)
    apellido_materno: Optional[str] = Field(default=None, min_length=1, max_length=100)
    correo: Optional[EmailStr] = None
    telefono: Optional[str] = Field(default=None, pattern=TELEFONO_PATTERN)
    fecha_nacimiento: Optional[date] = None
    genero_id: Optional[int] = None


class UsuarioUpdateAdmin(UsuarioUpdate):
    """Para PUT /usuarios/{id} — solo Admin, aquí sí se permite reasignar rol/albergue,
    y restablecer la contraseña de otro usuario (ej. el usuario la olvidó)."""

    rol_id: Optional[int] = None
    albergue_id: Optional[int] = None
    password: Optional[str] = Field(default=None, min_length=PASSWORD_MIN_LENGTH)
    vetado: Optional[bool] = None
    motivo_veto: Optional[str] = Field(default=None, max_length=500)


class CambiarPassword(BaseModel):
    password_actual: str
    password_nueva: str = Field(min_length=PASSWORD_MIN_LENGTH)


# ======================================================================
# "Mi perfil" — lo que consume la app móvil (usuario autenticado sobre sí mismo)
# ======================================================================

@router.get("/me", response_model=UsuarioResponse)
def obtener_mi_perfil(current_user: Usuario = Depends(get_current_user)):
    return current_user


@router.put("/me", response_model=UsuarioResponse)
def actualizar_mi_perfil(
    datos: UsuarioUpdate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    if datos.correo and datos.correo != current_user.correo:
        existe = db.query(Usuario).filter(Usuario.correo == datos.correo).first()
        if existe:
            raise HTTPException(status_code=400, detail="Este correo ya se encuentra registrado.")

    for campo, valor in datos.model_dump(exclude_unset=True).items():
        setattr(current_user, campo, valor)

    db.commit()
    db.refresh(current_user)
    return current_user


@router.put("/me/password", status_code=status.HTTP_204_NO_CONTENT)
def actualizar_mi_password(
    datos: CambiarPassword,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    if not current_user.password_hash or not verificar_password(
        datos.password_actual, current_user.password_hash
    ):
        raise HTTPException(status_code=400, detail="La contraseña actual no es correcta.")

    current_user.password_hash = obtener_password_hash(datos.password_nueva)
    db.commit()
    return None


@router.put("/me/direccion", response_model=DireccionResponse)
def actualizar_mi_direccion(
    datos: DireccionInput,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """Crea la dirección la primera vez (no se pide en el registro) o la actualiza si ya existe."""
    if not db.query(Estado).filter(Estado.id == datos.estado_id).first():
        raise HTTPException(status_code=404, detail="El estado indicado no existe.")

    if current_user.direccion_id:
        direccion = db.query(Direccion).filter(Direccion.id == current_user.direccion_id).first()
        for campo, valor in datos.model_dump().items():
            setattr(direccion, campo, valor)
    else:
        direccion = Direccion(**datos.model_dump())
        db.add(direccion)
        db.flush()  # asigna direccion.id sin cerrar la transacción
        current_user.direccion_id = direccion.id

    db.commit()
    db.refresh(direccion)
    return direccion


@router.post("/me/foto", response_model=UsuarioResponse)
def subir_mi_foto(
    archivo: UploadFile = File(...),
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    if archivo.content_type not in EXTENSIONES_PERMITIDAS:
        raise HTTPException(
            status_code=400, detail="Formato no soportado. Usa JPEG, PNG o WEBP."
        )

    os.makedirs(UPLOAD_DIR, exist_ok=True)
    extension = archivo.filename.rsplit(".", 1)[-1].lower() if "." in archivo.filename else "jpg"
    nombre_archivo = f"{current_user.id}_{uuid.uuid4().hex}.{extension}"
    ruta_disco = os.path.join(UPLOAD_DIR, nombre_archivo)

    with open(ruta_disco, "wb") as destino:
        destino.write(archivo.file.read())

    current_user.foto_url = f"/uploads/usuarios/{nombre_archivo}"
    db.commit()
    db.refresh(current_user)
    return current_user


# ======================================================================
# CRUD de staff (Recepcionista da de alta ciudadanos; Admin gestiona todo)
# ======================================================================

@router.post("/", response_model=UsuarioResponse, status_code=status.HTTP_201_CREATED)
def crear_usuario(
    usuario_in: UsuarioCreateStaff,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    if usuario_in.correo:
        existe = db.query(Usuario).filter(Usuario.correo == usuario_in.correo).first()
        if existe:
            raise HTTPException(status_code=400, detail="Este correo ya se encuentra registrado.")

    if usuario_in.genero_id is not None and not db.query(Genero).filter(Genero.id == usuario_in.genero_id).first():
        raise HTTPException(status_code=404, detail="El género indicado no existe.")
    if usuario_in.rol_id is not None and not db.query(Rol).filter(Rol.id == usuario_in.rol_id).first():
        raise HTTPException(status_code=404, detail="El rol indicado no existe.")
    if usuario_in.albergue_id is not None and not db.query(Albergue).filter(Albergue.id == usuario_in.albergue_id).first():
        raise HTTPException(status_code=404, detail="El albergue indicado no existe.")

    # Solo Admin puede asignar un rol distinto de Ciudadano
    rol_asignar = usuario_in.rol_id if current_user.rol_id == ROL_ADMIN else ROL_CIUDADANO

    nuevo_usuario = Usuario(
        nombre=usuario_in.nombre,
        apellido_paterno=usuario_in.apellido_paterno,
        apellido_materno=usuario_in.apellido_materno,
        correo=usuario_in.correo,
        password_hash=obtener_password_hash(usuario_in.password) if usuario_in.password else None,
        fecha_nacimiento=usuario_in.fecha_nacimiento,
        telefono=usuario_in.telefono,
        genero_id=usuario_in.genero_id,
        rol_id=rol_asignar,
        albergue_id=usuario_in.albergue_id if current_user.rol_id == ROL_ADMIN else None,
    )

    db.add(nuevo_usuario)
    db.commit()
    db.refresh(nuevo_usuario)
    return nuevo_usuario


def _anonimizar_y_eliminar_usuario(db: Session, usuario: Usuario) -> None:
    """Desvincula (usuario_id=NULL) el historial de donaciones/inscripciones antes de
    borrar la fila — así se pierde la identidad pero no el registro histórico."""
    db.query(Donacion).filter(Donacion.usuario_id == usuario.id).update({"usuario_id": None})
    db.query(InscripcionVoluntariado).filter(InscripcionVoluntariado.usuario_id == usuario.id).update(
        {"usuario_id": None}
    )
    db.delete(usuario)


def purgar_usuarios_vencidos(db: Session) -> None:
    """Elimina en definitiva a quien lleva más de 30 días desactivado. Se ejecuta de
    forma perezosa (al listar) en vez de con un cron aparte — no hay tráfico suficiente
    para que la demora importe, y evita añadir un scheduler solo para esto."""
    limite = datetime.now(timezone.utc) - timedelta(days=DIAS_GRACIA_ELIMINACION)
    vencidos = (
        db.query(Usuario)
        .filter(Usuario.activo.is_(False), Usuario.fecha_desactivacion.isnot(None), Usuario.fecha_desactivacion < limite)
        .all()
    )
    for usuario in vencidos:
        _anonimizar_y_eliminar_usuario(db, usuario)
    if vencidos:
        db.commit()


@router.get("/", response_model=List[UsuarioResponse])
def listar_usuarios(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    purgar_usuarios_vencidos(db)
    return db.query(Usuario).offset(skip).limit(limit).all()


@router.get("/{id}", response_model=UsuarioResponse)
def obtener_usuario(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    usuario = db.query(Usuario).filter(Usuario.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return usuario


@router.put("/{id}", response_model=UsuarioResponse)
def actualizar_usuario(
    id: int,
    usuario_in: UsuarioUpdateAdmin,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """Admin puede editar a cualquiera y todos los campos. Recepcionista solo puede
    editar Ciudadanos (verificación de datos en mostrador — RF de 'CRU de usuarios'),
    y nunca rol/albergue/veto/contraseña, aunque los mande en el body: se ignoran
    silenciosamente en vez de rechazar la petición, igual que ya se hace en
    crear_usuario con rol_asignar."""
    usuario = db.query(Usuario).filter(Usuario.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    cambios = usuario_in.model_dump(exclude_unset=True)

    if current_user.rol_id != ROL_ADMIN:
        if usuario.rol_id != ROL_CIUDADANO:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="Solo puedes editar cuentas de Ciudadano.",
            )
        for campo_restringido in ("rol_id", "albergue_id", "vetado", "motivo_veto", "password"):
            cambios.pop(campo_restringido, None)

    if "correo" in cambios and cambios["correo"] != usuario.correo:
        existe = db.query(Usuario).filter(Usuario.correo == cambios["correo"]).first()
        if existe:
            raise HTTPException(status_code=400, detail="Este correo ya se encuentra registrado.")
    if "genero_id" in cambios and cambios["genero_id"] is not None:
        if not db.query(Genero).filter(Genero.id == cambios["genero_id"]).first():
            raise HTTPException(status_code=404, detail="El género indicado no existe.")
    if "rol_id" in cambios and cambios["rol_id"] is not None:
        if not db.query(Rol).filter(Rol.id == cambios["rol_id"]).first():
            raise HTTPException(status_code=404, detail="El rol indicado no existe.")
    if "albergue_id" in cambios and cambios["albergue_id"] is not None:
        if not db.query(Albergue).filter(Albergue.id == cambios["albergue_id"]).first():
            raise HTTPException(status_code=404, detail="El albergue indicado no existe.")

    # "password" no es una columna real (el modelo guarda password_hash) — se
    # gestiona aparte para no perder silenciosamente el cambio ni guardarla en claro.
    password_nueva = cambios.pop("password", None)
    if password_nueva:
        usuario.password_hash = obtener_password_hash(password_nueva)

    for campo, valor in cambios.items():
        setattr(usuario, campo, valor)

    db.commit()
    db.refresh(usuario)
    return usuario


@router.delete("/{id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_usuario(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    """Borrado lógico (activo=False), no DELETE físico — conserva el historial de
    donaciones/voluntariados que referencian a este usuario. Queda 30 días con
    posibilidad de reactivarse (POST /{id}/reactivar) antes de purgarse en definitiva."""
    usuario = db.query(Usuario).filter(Usuario.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    usuario.activo = False
    usuario.fecha_desactivacion = datetime.now(timezone.utc)
    db.commit()
    return None


@router.post("/{id}/reactivar", response_model=UsuarioResponse)
def reactivar_usuario(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    """Revierte una baja mientras siga dentro de los 30 días de gracia."""
    usuario = db.query(Usuario).filter(Usuario.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    usuario.activo = True
    usuario.fecha_desactivacion = None
    db.commit()
    db.refresh(usuario)
    return usuario


@router.delete("/{id}/permanente", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_usuario_permanente(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    """Borrado físico inmediato, sin esperar los 30 días de gracia. Requiere que el
    usuario ya esté desactivado — evita que se elimine por accidente a alguien activo
    sin pasar primero por la confirmación de baja."""
    usuario = db.query(Usuario).filter(Usuario.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    if usuario.activo:
        raise HTTPException(
            status_code=400, detail="Debes desactivar al usuario antes de poder eliminarlo permanentemente."
        )

    _anonimizar_y_eliminar_usuario(db, usuario)
    db.commit()
    return None
