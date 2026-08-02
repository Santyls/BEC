from typing import List

from fastapi import APIRouter, Depends, HTTPException
from pydantic import BaseModel
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import (
    Categoria,
    CodigoPostal,
    Condicion,
    Estado,
    EstadoCampana,
    EstadoInscripcion,
    EstadoVoluntariado,
    Genero,
    Rol,
    Unidad,
)

router = APIRouter(prefix="/catalogos", tags=["Catálogos"])


class CatalogoItem(BaseModel):
    id: int
    nombre: str

    class Config:
        from_attributes = True


def _listar(db: Session, modelo):
    return db.query(modelo).order_by(modelo.nombre).all()


@router.get("/roles", response_model=List[CatalogoItem])
def roles(db: Session = Depends(get_db)):
    return _listar(db, Rol)


@router.get("/generos", response_model=List[CatalogoItem])
def generos(db: Session = Depends(get_db)):
    return _listar(db, Genero)


@router.get("/categorias", response_model=List[CatalogoItem])
def categorias(db: Session = Depends(get_db)):
    return _listar(db, Categoria)


@router.get("/unidades", response_model=List[CatalogoItem])
def unidades(db: Session = Depends(get_db)):
    return _listar(db, Unidad)


@router.get("/condiciones", response_model=List[CatalogoItem])
def condiciones(db: Session = Depends(get_db)):
    return _listar(db, Condicion)


@router.get("/estados-campanas", response_model=List[CatalogoItem])
def estados_campanas(db: Session = Depends(get_db)):
    return _listar(db, EstadoCampana)


@router.get("/estados-voluntariado", response_model=List[CatalogoItem])
def estados_voluntariado(db: Session = Depends(get_db)):
    return _listar(db, EstadoVoluntariado)


@router.get("/estados-inscripcion", response_model=List[CatalogoItem])
def estados_inscripcion(db: Session = Depends(get_db)):
    return _listar(db, EstadoInscripcion)


@router.get("/estados", response_model=List[CatalogoItem])
def estados(db: Session = Depends(get_db)):
    """Los 32 estados de México (catálogo fijo)."""
    return _listar(db, Estado)


# --- Autocompletado de dirección por código postal ---

class OpcionColonia(BaseModel):
    municipio: str
    colonia: str


class CodigoPostalResponse(BaseModel):
    codigo_postal: str
    estado_id: int
    estado_nombre: str
    opciones: List[OpcionColonia]


@router.get("/cp/{codigo_postal}", response_model=CodigoPostalResponse)
def buscar_codigo_postal(codigo_postal: str, db: Session = Depends(get_db)):
    """Dado un CP de 5 dígitos, regresa el estado y las colonias/municipios que le
    corresponden, para autocompletar el formulario de dirección (móvil, PRF, PAL).
    Nota: el catálogo cargado hoy es una muestra de Querétaro — la importación
    completa del catálogo SEPOMEX queda pendiente como tarea aparte."""
    filas = db.query(CodigoPostal).filter(CodigoPostal.codigo_postal == codigo_postal).all()
    if not filas:
        raise HTTPException(status_code=404, detail="Código postal no encontrado en el catálogo.")

    return CodigoPostalResponse(
        codigo_postal=codigo_postal,
        estado_id=filas[0].estado_id,
        estado_nombre=filas[0].estado.nombre,
        opciones=[OpcionColonia(municipio=f.municipio, colonia=f.colonia) for f in filas],
    )
