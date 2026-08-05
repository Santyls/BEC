from datetime import date, datetime, time
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, Query, status
from pydantic import BaseModel, Field
from sqlalchemy import func, or_
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import (
    Albergue,
    Campana,
    EstadoVoluntariado,
    InscripcionVoluntariado,
    Voluntariado,
    VoluntariadoEncargado,
    Usuario,
)
from app.security.security import (
    ROL_ADMIN,
    ROL_RECEPCIONISTA,
    get_current_user,
    get_recepcionista_or_admin,
    verificar_acceso_albergue,
)

router = APIRouter(prefix="/voluntariados", tags=["Voluntariados"])

ESTADO_PROGRAMADO = 1
ESTADO_ACTIVO = 2
ESTADO_FINALIZADO = 3
ESTADO_CANCELADO_EVENTO = 4

ESTADO_INSCRIPCION_PROXIMO = 1
ESTADO_INSCRIPCION_COMPLETADO = 2


class VoluntariadoCreate(BaseModel):
    nombre_programa: str = Field(min_length=3, max_length=150)
    albergue_id: Optional[int] = None
    campana_id: Optional[int] = None
    ubicacion: Optional[str] = Field(default=None, max_length=255)
    fecha_programada: date
    cupo_maximo: Optional[int] = Field(default=None, gt=0)
    hora_inicio: time
    hora_fin: time
    descripcion_requisitos: str = Field(min_length=1)
    # estado_id NO es parte de este schema a propósito: todo voluntariado nace
    # "Programado" — no tendría sentido crear uno ya Activo/Finalizado/Cancelado.
    # El cambio de estado es una acción explícita post-creación (ver Update / DELETE).


class VoluntariadoUpdate(BaseModel):
    nombre_programa: Optional[str] = Field(default=None, min_length=3, max_length=150)
    albergue_id: Optional[int] = None
    campana_id: Optional[int] = None
    ubicacion: Optional[str] = Field(default=None, max_length=255)
    fecha_programada: Optional[date] = None
    cupo_maximo: Optional[int] = Field(default=None, gt=0)
    hora_inicio: Optional[time] = None
    hora_fin: Optional[time] = None
    estado_id: Optional[int] = None
    descripcion_requisitos: Optional[str] = Field(default=None, min_length=1)


class VoluntariadoResponse(BaseModel):
    id: int
    nombre_programa: str
    albergue_id: Optional[int]
    campana_id: Optional[int]
    ubicacion: Optional[str] = None
    fecha_programada: date
    cupo_maximo: Optional[int]
    hora_inicio: time
    hora_fin: time
    estado_id: int
    descripcion_requisitos: str
    inscritos: int = 0

    class Config:
        from_attributes = True


class UsuarioInscritoResumen(BaseModel):
    id: int
    nombre: str
    apellido_paterno: str
    apellido_materno: str
    correo: Optional[str] = None
    telefono: Optional[str] = None
    vetado: bool

    class Config:
        from_attributes = True


class InscritoResponse(BaseModel):
    id: int  # id de la inscripción (lo usa el admin para cancelarla vía /inscripciones/{id}/cancelar)
    usuario: Optional[UsuarioInscritoResumen] = None  # None si el usuario fue eliminado permanentemente
    estado_id: int
    fecha_inscripcion: datetime
    fecha_cancelacion: Optional[datetime] = None

    class Config:
        from_attributes = True


class EncargadoCreate(BaseModel):
    usuario_id: int


class EncargadoResponse(BaseModel):
    id: int  # id de la asignación (para poder quitarla)
    usuario: UsuarioInscritoResumen

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


def _es_encargado(db: Session, voluntariado_id: int, usuario_id: int) -> bool:
    return (
        db.query(VoluntariadoEncargado)
        .filter(
            VoluntariadoEncargado.voluntariado_id == voluntariado_id,
            VoluntariadoEncargado.usuario_id == usuario_id,
        )
        .first()
        is not None
    )


def _puede_gestionar_asistencia(db: Session, voluntariado: Voluntariado, current_user: Usuario) -> bool:
    """Admin siempre puede. Un Recepcionista puede si: (a) el voluntariado no tiene
    ningún encargado asignado todavía (compatibilidad con lo que ya existía antes de
    este feature), o (b) él mismo es uno de los encargados asignados."""
    if current_user.rol_id == ROL_ADMIN:
        return True
    tiene_encargados = (
        db.query(VoluntariadoEncargado).filter(VoluntariadoEncargado.voluntariado_id == voluntariado.id).first()
        is not None
    )
    if not tiene_encargados:
        return True
    return _es_encargado(db, voluntariado.id, current_user.id)


def _finalizar_interno(db: Session, voluntariado: Voluntariado) -> None:
    """Completa la lógica compartida entre finalizar a mano y el auto-finalizado por
    fecha vencida: no toca a quien ya fue marcado explícitamente (asistió/no asistió/
    canceló), solo a quien seguía en 'Próximo' sin resolución."""
    db.query(InscripcionVoluntariado).filter(
        InscripcionVoluntariado.voluntariado_id == voluntariado.id,
        InscripcionVoluntariado.estado_id == ESTADO_INSCRIPCION_PROXIMO,
    ).update({"estado_id": ESTADO_INSCRIPCION_COMPLETADO})
    voluntariado.estado_id = ESTADO_FINALIZADO


def auto_finalizar_voluntariados_vencidos(db: Session) -> None:
    """Da por finalizado, en automático, cualquier voluntariado cuya fecha ya pasó y
    seguía Programado/Activo — evita depender de que alguien lo cierre a mano (mismo
    patrón perezoso que la purga de usuarios/albergues: corre en cada listado, no con
    un scheduler aparte)."""
    vencidos = (
        db.query(Voluntariado)
        .filter(
            Voluntariado.estado_id.in_([ESTADO_PROGRAMADO, ESTADO_ACTIVO]),
            Voluntariado.fecha_programada < date.today(),
        )
        .all()
    )
    for voluntariado in vencidos:
        _finalizar_interno(db, voluntariado)
    if vencidos:
        db.commit()


@router.get("/", response_model=List[VoluntariadoResponse])
def listar_voluntariados(
    albergue_id: Optional[int] = Query(default=None),
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user),
):
    """BEC_PAL (Admin) ve todos los albergues y puede filtrar por cualquiera. BEC_PRF
    (Recepcionista) solo ve los suyos + los que no tienen albergue asignado (eventos
    independientes) — el albergue_id que mande se ignora."""
    auto_finalizar_voluntariados_vencidos(db)

    query = db.query(Voluntariado)
    if current_user.rol_id == ROL_RECEPCIONISTA:
        query = query.filter(
            or_(Voluntariado.albergue_id == current_user.albergue_id, Voluntariado.albergue_id.is_(None))
        )
    elif albergue_id is not None:
        query = query.filter(Voluntariado.albergue_id == albergue_id)

    voluntariados = query.order_by(Voluntariado.fecha_programada.desc()).all()
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


@router.get("/mis-encargos", response_model=List[VoluntariadoResponse])
def mis_encargos(db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)):
    """Voluntariados donde el Recepcionista autenticado quedó asignado como encargado —
    lo consume BEC_PRF para la vista restringida de 'pasar lista'. Declarado antes de
    /{id} a propósito: si no, FastAPI intenta interpretar 'mis-encargos' como un id."""
    if current_user.rol_id != ROL_RECEPCIONISTA:
        raise HTTPException(status_code=403, detail="Solo disponible para recepcionistas.")

    voluntariados = (
        db.query(Voluntariado)
        .join(VoluntariadoEncargado, VoluntariadoEncargado.voluntariado_id == Voluntariado.id)
        .filter(VoluntariadoEncargado.usuario_id == current_user.id)
        .order_by(Voluntariado.fecha_programada.desc())
        .all()
    )
    return [_a_respuesta(db, v) for v in voluntariados]


@router.get("/{id}", response_model=VoluntariadoResponse)
def obtener_voluntariado(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)
):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)
    return _a_respuesta(db, voluntariado)


@router.get("/{id}/inscritos", response_model=List[InscritoResponse])
def listar_inscritos(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """Detalle de quién se inscribió — lo consumen las vistas de detalle del voluntariado
    en BEC_PAL y BEC_PRF (cancelar inscripción individual, vetar usuario)."""
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)

    return (
        db.query(InscripcionVoluntariado)
        .filter(InscripcionVoluntariado.voluntariado_id == id)
        .order_by(InscripcionVoluntariado.fecha_inscripcion.desc())
        .all()
    )


def _validar_fks(db: Session, albergue_id: Optional[int], campana_id: Optional[int]) -> None:
    if albergue_id is not None and not db.query(Albergue).filter(Albergue.id == albergue_id).first():
        raise HTTPException(status_code=404, detail="El albergue indicado no existe.")
    if campana_id is not None and not db.query(Campana).filter(Campana.id == campana_id).first():
        raise HTTPException(status_code=404, detail="La campaña indicada no existe.")


@router.post("/", response_model=VoluntariadoResponse, status_code=status.HTTP_201_CREATED)
def crear_voluntariado(
    datos: VoluntariadoCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    if datos.hora_fin <= datos.hora_inicio:
        raise HTTPException(status_code=400, detail="La hora de fin debe ser posterior a la de inicio.")
    # Un voluntariado nuevo siempre nace "Programado": crearlo con fecha pasada no
    # tiene sentido (el auto-finalizado lo cerraría de inmediato). Editar sí permite
    # fechas pasadas, para poder corregir un registro histórico.
    if datos.fecha_programada < date.today():
        raise HTTPException(
            status_code=400, detail="No se puede programar un voluntariado en una fecha que ya pasó."
        )

    if current_user.rol_id == ROL_RECEPCIONISTA:
        if current_user.albergue_id is None:
            raise HTTPException(
                status_code=400, detail="Tu cuenta no tiene un albergue asignado; contacta a un Administrador."
            )
        datos.albergue_id = current_user.albergue_id

    _validar_fks(db, datos.albergue_id, datos.campana_id)

    voluntariado = Voluntariado(**datos.model_dump(), estado_id=ESTADO_PROGRAMADO)
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
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)

    cambios = datos.model_dump(exclude_unset=True)

    if current_user.rol_id == ROL_RECEPCIONISTA:
        # No puede reasignar el voluntariado a otro albergue (ni desasignarlo) — eso
        # queda para Admin, igual que en usuarios.py con rol_id/albergue_id.
        cambios.pop("albergue_id", None)

    hora_inicio = cambios.get("hora_inicio", voluntariado.hora_inicio)
    hora_fin = cambios.get("hora_fin", voluntariado.hora_fin)
    if hora_fin <= hora_inicio:
        raise HTTPException(status_code=400, detail="La hora de fin debe ser posterior a la de inicio.")

    if "albergue_id" in cambios or "campana_id" in cambios:
        _validar_fks(
            db,
            cambios.get("albergue_id", voluntariado.albergue_id),
            cambios.get("campana_id", voluntariado.campana_id),
        )
    if "estado_id" in cambios:
        if not db.query(EstadoVoluntariado).filter(EstadoVoluntariado.id == cambios["estado_id"]).first():
            raise HTTPException(status_code=404, detail="El estado indicado no existe.")

    for campo, valor in cambios.items():
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
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)

    voluntariado.estado_id = ESTADO_CANCELADO_EVENTO
    db.commit()
    return None


@router.post("/{id}/finalizar", response_model=VoluntariadoResponse)
def finalizar_voluntariado(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """Marca el voluntariado como Finalizado y da por completada la participación de
    todos los inscritos que seguían en estado Próximo (a quien ya había cancelado, o a
    quien ya se le pasó lista, no se le toca su estado). Restringido a los encargados
    asignados una vez que el voluntariado ya tiene alguno (ver _puede_gestionar_asistencia)."""
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)
        if not _puede_gestionar_asistencia(db, voluntariado, current_user):
            raise HTTPException(
                status_code=403, detail="Solo un encargado asignado a este voluntariado puede finalizarlo."
            )

    if voluntariado.estado_id == ESTADO_CANCELADO_EVENTO:
        raise HTTPException(status_code=400, detail="No se puede finalizar un voluntariado cancelado.")
    if voluntariado.estado_id == ESTADO_FINALIZADO:
        raise HTTPException(status_code=400, detail="Este voluntariado ya está finalizado.")
    if date.today() < voluntariado.fecha_programada:
        raise HTTPException(
            status_code=400, detail="No se puede finalizar un voluntariado antes de la fecha programada."
        )

    _finalizar_interno(db, voluntariado)
    db.commit()
    db.refresh(voluntariado)
    return _a_respuesta(db, voluntariado)


@router.get("/{id}/encargados", response_model=List[EncargadoResponse])
def listar_encargados(
    id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)

    return db.query(VoluntariadoEncargado).filter(VoluntariadoEncargado.voluntariado_id == id).all()


@router.post("/{id}/encargados", response_model=EncargadoResponse, status_code=status.HTTP_201_CREATED)
def asignar_encargado(
    id: int,
    datos: EncargadoCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)

    candidato = db.query(Usuario).filter(Usuario.id == datos.usuario_id).first()
    if not candidato:
        raise HTTPException(status_code=404, detail="El usuario indicado no existe.")
    if candidato.rol_id != ROL_RECEPCIONISTA:
        raise HTTPException(status_code=400, detail="Solo un Recepcionista puede ser encargado de un voluntariado.")
    # Si el voluntariado sí pertenece a un albergue, el encargado debe ser de ese mismo
    # albergue (evita que alguien gestione asistencia de un evento que no le toca).
    if voluntariado.albergue_id is not None and candidato.albergue_id != voluntariado.albergue_id:
        raise HTTPException(status_code=400, detail="El encargado debe pertenecer al mismo albergue del voluntariado.")

    ya_asignado = (
        db.query(VoluntariadoEncargado)
        .filter(VoluntariadoEncargado.voluntariado_id == id, VoluntariadoEncargado.usuario_id == datos.usuario_id)
        .first()
    )
    if ya_asignado:
        raise HTTPException(status_code=400, detail="Ese usuario ya es encargado de este voluntariado.")

    encargado = VoluntariadoEncargado(voluntariado_id=id, usuario_id=datos.usuario_id)
    db.add(encargado)
    db.commit()
    db.refresh(encargado)
    return encargado


@router.delete("/{id}/encargados/{usuario_id}", status_code=status.HTTP_204_NO_CONTENT)
def quitar_encargado(
    id: int,
    usuario_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.id == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, voluntariado.albergue_id)

    encargado = (
        db.query(VoluntariadoEncargado)
        .filter(VoluntariadoEncargado.voluntariado_id == id, VoluntariadoEncargado.usuario_id == usuario_id)
        .first()
    )
    if not encargado:
        raise HTTPException(status_code=404, detail="Ese usuario no es encargado de este voluntariado.")

    db.delete(encargado)
    db.commit()
    return None
