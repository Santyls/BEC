from datetime import date, datetime, time, timezone
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import InscripcionVoluntariado, Voluntariado, Usuario
from app.routers.voluntariados import _contar_inscritos, _puede_gestionar_asistencia, ESTADO_PROGRAMADO, ESTADO_ACTIVO
from app.security.security import (
    ROL_CIUDADANO,
    ROL_RECEPCIONISTA,
    get_current_user,
    get_recepcionista_or_admin,
    verificar_acceso_albergue,
)

router = APIRouter(prefix="/inscripciones", tags=["Inscripciones a voluntariados"])

ESTADO_PROXIMO = 1
ESTADO_COMPLETADO = 2
ESTADO_CANCELADO = 3
ESTADO_NO_ASISTIO = 4


class InscripcionCreate(BaseModel):
    voluntariado_id: int
    usuario_id: Optional[int] = None  # solo lo usa Recepcionista/Admin al inscribir a alguien


class AsistenciaUpdate(BaseModel):
    asistio: bool


class VoluntariadoResumen(BaseModel):
    id: int
    nombre_programa: str
    ubicacion: Optional[str] = None
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
        usuario_objetivo = current_user
    else:
        usuario_id = datos.usuario_id or current_user.id
        usuario_objetivo = db.query(Usuario).filter(Usuario.id == usuario_id).first()
        if not usuario_objetivo:
            raise HTTPException(status_code=404, detail="El usuario a inscribir no existe.")

    if usuario_objetivo.vetado:
        raise HTTPException(
            status_code=403, detail="Este usuario está vetado y no puede inscribirse a voluntariados."
        )

    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == datos.voluntariado_id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    if voluntariado.estado_id not in (ESTADO_PROGRAMADO, ESTADO_ACTIVO):
        raise HTTPException(status_code=400, detail="Este voluntariado ya no acepta inscripciones.")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        # "Asignar Voluntario" (BEC_PRF) solo sobre voluntariados de su propio albergue.
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)

    # Solo puede existir UNA fila por (usuario, voluntariado) — uq_inscripcion_usuario_voluntariado.
    # Si ya canceló antes, se reutiliza esa fila (vuelve a "Próximo") en vez de intentar
    # insertar una segunda, que violaría la restricción única.
    inscripcion_existente = (
        db.query(InscripcionVoluntariado)
        .filter(
            InscripcionVoluntariado.usuario_id == usuario_id,
            InscripcionVoluntariado.voluntariado_id == datos.voluntariado_id,
        )
        .first()
    )
    if inscripcion_existente and inscripcion_existente.estado_id != ESTADO_CANCELADO:
        raise HTTPException(status_code=400, detail="Ya estás inscrito en este voluntariado.")

    if voluntariado.cupo_maximo is not None:
        inscritos = _contar_inscritos(db, voluntariado.id)
        if inscritos >= voluntariado.cupo_maximo:
            raise HTTPException(status_code=400, detail="Ya no hay cupo disponible para este voluntariado.")

    if inscripcion_existente:
        inscripcion = inscripcion_existente
        inscripcion.estado_id = ESTADO_PROXIMO
        inscripcion.fecha_inscripcion = datetime.now(timezone.utc)
        inscripcion.fecha_cancelacion = None
    else:
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
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, inscripcion.voluntariado.albergue_id)

    if inscripcion.estado_id != ESTADO_PROXIMO:
        raise HTTPException(status_code=400, detail="Solo se pueden cancelar inscripciones en estado Próximo.")

    inscripcion.estado_id = ESTADO_CANCELADO
    inscripcion.fecha_cancelacion = datetime.now(timezone.utc)
    db.commit()
    db.refresh(inscripcion)
    return inscripcion


@router.put("/{id}/asistencia", response_model=InscripcionResponse)
def marcar_asistencia(
    id: int,
    datos: AsistenciaUpdate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """'Pasar lista': solo puede hacerlo un encargado asignado a este voluntariado (o
    cualquier recepcionista con acceso si el voluntariado aún no tiene encargados —
    ver _puede_gestionar_asistencia)."""
    inscripcion = db.query(InscripcionVoluntariado).filter(InscripcionVoluntariado.id == id).first()
    if not inscripcion:
        raise HTTPException(status_code=404, detail="Inscripción no encontrada")

    voluntariado = inscripcion.voluntariado
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)
        if not _puede_gestionar_asistencia(db, voluntariado, current_user):
            raise HTTPException(
                status_code=403, detail="Solo un encargado asignado a este voluntariado puede pasar lista."
            )

    if inscripcion.estado_id != ESTADO_PROXIMO:
        raise HTTPException(
            status_code=400, detail="Solo se puede registrar asistencia de inscripciones en estado Próximo."
        )

    inscripcion.estado_id = ESTADO_COMPLETADO if datos.asistio else ESTADO_NO_ASISTIO
    db.commit()
    db.refresh(inscripcion)
    return inscripcion
