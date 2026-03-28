from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from pydantic import BaseModel
from typing import List, Optional
from app.data.database import get_db
from app.models.models import Albergue, Usuario, Direccion
from app.security.security import get_current_user, get_admin_user

router = APIRouter()

# --- Schemas ---

class AlbergueCreate(BaseModel):
    Nombre_Albergue: str
    Capacidad_Max: int
    Tel_Contacto: int
    Id_Direccion: int

class AlbergueUpdate(BaseModel):
    Nombre_Albergue: Optional[str] = None
    Capacidad_Max: Optional[int] = None
    Tel_Contacto: Optional[int] = None
    Id_Direccion: Optional[int] = None

class AlbergueResponse(BaseModel):
    Id_Albergue: int
    Nombre_Albergue: str
    Capacidad_Max: int
    Tel_Contacto: int
    Id_Direccion: int

    class Config:
        from_attributes = True

# --- Endpoints ---

@router.post("/", status_code=status.HTTP_201_CREATED)
def crear_albergue(
    albergue_in: AlbergueCreate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    # Verificacion de Foraneas (Regla del Maestro)
    direccion_existe = db.query(Direccion).filter(Direccion.Id_Direccion == albergue_in.Id_Direccion).first()
    if not direccion_existe:
        raise HTTPException(status_code=400, detail="La Direccion especificada no existe")

    nuevo_albergue = Albergue(
        Nombre_Albergue=albergue_in.Nombre_Albergue,
        Capacidad_Max=albergue_in.Capacidad_Max,
        Tel_Contacto=albergue_in.Tel_Contacto,
        Id_Direccion=albergue_in.Id_Direccion
    )
    db.add(nuevo_albergue)
    db.commit()
    db.refresh(nuevo_albergue)
    
    return {
        "mensaje": "Albergue agregado correctamente", 
        "status": 201, 
        "data": nuevo_albergue
    }

@router.get("/")
def listar_albergues(
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_current_user)
):
    albergues = db.query(Albergue).all()
    return albergues

@router.get("/{id}")
def obtener_albergue(
    id: int, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_current_user)
):
    albergue = db.query(Albergue).filter(Albergue.Id_Albergue == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")
    return albergue

@router.put("/{id}")
def actualizar_albergue_completo(
    id: int, 
    albergue_in: AlbergueCreate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    # Verificacion de Foraneas
    direccion_existe = db.query(Direccion).filter(Direccion.Id_Direccion == albergue_in.Id_Direccion).first()
    if not direccion_existe:
        raise HTTPException(status_code=400, detail="La Direccion especificada no existe")

    albergue = db.query(Albergue).filter(Albergue.Id_Albergue == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")
    
    albergue.Nombre_Albergue = albergue_in.Nombre_Albergue
    albergue.Capacidad_Max = albergue_in.Capacidad_Max
    albergue.Tel_Contacto = albergue_in.Tel_Contacto
    albergue.Id_Direccion = albergue_in.Id_Direccion
    
    db.commit()
    db.refresh(albergue)
    return {
        "mensaje": "Albergue editado correctamente", 
        "status": 200, 
        "data": albergue
    }

@router.patch("/{id}")
def modificar_albergue(
    id: int, 
    albergue_in: AlbergueUpdate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    albergue = db.query(Albergue).filter(Albergue.Id_Albergue == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")
    
    update_data = albergue_in.model_dump(exclude_unset=True)
    
    # Validar FK si se envió en el PATCH
    if "Id_Direccion" in update_data:
        direccion_existe = db.query(Direccion).filter(Direccion.Id_Direccion == update_data["Id_Direccion"]).first()
        if not direccion_existe:
            raise HTTPException(status_code=400, detail="La Direccion especificada no existe")

    for key, value in update_data.items():
        setattr(albergue, key, value)
        
    db.commit()
    db.refresh(albergue)
    return {
        "mensaje": "Albergue editado correctamente", 
        "status": 200, 
        "data": albergue
    }

@router.delete("/{id}")
def eliminar_albergue(
    id: int, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    albergue = db.query(Albergue).filter(Albergue.Id_Albergue == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")
    
    db.delete(albergue)
    db.commit()
    return {
        "mensaje": f"Albergue eliminado por {current_user.Nombre}", 
        "status": 200
    }
