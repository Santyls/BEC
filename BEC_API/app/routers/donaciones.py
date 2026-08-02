from datetime import date
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, Query, status
from pydantic import BaseModel
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Donacion, Usuario
from app.security.security import get_current_user, get_recepcionista_or_admin

router = APIRouter(prefix="/donaciones", tags=["Donaciones"])


class DonacionCreate(BaseModel):
    usuario_id: Optional[int] = None  # None = donación anónima (BEC_PAL lo permite explícitamente)
    categoria_id: int
    condicion_id: int
    cantidad: float
    unidad_id: int
    marca: Optional[str] = None
    albergue_id: int


class AlbergueResumen(BaseModel):
    id: int
    nombre: str

    class Config:
        from_attributes = True


class DonacionResponse(BaseModel):
    id: int
    usuario_id: Optional[int]
    categoria_id: int
    condicion_id: int
    cantidad: float
    unidad_id: int
    marca: Optional[str]
    albergue_id: int
    fecha_donacion: date
    albergue: AlbergueResumen

    class Config:
        from_attributes = True


@router.get("/me", response_model=List[DonacionResponse])
def mis_donaciones(
    categoria_id: Optional[int] = Query(default=None),
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """Historial que consume el móvil en Mis Donaciones, con filtro opcional por categoría."""
    query = db.query(Donacion).filter(Donacion.usuario_id == current_user.id)
    if categoria_id is not None:
        query = query.filter(Donacion.categoria_id == categoria_id)
    return query.order_by(Donacion.fecha_donacion.desc()).all()


@router.get("/", response_model=List[DonacionResponse])
def listar_donaciones(
    categoria_id: Optional[int] = Query(default=None),
    albergue_id: Optional[int] = Query(default=None),
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """Inventario global (BEC_PAL) con filtros."""
    query = db.query(Donacion)
    if categoria_id is not None:
        query = query.filter(Donacion.categoria_id == categoria_id)
    if albergue_id is not None:
        query = query.filter(Donacion.albergue_id == albergue_id)
    return query.order_by(Donacion.fecha_donacion.desc()).all()


@router.get("/{id}", response_model=DonacionResponse)
def obtener_donacion(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    donacion = db.query(Donacion).filter(Donacion.id == id).first()
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")

    # Un ciudadano solo puede ver el detalle de sus propias donaciones
    from app.security.security import ROL_CIUDADANO

    if current_user.rol_id == ROL_CIUDADANO and donacion.usuario_id != current_user.id:
        raise HTTPException(status_code=403, detail="No puedes ver esta donación.")
    return donacion


@router.post("/", response_model=DonacionResponse, status_code=status.HTTP_201_CREATED)
def registrar_donacion(
    datos: DonacionCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """La registra el Recepcionista en el mostrador (BEC_PRF) o el Admin — el ciudadano
    únicamente consulta su historial, no crea donaciones desde el móvil (RF05 original)."""
    if datos.usuario_id is not None:
        donante = db.query(Usuario).filter(Usuario.id == datos.usuario_id).first()
        if not donante:
            raise HTTPException(status_code=404, detail="El usuario donante no existe.")

    donacion = Donacion(**datos.model_dump())
    db.add(donacion)
    db.commit()
    db.refresh(donacion)
    return donacion
