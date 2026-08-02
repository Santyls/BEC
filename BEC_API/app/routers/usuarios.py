import os
import uuid
from datetime import date
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, UploadFile, File, status
from pydantic import BaseModel, EmailStr
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Direccion, Usuario
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


# ======================================================================
# Schemas
# ======================================================================

class UsuarioResponse(BaseModel):
    id: int
    nombre: str
    apellido_paterno: str
    apellido_materno: str
    correo: Optional[EmailStr] = None
    telefono: str
    fecha_nacimiento: Optional[date] = None
    genero_id: Optional[int] = None
    rol_id: int
    albergue_id: Optional[int] = None
    foto_url: Optional[str] = None
    terminos_aceptados: bool
    activo: bool

    class Config:
        from_attributes = True


class UsuarioCreateStaff(BaseModel):
    """Usada por Recepcionista/Admin para dar de alta a alguien (mostrador o back-office).
    Recepcionista: solo nombre/apellidos/teléfono son obligatorios en la práctica —
    correo es opcional y, si no hay contraseña, la cuenta queda sin acceso propio."""

    nombre: str
    apellido_paterno: str
    apellido_materno: str
    telefono: str
    correo: Optional[EmailStr] = None
    password: Optional[str] = None
    fecha_nacimiento: Optional[date] = None
    genero_id: Optional[int] = None
    rol_id: int = ROL_CIUDADANO
    albergue_id: Optional[int] = None


class UsuarioUpdate(BaseModel):
    nombre: Optional[str] = None
    apellido_paterno: Optional[str] = None
    apellido_materno: Optional[str] = None
    correo: Optional[EmailStr] = None
    telefono: Optional[str] = None
    fecha_nacimiento: Optional[date] = None
    genero_id: Optional[int] = None


class DireccionInput(BaseModel):
    estado_id: int
    municipio: str
    colonia: str
    calle: str
    numero_exterior: str
    numero_interior: Optional[str] = None
    codigo_postal: str


class DireccionResponse(DireccionInput):
    id: int

    class Config:
        from_attributes = True


class CambiarPassword(BaseModel):
    password_actual: str
    password_nueva: str


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
    if len(datos.password_nueva) < 6:
        raise HTTPException(status_code=400, detail="La nueva contraseña debe tener al menos 6 caracteres.")

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


@router.get("/", response_model=List[UsuarioResponse])
def listar_usuarios(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
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
    usuario_in: UsuarioUpdate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    usuario = db.query(Usuario).filter(Usuario.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    for campo, valor in usuario_in.model_dump(exclude_unset=True).items():
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
    donaciones/voluntariados que referencian a este usuario."""
    usuario = db.query(Usuario).filter(Usuario.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    usuario.activo = False
    db.commit()
    return None
