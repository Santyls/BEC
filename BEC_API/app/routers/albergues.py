from datetime import datetime, timedelta, timezone
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel, Field
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Albergue, Direccion, Donacion, Estado, Usuario, Voluntariado
from app.security.security import get_admin_user, get_current_user

router = APIRouter(prefix="/albergues", tags=["Albergues"])

TELEFONO_PATTERN = r"^\d{10}$"
DIAS_GRACIA_ELIMINACION = 30


class DireccionInput(BaseModel):
    estado_id: int
    municipio: str = Field(min_length=1, max_length=100)
    colonia: str = Field(min_length=1, max_length=150)
    calle: str = Field(min_length=1, max_length=150)
    numero_exterior: str = Field(min_length=1, max_length=20)
    numero_interior: Optional[str] = Field(default=None, max_length=20)
    codigo_postal: str = Field(pattern=r"^\d{5}$")


class DireccionResponse(DireccionInput):
    id: int

    class Config:
        from_attributes = True


class AlbergueCreate(BaseModel):
    nombre: str = Field(min_length=3, max_length=150)
    capacidad_max: int = Field(gt=0)
    telefono: str = Field(pattern=TELEFONO_PATTERN)
    direccion: DireccionInput


class AlbergueUpdate(BaseModel):
    nombre: Optional[str] = Field(default=None, min_length=3, max_length=150)
    capacidad_max: Optional[int] = Field(default=None, gt=0)
    telefono: Optional[str] = Field(default=None, pattern=TELEFONO_PATTERN)
    direccion: Optional[DireccionInput] = None


class AlbergueResponse(BaseModel):
    id: int
    nombre: str
    capacidad_max: int
    telefono: str
    activo: bool
    fecha_desactivacion: Optional[datetime] = None
    direccion: DireccionResponse

    class Config:
        from_attributes = True


def _anonimizar_y_eliminar_albergue(db: Session, albergue: Albergue) -> None:
    """Desvincula (albergue_id=NULL) el historial de donaciones/voluntariados antes de
    borrar la fila — el registro se conserva, solo se pierde a qué albergue apuntaba."""
    db.query(Donacion).filter(Donacion.albergue_id == albergue.id).update({"albergue_id": None})
    db.query(Voluntariado).filter(Voluntariado.albergue_id == albergue.id).update({"albergue_id": None})
    db.delete(albergue)


def purgar_albergues_vencidos(db: Session) -> None:
    """Mismo criterio de gracia de 30 días que usuarios.py — ver esa función para el
    razonamiento de por qué es una purga perezosa y no un cron aparte."""
    limite = datetime.now(timezone.utc) - timedelta(days=DIAS_GRACIA_ELIMINACION)
    vencidos = (
        db.query(Albergue)
        .filter(Albergue.activo.is_(False), Albergue.fecha_desactivacion.isnot(None), Albergue.fecha_desactivacion < limite)
        .all()
    )
    for albergue in vencidos:
        _anonimizar_y_eliminar_albergue(db, albergue)
    if vencidos:
        db.commit()


@router.get("/", response_model=List[AlbergueResponse])
def listar_albergues(
    solo_activos: bool = True,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """Cualquier usuario autenticado puede consultarlos (no es información sensible;
    el móvil los muestra en el detalle de donaciones/voluntariados)."""
    purgar_albergues_vencidos(db)
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
    if not db.query(Estado).filter(Estado.id == datos.direccion.estado_id).first():
        raise HTTPException(status_code=404, detail="El estado indicado no existe.")

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

    cambios = datos.model_dump(exclude_unset=True)
    direccion_nueva = cambios.pop("direccion", None)

    for campo, valor in cambios.items():
        setattr(albergue, campo, valor)

    if direccion_nueva is not None:
        if not db.query(Estado).filter(Estado.id == direccion_nueva["estado_id"]).first():
            raise HTTPException(status_code=404, detail="El estado indicado no existe.")

        direccion = db.query(Direccion).filter(Direccion.id == albergue.direccion_id).first()
        for campo, valor in direccion_nueva.items():
            setattr(direccion, campo, valor)

    db.commit()
    db.refresh(albergue)
    return albergue


@router.delete("/{id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_albergue(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)
):
    """Borrado lógico: conserva el historial de donaciones/voluntariados vinculados.
    Queda 30 días con posibilidad de reactivarse antes de purgarse en definitiva."""
    albergue = db.query(Albergue).filter(Albergue.id == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")

    albergue.activo = False
    albergue.fecha_desactivacion = datetime.now(timezone.utc)
    db.commit()
    return None


@router.post("/{id}/reactivar", response_model=AlbergueResponse)
def reactivar_albergue(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)
):
    """Revierte una baja mientras siga dentro de los 30 días de gracia."""
    albergue = db.query(Albergue).filter(Albergue.id == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")

    albergue.activo = True
    albergue.fecha_desactivacion = None
    db.commit()
    db.refresh(albergue)
    return albergue


@router.delete("/{id}/permanente", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_albergue_permanente(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)
):
    """Borrado físico inmediato, sin esperar los 30 días de gracia. Requiere que el
    albergue ya esté desactivado."""
    albergue = db.query(Albergue).filter(Albergue.id == id).first()
    if not albergue:
        raise HTTPException(status_code=404, detail="Albergue no encontrado")
    if albergue.activo:
        raise HTTPException(
            status_code=400, detail="Debes desactivar el albergue antes de poder eliminarlo permanentemente."
        )

    _anonimizar_y_eliminar_albergue(db, albergue)
    db.commit()
    return None
