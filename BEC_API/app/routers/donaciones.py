from datetime import date
from typing import List, Optional

from fastapi import APIRouter, Depends, HTTPException, Query, status
from pydantic import BaseModel, Field
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Albergue, Categoria, Condicion, Donacion, Unidad, Usuario
from app.security.security import (
    ROL_CIUDADANO,
    ROL_RECEPCIONISTA,
    get_admin_user,
    get_current_user,
    get_recepcionista_or_admin,
    verificar_acceso_albergue,
)

router = APIRouter(prefix="/donaciones", tags=["Donaciones"])


class DonacionCreate(BaseModel):
    usuario_id: Optional[int] = None  # None = donación anónima (BEC_PAL lo permite explícitamente)
    categoria_id: int
    condicion_id: int
    cantidad: float = Field(gt=0)
    unidad_id: int
    marca: Optional[str] = Field(default=None, max_length=100)
    # Optional a nivel schema porque BEC_PRF (Recepcionista) no lo manda — se fuerza
    # server-side al propio albergue. Admin (BEC_PAL) sí debe mandarlo explícitamente.
    albergue_id: Optional[int] = None


class DonacionUpdate(BaseModel):
    """Todo opcional: se manda solo lo que se quiere corregir (PUT parcial).
    `fecha_donacion` no se puede editar a propósito — es el registro de cuándo
    entró realmente al inventario."""

    usuario_id: Optional[int] = None
    categoria_id: Optional[int] = None
    condicion_id: Optional[int] = None
    cantidad: Optional[float] = Field(default=None, gt=0)
    unidad_id: Optional[int] = None
    marca: Optional[str] = Field(default=None, max_length=100)
    albergue_id: Optional[int] = None


class AlbergueResumen(BaseModel):
    id: int
    nombre: str

    class Config:
        from_attributes = True


class UsuarioResumen(BaseModel):
    id: int
    nombre: str
    apellido_paterno: str
    correo: Optional[str] = None

    class Config:
        from_attributes = True


class CatalogoResumen(BaseModel):
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
    albergue_id: Optional[int]
    fecha_donacion: date
    albergue: Optional[AlbergueResumen] = None
    usuario: Optional[UsuarioResumen] = None
    categoria: CatalogoResumen
    condicion: CatalogoResumen
    unidad: CatalogoResumen

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
    """Inventario (BEC_PAL ve todos los albergues; BEC_PRF/Recepcionista solo el suyo,
    sin importar qué albergue_id mande — se ignora y se fuerza al propio)."""
    query = db.query(Donacion)
    if categoria_id is not None:
        query = query.filter(Donacion.categoria_id == categoria_id)

    if current_user.rol_id == ROL_RECEPCIONISTA:
        # Filtro SIEMPRE aplicado (incluso si albergue_id propio es None) — nunca debe
        # colar al caso "sin filtro = ve todo" de un Recepcionista sin albergue asignado.
        query = query.filter(Donacion.albergue_id == current_user.albergue_id)
    elif albergue_id is not None:
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
    if current_user.rol_id == ROL_CIUDADANO and donacion.usuario_id != current_user.id:
        raise HTTPException(status_code=403, detail="No puedes ver esta donación.")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, donacion.albergue_id)
    return donacion


@router.post("/", response_model=DonacionResponse, status_code=status.HTTP_201_CREATED)
def registrar_donacion(
    datos: DonacionCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """La registra el Recepcionista en el mostrador (BEC_PRF) o el Admin — el ciudadano
    únicamente consulta su historial, no crea donaciones desde el móvil (RF05 original).
    Un Recepcionista solo puede registrar donaciones para SU propio albergue: el
    albergue_id que mande se ignora y se fuerza al suyo."""
    if current_user.rol_id == ROL_RECEPCIONISTA:
        if current_user.albergue_id is None:
            raise HTTPException(
                status_code=400, detail="Tu cuenta no tiene un albergue asignado; contacta a un Administrador."
            )
        datos.albergue_id = current_user.albergue_id
    elif datos.albergue_id is None:
        raise HTTPException(status_code=422, detail="El albergue destino es obligatorio.")

    if datos.usuario_id is not None:
        donante = db.query(Usuario).filter(Usuario.id == datos.usuario_id).first()
        if not donante:
            raise HTTPException(status_code=404, detail="El usuario donante no existe.")

    if not db.query(Categoria).filter(Categoria.id == datos.categoria_id).first():
        raise HTTPException(status_code=404, detail="La categoría indicada no existe.")
    if not db.query(Condicion).filter(Condicion.id == datos.condicion_id).first():
        raise HTTPException(status_code=404, detail="La condición indicada no existe.")
    if not db.query(Unidad).filter(Unidad.id == datos.unidad_id).first():
        raise HTTPException(status_code=404, detail="La unidad indicada no existe.")
    if not db.query(Albergue).filter(Albergue.id == datos.albergue_id).first():
        raise HTTPException(status_code=404, detail="El albergue indicado no existe.")

    donacion = Donacion(**datos.model_dump())
    db.add(donacion)
    db.commit()
    db.refresh(donacion)
    return donacion


@router.put("/{id}", response_model=DonacionResponse)
def actualizar_donacion(
    id: int,
    datos: DonacionUpdate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin),
):
    """Corrige una donación ya registrada (se capturó mal la cantidad, la categoría,
    etc.). Mismo criterio de acceso que el resto: el Recepcionista solo puede tocar
    las de su albergue y no puede moverlas a otro."""
    donacion = db.query(Donacion).filter(Donacion.id == id).first()
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")
    if current_user.rol_id == ROL_RECEPCIONISTA:
        verificar_acceso_albergue(current_user, donacion.albergue_id)

    cambios = datos.model_dump(exclude_unset=True)
    if current_user.rol_id == ROL_RECEPCIONISTA:
        cambios.pop("albergue_id", None)

    if "usuario_id" in cambios and cambios["usuario_id"] is not None:
        if not db.query(Usuario).filter(Usuario.id == cambios["usuario_id"]).first():
            raise HTTPException(status_code=404, detail="El usuario donante no existe.")
    if "categoria_id" in cambios and not db.query(Categoria).filter(Categoria.id == cambios["categoria_id"]).first():
        raise HTTPException(status_code=404, detail="La categoría indicada no existe.")
    if "condicion_id" in cambios and not db.query(Condicion).filter(Condicion.id == cambios["condicion_id"]).first():
        raise HTTPException(status_code=404, detail="La condición indicada no existe.")
    if "unidad_id" in cambios and not db.query(Unidad).filter(Unidad.id == cambios["unidad_id"]).first():
        raise HTTPException(status_code=404, detail="La unidad indicada no existe.")
    if "albergue_id" in cambios and not db.query(Albergue).filter(Albergue.id == cambios["albergue_id"]).first():
        raise HTTPException(status_code=404, detail="El albergue indicado no existe.")

    for campo, valor in cambios.items():
        setattr(donacion, campo, valor)

    db.commit()
    db.refresh(donacion)
    return donacion


@router.delete("/{id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_donacion(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_admin_user),
):
    """Borrado definitivo, restringido a Admin: una donación mal capturada se corrige
    con PUT; eliminarla altera el inventario y los reportes, así que no se deja al
    Recepcionista."""
    donacion = db.query(Donacion).filter(Donacion.id == id).first()
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")

    db.delete(donacion)
    db.commit()
    return None
