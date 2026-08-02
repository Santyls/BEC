from datetime import date, time
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel
from sqlalchemy import func
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import InscripcionVoluntariado, Voluntariado, Usuario
from app.security.security import get_current_user, get_recepcionista_or_admin

router = APIRouter(prefix="/voluntariados", tags=["Voluntariados"])

ESTADO_PROGRAMADO = 1
ESTADO_ACTIVO = 2
ESTADO_CANCELADO_EVENTO = 4

ESTADO_INSCRIPCION_PROXIMO = 1


class VoluntariadoCreate(BaseModel):
    nombre_programa: str
    albergue_id: Optional[int] = None
    campana_id: Optional[int] = None
    fecha_programada: date
    cupo_maximo: Optional[int] = None
    hora_inicio: time
    hora_fin: time
    estado_id: int = ESTADO_PROGRAMADO
    descripcion_requisitos: str


class VoluntariadoUpdate(BaseModel):
    nombre_programa: Optional[str] = None
    albergue_id: Optional[int] = None
    campana_id: Optional[int] = None
    fecha_programada: Optional[date] = None
    cupo_maximo: Optional[int] = None
    hora_inicio: Optional[time] = None
    hora_fin: Optional[time] = None
    estado_id: Optional[int] = None
    descripcion_requisitos: Optional[str] = None


class VoluntariadoResponse(BaseModel):
    id: int
    nombre_programa: str
    albergue_id: Optional[int]
    campana_id: Optional[int]
    fecha_programada: date
    cupo_maximo: Optional[int]
    hora_inicio: time
    hora_fin: time
    estado_id: int
    descripcion_requisitos: str
    inscritos: int = 0

    class Config:
        from_attributes = True


def _contar_inscritos(db: Session, voluntariado_id: int) -> int:
    return (
        db.query(func.count(InscripcionVoluntariado.id))
        .filter(
            InscripcionVoluntariado.voluntariado_id == voluntariado_id,
            InscripcionVoluntariado.estado_id != 3,  # excluye canceladas
        )
        .scalar()
    )


def _a_respuesta(db: Session, voluntariado: Voluntariado) -> VoluntariadoResponse:
    data = VoluntariadoResponse.model_validate(voluntariado)
    data.inscritos = _contar_inscritos(db, voluntariado.id)
    return data


@router.get("/", response_model=List[VoluntariadoResponse])
def listar_voluntariados(
    db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    voluntariados = db.query(Voluntariado).order_by(Voluntariado.fecha_programada.desc()).all()
    return [_a_respuesta(db, v) for v in voluntariados]


@router.get("/disponibles", response_model=List[VoluntariadoResponse])
def listar_disponibles(
    db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    """Lo que consume el móvil en el recuadro 'Voluntariados disponibles': programados o
    activos, con cupo abierto (o sin límite de cupo)."""
    candidatos = (
        db.query(Voluntariado)
        .filter(Voluntariado.estado_id.in_([ESTADO_PROGRAMADO, ESTADO_ACTIVO]))
        .filter(Voluntariado.fecha_programada >= date.today())
        .order_by(Voluntariado.fecha_programada)
        .all()
    )
    disponibles = []
    for v in candidatos:
        inscritos = _contar_inscritos(db, v.id)
        if v.cupo_maximo is None or inscritos < v.cupo_maximo:
            respuesta = VoluntariadoResponse.model_validate(v)
            respuesta.inscritos = inscritos
            disponibles.append(respuesta)
    return disponibles


@router.get("/{id}", response_model=VoluntariadoResponse)
def obtener_voluntariado(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    return _a_respuesta(db, voluntariado)


@router.post("/", response_model=VoluntariadoResponse, status_code=status.HTTP_201_CREATED)
def crear_voluntariado(
    datos: VoluntariadoCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    if datos.hora_fin <= datos.hora_inicio:
        raise HTTPException(status_code=400, detail="La hora de fin debe ser posterior a la de inicio.")

    voluntariado = Voluntariado(**datos.model_dump())
    db.add(voluntariado)
    db.commit()
    db.refresh(voluntariado)
    return _a_respuesta(db, voluntariado)


@router.put("/{id}", response_model=VoluntariadoResponse)
def actualizar_voluntariado(
    id: int,
    datos: VoluntariadoUpdate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")

    for campo, valor in datos.model_dump(exclude_unset=True).items():
        setattr(voluntariado, campo, valor)

    db.commit()
    db.refresh(voluntariado)
    return _a_respuesta(db, voluntariado)


@router.delete("/{id}", status_code=status.HTTP_204_NO_CONTENT)
def cancelar_voluntariado(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """Cancela el EVENTO completo (no una inscripción individual — eso es
    PUT /inscripciones/{id}/cancelar). Conserva el registro, solo cambia su estado."""
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")

    voluntariado.estado_id = ESTADO_CANCELADO_EVENTO
    db.commit()
    return None
