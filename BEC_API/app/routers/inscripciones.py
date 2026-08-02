from datetime import date, datetime, time, timezone
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import InscripcionVoluntariado, Voluntariado, Usuario
from app.routers.voluntariados import _contar_inscritos, ESTADO_PROGRAMADO, ESTADO_ACTIVO
from app.security.security import ROL_CIUDADANO, get_current_user

router = APIRouter(prefix="/inscripciones", tags=["Inscripciones a voluntariados"])

ESTADO_PROXIMO = 1
ESTADO_COMPLETADO = 2
ESTADO_CANCELADO = 3


class InscripcionCreate(BaseModel):
    voluntariado_id: int
    usuario_id: Optional[int] = None  # solo lo usa Recepcionista/Admin al inscribir a alguien


class VoluntariadoResumen(BaseModel):
    id: int
    nombre_programa: str
    fecha_programada: date
    hora_inicio: time
    hora_fin: time
    descripcion_requisitos: str

    class Config:
        from_attributes = True


class InscripcionResponse(BaseModel):
    id: int
    usuario_id: int
    voluntariado_id: int
    estado_id: int
    fecha_inscripcion: datetime
    fecha_cancelacion: Optional[datetime] = None
    voluntariado: VoluntariadoResumen

    class Config:
        from_attributes = True


@router.get("/me", response_model=List[InscripcionResponse])
def mis_inscripciones(
    db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    """Lo que llena la pantalla 'Mis Voluntariados' del móvil."""
    return (
        db.query(InscripcionVoluntariado)
        .filter(InscripcionVoluntariado.usuario_id == current_user.id)
        .order_by(InscripcionVoluntariado.fecha_inscripcion.desc())
        .all()
    )


@router.post("/", response_model=InscripcionResponse, status_code=status.HTTP_201_CREATED)
def inscribirse(
    datos: InscripcionCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    # Un ciudadano solo puede inscribirse a sí mismo; el staff puede inscribir a alguien más
    # (BEC_PRF "Asignar Voluntario").
    if current_user.rol_id == ROL_CIUDADANO:
        usuario_id = current_user.id
    else:
        usuario_id = datos.usuario_id or current_user.id

    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == datos.voluntariado_id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    if voluntariado.estado_id not in (ESTADO_PROGRAMADO, ESTADO_ACTIVO):
        raise HTTPException(status_code=400, detail="Este voluntariado ya no acepta inscripciones.")

    ya_inscrito = (
        db.query(InscripcionVoluntariado)
        .filter(
            InscripcionVoluntariado.usuario_id == usuario_id,
            InscripcionVoluntariado.voluntariado_id == datos.voluntariado_id,
            InscripcionVoluntariado.estado_id != ESTADO_CANCELADO,
        )
        .first()
    )
    if ya_inscrito:
        raise HTTPException(status_code=400, detail="Ya estás inscrito en este voluntariado.")

    if voluntariado.cupo_maximo is not None:
        inscritos = _contar_inscritos(db, voluntariado.id)
        if inscritos >= voluntariado.cupo_maximo:
            raise HTTPException(status_code=400, detail="Ya no hay cupo disponible para este voluntariado.")

    inscripcion = InscripcionVoluntariado(
        usuario_id=usuario_id,
        voluntariado_id=datos.voluntariado_id,
        estado_id=ESTADO_PROXIMO,
    )
    db.add(inscripcion)
    db.commit()
    db.refresh(inscripcion)
    return inscripcion


@router.put("/{id}/cancelar", response_model=InscripcionResponse)
def cancelar_inscripcion(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """Cancela TU participación individual (no el evento completo). Un ciudadano solo
    puede cancelar la suya; Recepcionista/Admin pueden cancelar cualquiera."""
    inscripcion = db.query(InscripcionVoluntariado).filter(InscripcionVoluntariado.id == id).first()
    if not inscripcion:
        raise HTTPException(status_code=404, detail="Inscripción no encontrada")

    if current_user.rol_id == ROL_CIUDADANO and inscripcion.usuario_id != current_user.id:
        raise HTTPException(status_code=403, detail="No puedes cancelar la inscripción de otra persona.")

    if inscripcion.estado_id != ESTADO_PROXIMO:
        raise HTTPException(status_code=400, detail="Solo se pueden cancelar inscripciones en estado Próximo.")

    inscripcion.estado_id = ESTADO_CANCELADO
    inscripcion.fecha_cancelacion = datetime.now(timezone.utc)
    db.commit()
    db.refresh(inscripcion)
    return inscripcion
