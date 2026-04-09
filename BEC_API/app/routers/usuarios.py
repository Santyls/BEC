from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from pydantic import BaseModel, EmailStr
from typing import List, Optional
from app.data.database import get_db
from app.models.models import Usuario, Rol, Genero, Direccion, Albergue
from app.security.security import get_current_user, get_admin_user, get_recepcionista_or_admin, obtener_password_hash

router = APIRouter(prefix="/usuarios", tags=["Usuarios"])

# --- Pydantic Schemas V2 ---
class UsuarioCreate(BaseModel):
    Nombre: str
    Apellido_P: str
    Apellido_M: str
    Correo: EmailStr
    Password: str
    Edad: int
    Id_Direccion: int
    Id_Albergue: Optional[int] = None
    Id_Rol: Optional[int] = 3   # 3 = Ciudadano por defecto (registro público)
    Tel: int
    Id_Genero: int

class UsuarioUpdate(BaseModel):
    Nombre: Optional[str] = None
    Apellido_P: Optional[str] = None
    Apellido_M: Optional[str] = None
    Correo: Optional[EmailStr] = None
    Password: Optional[str] = None
    Edad: Optional[int] = None
    Id_Direccion: Optional[int] = None
    Id_Albergue: Optional[int] = None
    Id_Rol: Optional[int] = None
    Tel: Optional[int] = None
    Id_Genero: Optional[int] = None

class UsuarioResponse(BaseModel):
    id_Usuario: int
    Nombre: str
    Apellido_P: str
    Apellido_M: str
    Correo: str
    Edad: int
    Id_Direccion: int
    Id_Albergue: Optional[int] = None
    Id_Rol: int
    Tel: int
    Id_Genero: int

    class Config:
        from_attributes = True

# --- Funciones auxiliares de validación FK ---
def validar_foraneas_usuario(db: Session, Id_Rol, Id_Genero, Id_Direccion, Id_Albergue=None):
    """Valida que las llaves foráneas existan antes de insertar/actualizar."""
    rol_existe = db.query(Rol).filter(Rol.Id_Rol == Id_Rol).first()
    if not rol_existe:
        raise HTTPException(status_code=400, detail="El Id_Rol especificado no existe")

    genero_existe = db.query(Genero).filter(Genero.Id_Genero == Id_Genero).first()
    if not genero_existe:
        raise HTTPException(status_code=400, detail="El Id_Genero especificado no existe")

    direccion_existe = db.query(Direccion).filter(Direccion.Id_Direccion == Id_Direccion).first()
    if not direccion_existe:
        raise HTTPException(status_code=400, detail="El Id_Direccion especificado no existe")

    if Id_Albergue is not None:
        albergue_existe = db.query(Albergue).filter(Albergue.Id_Albergue == Id_Albergue).first()
        if not albergue_existe:
            raise HTTPException(status_code=400, detail="El Id_Albergue especificado no existe")

# --- Schema para auto-edición de perfil (campos que el propio usuario puede cambiar) ---
class PerfilUpdate(BaseModel):
    Nombre:       Optional[str] = None
    Apellido_P:   Optional[str] = None
    Apellido_M:   Optional[str] = None
    Edad:         Optional[int] = None
    Tel:          Optional[int] = None
    Id_Genero:    Optional[int] = None
    Id_Direccion: Optional[int] = None
    # Correo y Rol explícitamente excluidos — no editables por el ciudadano

# --- Rutas CRUD ---

# /me DEBE ir antes de /{id} para que FastAPI no intente convertir "me" a int
@router.get("/me", tags=["Mi Perfil"])
def obtener_mi_perfil(
    current_user: Usuario = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Devuelve los datos del usuario autenticado, incluyendo su dirección si existe."""
    direccion = None
    if current_user.Id_Direccion:
        direccion = db.query(Direccion).filter(
            Direccion.Id_Direccion == current_user.Id_Direccion
        ).first()

    return {
        "id_Usuario":   current_user.id_Usuario,
        "Nombre":       current_user.Nombre,
        "Apellido_P":   current_user.Apellido_P,
        "Apellido_M":   current_user.Apellido_M,
        "Correo":       current_user.Correo,
        "Edad":         current_user.Edad,
        "Tel":          current_user.Tel,
        "Id_Genero":    current_user.Id_Genero,
        "Id_Rol":       current_user.Id_Rol,
        "Id_Albergue":  current_user.Id_Albergue,
        "Id_Direccion": current_user.Id_Direccion,
        "direccion": {
            "Id_Direccion": direccion.Id_Direccion,
            "Id_Colonia":   direccion.Id_Colonia,
            "Calle":        direccion.Calle,
            "No_exterior":  direccion.No_exterior,
        } if direccion else None,
    }

@router.patch("/me", tags=["Mi Perfil"])
def actualizar_mi_perfil(
    perfil_in: PerfilUpdate,
    current_user: Usuario = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Permite al usuario autenticado actualizar sus propios datos personales.
    Correo y Rol NO son modificables desde este endpoint."""
    update_data = perfil_in.model_dump(exclude_unset=True)

    if "Id_Genero" in update_data:
        from app.models.models import Genero as GeneroModel
        genero_existe = db.query(GeneroModel).filter(
            GeneroModel.Id_Genero == update_data["Id_Genero"]
        ).first()
        if not genero_existe:
            raise HTTPException(status_code=400, detail="El Id_Genero especificado no existe")

    if "Id_Direccion" in update_data and update_data["Id_Direccion"] is not None:
        from app.models.models import Direccion as DireccionModel
        direccion_existe = db.query(DireccionModel).filter(
            DireccionModel.Id_Direccion == update_data["Id_Direccion"]
        ).first()
        if not direccion_existe:
            raise HTTPException(status_code=400, detail="El Id_Direccion especificado no existe")

    for key, value in update_data.items():
        setattr(current_user, key, value)

    db.commit()
    db.refresh(current_user)
    return {
        "mensaje": "Perfil actualizado correctamente",
        "status": 200,
        "data": {
            "id_Usuario": current_user.id_Usuario,
            "Nombre":     current_user.Nombre,
            "Apellido_P": current_user.Apellido_P,
            "Apellido_M": current_user.Apellido_M,
            "Correo":     current_user.Correo,
            "Edad":       current_user.Edad,
            "Tel":        current_user.Tel,
            "Id_Genero":  current_user.Id_Genero,
        }
    }

@router.post("/", status_code=status.HTTP_201_CREATED)
def crear_usuario(
    usuario_in: UsuarioCreate,
    db: Session = Depends(get_db)
    # Sin autenticación: endpoint público para auto-registro de ciudadanos
):
    """Registro público de nuevos ciudadanos. El rol SIEMPRE se fuerza a 3 (Ciudadano)."""
    # Verificamos si el correo ya existe
    existe = db.query(Usuario).filter(Usuario.Correo == usuario_in.Correo).first()
    if existe:
        raise HTTPException(
            status_code=400,
            detail="Este correo ya se encuentra registrado. Intenta iniciar sesión."
        )

    # SEGURIDAD: forzar siempre rol Ciudadano (Id_Rol=3) — nadie puede auto-asignarse Admin
    rol_asignar = 3

    # Verificación de Foráneas
    validar_foraneas_usuario(db, rol_asignar, usuario_in.Id_Genero, usuario_in.Id_Direccion, usuario_in.Id_Albergue)

    nuevo_usuario = Usuario(
        Nombre=usuario_in.Nombre,
        Apellido_P=usuario_in.Apellido_P,
        Apellido_M=usuario_in.Apellido_M,
        Correo=usuario_in.Correo,
        Password=obtener_password_hash(usuario_in.Password),
        Edad=usuario_in.Edad,
        Id_Direccion=usuario_in.Id_Direccion,
        Id_Albergue=None,         # Ciudadanos nuevos nunca tienen albergue
        Id_Rol=rol_asignar,
        Tel=usuario_in.Tel,
        Id_Genero=usuario_in.Id_Genero
    )

    db.add(nuevo_usuario)
    db.commit()
    db.refresh(nuevo_usuario)
    return {
        "mensaje": "Usuario registrado correctamente",
        "status": 201,
        "data": nuevo_usuario
    }

@router.get("/", response_model=List[UsuarioResponse])
def listar_usuarios(
    skip: int = 0, limit: int = 100, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    return db.query(Usuario).offset(skip).limit(limit).all()

@router.get("/{id}", response_model=UsuarioResponse)
def obtener_usuario(
    id: int, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    usuario = db.query(Usuario).filter(Usuario.id_Usuario == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return usuario

@router.put("/{id}")
def actualizar_usuario(
    id: int, 
    usuario_in: UsuarioCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    usuario = db.query(Usuario).filter(Usuario.id_Usuario == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    # Validacion: Solo admin puede cambiar el rol
    rol_asignar = usuario_in.Id_Rol if current_user.Id_Rol == 1 else usuario.Id_Rol

    # Verificacion de Foraneas (Regla del Maestro)
    validar_foraneas_usuario(db, rol_asignar, usuario_in.Id_Genero, usuario_in.Id_Direccion, usuario_in.Id_Albergue)

    usuario.Nombre = usuario_in.Nombre
    usuario.Apellido_P = usuario_in.Apellido_P
    usuario.Apellido_M = usuario_in.Apellido_M
    usuario.Correo = usuario_in.Correo
    usuario.Password = obtener_password_hash(usuario_in.Password)
    usuario.Edad = usuario_in.Edad
    usuario.Id_Direccion = usuario_in.Id_Direccion
    usuario.Id_Albergue = usuario_in.Id_Albergue
    usuario.Id_Rol = rol_asignar
    usuario.Tel = usuario_in.Tel
    usuario.Id_Genero = usuario_in.Id_Genero

    db.commit()
    db.refresh(usuario)
    return {
        "mensaje": "Usuario editado correctamente",
        "status": 200,
        "data": usuario
    }

@router.patch("/{id}")
def modificar_usuario(
    id: int, 
    usuario_in: UsuarioUpdate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    usuario = db.query(Usuario).filter(Usuario.id_Usuario == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    update_data = usuario_in.model_dump(exclude_unset=True)

    if "Id_Rol" in update_data:
        # Solo admin puede cambiar rol
        if current_user.Id_Rol != 1:
            del update_data["Id_Rol"]
        else:
            rol_existe = db.query(Rol).filter(Rol.Id_Rol == update_data["Id_Rol"]).first()
            if not rol_existe:
                raise HTTPException(status_code=400, detail="El Id_Rol especificado no existe")

    if "Id_Genero" in update_data:
        genero_existe = db.query(Genero).filter(Genero.Id_Genero == update_data["Id_Genero"]).first()
        if not genero_existe:
            raise HTTPException(status_code=400, detail="El Id_Genero especificado no existe")

    if "Id_Direccion" in update_data:
        direccion_existe = db.query(Direccion).filter(Direccion.Id_Direccion == update_data["Id_Direccion"]).first()
        if not direccion_existe:
            raise HTTPException(status_code=400, detail="El Id_Direccion especificado no existe")

    if "Id_Albergue" in update_data and update_data["Id_Albergue"] is not None:
        albergue_existe = db.query(Albergue).filter(Albergue.Id_Albergue == update_data["Id_Albergue"]).first()
        if not albergue_existe:
            raise HTTPException(status_code=400, detail="El Id_Albergue especificado no existe")

    if "Password" in update_data:
        update_data["Password"] = obtener_password_hash(update_data["Password"])

    for key, value in update_data.items():
        setattr(usuario, key, value)

    db.commit()
    db.refresh(usuario)
    return {
        "mensaje": "Usuario editado correctamente",
        "status": 200,
        "data": usuario
    }

@router.delete("/{id}")
def eliminar_usuario(
    id: int, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user)
):
    usuario = db.query(Usuario).filter(Usuario.id_Usuario == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    
    db.delete(usuario)
    db.commit()
    return {
        "mensaje": f"Usuario eliminado por {current_user.Nombre}",
        "status": 200
    }
