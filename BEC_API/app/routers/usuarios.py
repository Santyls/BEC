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
    Id_Rol: int
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

# --- Rutas CRUD ---

@router.post("/", status_code=status.HTTP_201_CREATED)
def crear_usuario(
    usuario_in: UsuarioCreate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    # Verificamos si el correo ya existe
    existe = db.query(Usuario).filter(Usuario.Correo == usuario_in.Correo).first()
    if existe:
        raise HTTPException(status_code=400, detail="Este correo ya se encuentra registrado.")

    # Regla: Si es Recepcionista, forzamos Id_Rol = 3 (Ciudadano), solo Admin puede asignar otro rol
    rol_asignar = usuario_in.Id_Rol if current_user.Id_Rol == 1 else 3

    # Verificacion de Foraneas (Regla del Maestro)
    validar_foraneas_usuario(db, rol_asignar, usuario_in.Id_Genero, usuario_in.Id_Direccion, usuario_in.Id_Albergue)

    nuevo_usuario = Usuario(
        Nombre=usuario_in.Nombre,
        Apellido_P=usuario_in.Apellido_P,
        Apellido_M=usuario_in.Apellido_M,
        Correo=usuario_in.Correo,
        Password=obtener_password_hash(usuario_in.Password),
        Edad=usuario_in.Edad,
        Id_Direccion=usuario_in.Id_Direccion,
        Id_Albergue=usuario_in.Id_Albergue,
        Id_Rol=rol_asignar,
        Tel=usuario_in.Tel,
        Id_Genero=usuario_in.Id_Genero
    )
    
    db.add(nuevo_usuario)
    db.commit()
    db.refresh(nuevo_usuario)
    return {
        "mensaje": "Usuario agregado correctamente",
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

    # Validar FKs solo si se enviaron en el PATCH
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

    # Si se envió Password nuevo, hashear
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
