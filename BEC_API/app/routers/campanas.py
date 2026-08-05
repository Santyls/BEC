from datetime import date
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel, Field
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Campana, EstadoCampana, Usuario
from app.security.security import get_admin_user, get_current_user

router = APIRouter(prefix="/campanas", tags=["Campañas"])

ESTADO_PROGRAMADA = 1


class CampanaCreate(BaseModel):
    nombre: str = Field(min_length=3, max_length=150)
    fecha_inicio: date
    fecha_fin: date
    descripcion_objetivos: str = Field(min_length=1)
    # estado_id NO es parte de este schema a propósito: toda campaña nace
    # "Programada" — no tendría sentido crear una ya Activa/Finalizada.


class CampanaUpdate(BaseModel):
    nombre: Optional[str] = Field(default=None, min_length=3, max_length=150)
    fecha_inicio: Optional[date] = None
    fecha_fin: Optional[date] = None
    estado_id: Optional[int] = None
    descripcion_objetivos: Optional[str] = Field(default=None, min_length=1)


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

    campana = Campana(**datos.model_dump(), estado_id=ESTADO_PROGRAMADA)
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

    cambios = datos.model_dump(exclude_unset=True)

    fecha_inicio = cambios.get("fecha_inicio", campana.fecha_inicio)
    fecha_fin = cambios.get("fecha_fin", campana.fecha_fin)
    if fecha_fin < fecha_inicio:
        raise HTTPException(status_code=400, detail="La fecha de fin no puede ser anterior a la de inicio.")

    if "estado_id" in cambios:
        if not db.query(EstadoCampana).filter(EstadoCampana.id == cambios["estado_id"]).first():
            raise HTTPException(status_code=404, detail="El estado indicado no existe.")

    for campo, valor in cambios.items():
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
