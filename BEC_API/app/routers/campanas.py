from datetime import date
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Campana, Usuario
from app.security.security import get_admin_user, get_current_user

router = APIRouter(prefix="/campanas", tags=["Campañas"])


class CampanaCreate(BaseModel):
    nombre: str
    fecha_inicio: date
    fecha_fin: date
    estado_id: int
    descripcion_objetivos: str


class CampanaUpdate(BaseModel):
    nombre: Optional[str] = None
    fecha_inicio: Optional[date] = None
    fecha_fin: Optional[date] = None
    estado_id: Optional[int] = None
    descripcion_objetivos: Optional[str] = None


class CampanaResponse(BaseModel):
    id: int
    nombre: str
    fecha_inicio: date
    fecha_fin: date
    estado_id: int
    descripcion_objetivos: str

    class Config:
        from_attributes = True


@router.get("/", response_model=List[CampanaResponse])
def listar_campanas(
    db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    return db.query(Campana).order_by(Campana.fecha_inicio.desc()).all()


@router.get("/{id}", response_model=CampanaResponse)
def obtener_campana(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    campana = db.query(Campana).filter(Campana.id == id).first()
    if not campana:
        raise HTTPException(status_code=404, detail="Campaña no encontrada")
    return campana


@router.post("/", response_model=CampanaResponse, status_code=status.HTTP_201_CREATED)
def crear_campana(
    datos: CampanaCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    if datos.fecha_fin < datos.fecha_inicio:
        raise HTTPException(status_code=400, detail="La fecha de fin no puede ser anterior a la de inicio.")

    campana = Campana(**datos.model_dump())
    db.add(campana)
    db.commit()
    db.refresh(campana)
    return campana


@router.put("/{id}", response_model=CampanaResponse)
def actualizar_campana(
    id: int,
    datos: CampanaUpdate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    campana = db.query(Campana).filter(Campana.id == id).first()
    if not campana:
        raise HTTPException(status_code=404, detail="Campaña no encontrada")

    for campo, valor in datos.model_dump(exclude_unset=True).items():
        setattr(campana, campo, valor)

    db.commit()
    db.refresh(campana)
    return campana


@router.delete("/{id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_campana(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)
):
    """Marca la campaña como Finalizada (id=3) en vez de borrarla — conserva el historial
    de los voluntariados que la referencian."""
    campana = db.query(Campana).filter(Campana.id == id).first()
    if not campana:
        raise HTTPException(status_code=404, detail="Campaña no encontrada")

    campana.estado_id = 3  # Finalizada
    db.commit()
    return None
