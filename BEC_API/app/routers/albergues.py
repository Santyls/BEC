from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Albergue, Direccion, Usuario
from app.security.security import get_admin_user, get_current_user

router = APIRouter(prefix="/albergues", tags=["Albergues"])


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


class AlbergueCreate(BaseModel):
    nombre: str
    capacidad_max: int
    telefono: str
    direccion: DireccionInput


class AlbergueUpdate(BaseModel):
    nombre: Optional[str] = None
    capacidad_max: Optional[int] = None
    telefono: Optional[str] = None


class AlbergueResponse(BaseModel):
    id: int
    nombre: str
    capacidad_max: int
    telefono: str
    activo: bool
    direccion: DireccionResponse

    class Config:
        from_attributes = True


@router.get("/", response_model=List[AlbergueResponse])
def listar_albergues(
    solo_activos: bool = True,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """Cualquier usuario autenticado puede consultarlos (no es información sensible;
    el móvil los muestra en el detalle de donaciones/voluntariados)."""
    query = db.query(Albergue)
    if solo_activos:
        query = query.filter(Albergue.activo.is_(True))
    return query.order_by(Albergue.nombre).all()


@router.get("/{id}", response_model=AlbergueResponse)
def obtener_albergue(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    albergue = db.query(Albergue).filter(Albergue.id == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")
    return albergue


@router.post("/", response_model=AlbergueResponse, status_code=status.HTTP_201_CREATED)
def crear_albergue(
    datos: AlbergueCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    direccion = Direccion(**datos.direccion.model_dump())
    db.add(direccion)
    db.flush()

    albergue = Albergue(
        nombre=datos.nombre,
        capacidad_max=datos.capacidad_max,
        telefono=datos.telefono,
        direccion_id=direccion.id,
    )
    db.add(albergue)
    db.commit()
    db.refresh(albergue)
    return albergue


@router.put("/{id}", response_model=AlbergueResponse)
def actualizar_albergue(
    id: int,
    datos: AlbergueUpdate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    albergue = db.query(Albergue).filter(Albergue.id == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")

    for campo, valor in datos.model_dump(exclude_unset=True).items():
        setattr(albergue, campo, valor)

    db.commit()
    db.refresh(albergue)
    return albergue


@router.delete("/{id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_albergue(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)
):
    """Borrado lógico: conserva el historial de donaciones/voluntariados vinculados."""
    albergue = db.query(Albergue).filter(Albergue.id == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")

    albergue.activo = False
    db.commit()
    return None
