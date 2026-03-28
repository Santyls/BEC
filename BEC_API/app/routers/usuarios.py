from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from pydantic import BaseModel, EmailStr
from typing import List, Optional
from app.data.database import get_db
from app.models.models import Usuario
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

class UsuarioResponse(BaseModel):
    id_Usuario: int
    Nombre: str
    Correo: str
    Id_Rol: int

    class Config:
        from_attributes = True

# --- Rutas CRUD ---

@router.post("/", response_model=UsuarioResponse, status_code=status.HTTP_201_CREATED)
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
    return nuevo_usuario

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

@router.put("/{id}", response_model=UsuarioResponse)
def actualizar_usuario(
    id: int, 
    usuario_in: UsuarioCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    usuario = db.query(Usuario).filter(Usuario.id_Usuario == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    usuario.Nombre = usuario_in.Nombre
    usuario.Apellido_P = usuario_in.Apellido_P
    usuario.Apellido_M = usuario_in.Apellido_M
    usuario.Correo = usuario_in.Correo
    usuario.Password = obtener_password_hash(usuario_in.Password)
    usuario.Edad = usuario_in.Edad
    usuario.Id_Direccion = usuario_in.Id_Direccion
    usuario.Id_Albergue = usuario_in.Id_Albergue
    # Validacion: Solo admin puede cambiar el rol
    usuario.Id_Rol = usuario_in.Id_Rol if current_user.Id_Rol == 1 else usuario.Id_Rol
    usuario.Tel = usuario_in.Tel
    usuario.Id_Genero = usuario_in.Id_Genero

    db.commit()
    db.refresh(usuario)
    return usuario

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
    return {"status": "success", "message": f"Usuario con ID {id} eliminado correctamente."}
