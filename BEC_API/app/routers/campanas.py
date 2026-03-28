from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from pydantic import BaseModel
from typing import List, Optional
from datetime import date
from app.data.database import get_db
from app.models.models import Campana, Usuario, EstadoCampana
from app.security.security import get_current_user, get_admin_user

router = APIRouter()

# --- Schemas ---

class CampanaCreate(BaseModel):
    Fecha_Inicio: date
    Descripcion_Objetivos: str
    Fecha_Fin: date
    Nombre_Campana: str
    id_Estado_campana: int

class CampanaUpdate(BaseModel):
    Fecha_Inicio: Optional[date] = None
    Descripcion_Objetivos: Optional[str] = None
    Fecha_Fin: Optional[date] = None
    Nombre_Campana: Optional[str] = None
    id_Estado_campana: Optional[int] = None

class CampanaResponse(BaseModel):
    id_Campana: int
    Fecha_Inicio: date
    Descripcion_Objetivos: str
    Fecha_Fin: date
    Nombre_Campana: str
    id_Estado_campana: int

    class Config:
        from_attributes = True

# --- Endpoints ---

@router.post("/", status_code=status.HTTP_201_CREATED)
def crear_campana(
    campana_in: CampanaCreate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    # Validar FK
    estado_existe = db.query(EstadoCampana).filter(EstadoCampana.Id_Estado_Campana == campana_in.id_Estado_campana).first()
    if not estado_existe:
        raise HTTPException(status_code=400, detail="El Estado de Campaña especificado no existe")

    nueva_campana = Campana(
        Nombre_Campana=campana_in.Nombre_Campana,
        Fecha_Inicio=campana_in.Fecha_Inicio,
        Fecha_Fin=campana_in.Fecha_Fin,
        id_Estado_campana=campana_in.id_Estado_campana,
        Descripcion_Objetivos=campana_in.Descripcion_Objetivos
    )
    db.add(nueva_campana)
    db.commit()
    db.refresh(nueva_campana)
    
    return {
        "mensaje": "Campaña agregada correctamente", 
        "status": 201, 
        "data": nueva_campana
    }

@router.get("/")
def listar_campanas(
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_current_user)
):
    campanas = db.query(Campana).all()
    # Lógica Especial (Lazy Update)
    modificado = False
    for campana in campanas:
        if date.today() > campana.Fecha_Fin and campana.id_Estado_campana == 2:
            campana.id_Estado_campana = 3
            modificado = True
            
    if modificado:
        db.commit()
        for campana in campanas:
            db.refresh(campana)
            
    return campanas

@router.get("/{id}")
def obtener_campana(
    id: int, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_current_user)
):
    campana = db.query(Campana).filter(Campana.id_Campana == id).first()
    if not campana:
        raise HTTPException(status_code=404, detail="Campaña no encontrada")
    
    # Lógica Especial (Lazy Update)
    if date.today() > campana.Fecha_Fin and campana.id_Estado_campana == 2:
        campana.id_Estado_campana = 3
        db.commit()
        db.refresh(campana)

    return campana

@router.put("/{id}")
def actualizar_campana_completa(
    id: int, 
    campana_in: CampanaCreate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    # Verificacion de Foraneas
    estado_existe = db.query(EstadoCampana).filter(EstadoCampana.Id_Estado_Campana == campana_in.id_Estado_campana).first()
    if not estado_existe:
        raise HTTPException(status_code=400, detail="El Estado de Campaña especificado no existe")

    campana = db.query(Campana).filter(Campana.id_Campana == id).first()
    if not campana:
        raise HTTPException(status_code=404, detail="Campaña no encontrada")
    
    campana.Nombre_Campana = campana_in.Nombre_Campana
    campana.Fecha_Inicio = campana_in.Fecha_Inicio
    campana.Fecha_Fin = campana_in.Fecha_Fin
    campana.id_Estado_campana = campana_in.id_Estado_campana
    campana.Descripcion_Objetivos = campana_in.Descripcion_Objetivos
    
    db.commit()
    db.refresh(campana)
    return {
        "mensaje": "Campaña editada correctamente", 
        "status": 200, 
        "data": campana
    }

@router.patch("/{id}")
def modificar_campana(
    id: int, 
    campana_in: CampanaUpdate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    campana = db.query(Campana).filter(Campana.id_Campana == id).first()
    if not campana:
        raise HTTPException(status_code=404, detail="Campaña no encontrada")
    
    update_data = campana_in.model_dump(exclude_unset=True)
    
    # Validar FK si se envió en el PATCH
    if "id_Estado_campana" in update_data:
        estado_existe = db.query(EstadoCampana).filter(EstadoCampana.Id_Estado_Campana == update_data["id_Estado_campana"]).first()
        if not estado_existe:
            raise HTTPException(status_code=400, detail="El Estado de Campaña especificado no existe")

    for key, value in update_data.items():
        setattr(campana, key, value)
        
    db.commit()
    db.refresh(campana)
    return {
        "mensaje": "Campaña editada correctamente", 
        "status": 200, 
        "data": campana
    }

@router.delete("/{id}")
def eliminar_campana(
    id: int, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    campana = db.query(Campana).filter(Campana.id_Campana == id).first()
    if not campana:
        raise HTTPException(status_code=404, detail="Campaña no encontrada")
    
    db.delete(campana)
    db.commit()
    return {
        "mensaje": f"Campaña eliminada por {current_user.Nombre}", 
        "status": 200
    }
