from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from pydantic import BaseModel
from typing import List, Optional
from app.data.database import get_db
from app.models.models import Estado_Rep, Municipio, Colonia, Direccion, Usuario
from app.security.security import get_current_user, get_recepcionista_or_admin

router = APIRouter()

# ─────────────────────────────────────────────
# Schemas
# ─────────────────────────────────────────────

class EstadoResponse(BaseModel):
    Id_Estado: int
    Nombre_Estado: str
    class Config:
        from_attributes = True

class MunicipioResponse(BaseModel):
    Id_Municipio: int
    Nombre_Municipio: str
    Id_Estado: int
    class Config:
        from_attributes = True

class ColoniaResponse(BaseModel):
    Id_Colonia: int
    Nombre_Colonia: str
    Id_mucipio: int
    Cp: int
    class Config:
        from_attributes = True

class DireccionCreate(BaseModel):
    Id_Colonia: int
    Calle: str
    No_exterior: str

class DireccionResponse(BaseModel):
    Id_Direccion: int
    Id_Colonia: int
    Calle: str
    No_exterior: str
    class Config:
        from_attributes = True

# ─────────────────────────────────────────────
# Catálogo: Estados
# ─────────────────────────────────────────────

@router.get("/estados", response_model=List[EstadoResponse], tags=["Catálogos"])
def listar_estados(
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user)
):
    return db.query(Estado_Rep).order_by(Estado_Rep.Nombre_Estado).all()

# ─────────────────────────────────────────────
# Catálogo: Municipios por Estado
# ─────────────────────────────────────────────

@router.get("/estados/{id_estado}/municipios", response_model=List[MunicipioResponse], tags=["Catálogos"])
def listar_municipios_por_estado(
    id_estado: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user)
):
    estado = db.query(Estado_Rep).filter(Estado_Rep.Id_Estado == id_estado).first()
    if not estado:
        raise HTTPException(status_code=404, detail="Estado no encontrado")
    return db.query(Municipio).filter(Municipio.Id_Estado == id_estado).order_by(Municipio.Nombre_Municipio).all()

# ─────────────────────────────────────────────
# Catálogo: Colonias por Municipio
# ─────────────────────────────────────────────

@router.get("/municipios/{id_municipio}/colonias", response_model=List[ColoniaResponse], tags=["Catálogos"])
def listar_colonias_por_municipio(
    id_municipio: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user)
):
    municipio = db.query(Municipio).filter(Municipio.Id_Municipio == id_municipio).first()
    if not municipio:
        raise HTTPException(status_code=404, detail="Municipio no encontrado")
    return db.query(Colonia).filter(Colonia.Id_mucipio == id_municipio).order_by(Colonia.Nombre_Colonia).all()

# ─────────────────────────────────────────────
# CRUD: Direcciones
# ─────────────────────────────────────────────

@router.post("/direcciones", response_model=DireccionResponse, status_code=status.HTTP_201_CREATED, tags=["Direcciones"])
def crear_direccion(
    direccion_in: DireccionCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    colonia = db.query(Colonia).filter(Colonia.Id_Colonia == direccion_in.Id_Colonia).first()
    if not colonia:
        raise HTTPException(status_code=400, detail="La Colonia especificada no existe")

    nueva = Direccion(
        Id_Colonia=direccion_in.Id_Colonia,
        Calle=direccion_in.Calle,
        No_exterior=direccion_in.No_exterior,
    )
    db.add(nueva)
    db.commit()
    db.refresh(nueva)
    return nueva

@router.get("/direcciones/{id}", response_model=DireccionResponse, tags=["Direcciones"])
def obtener_direccion(
    id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_user)
):
    dir_ = db.query(Direccion).filter(Direccion.Id_Direccion == id).first()
    if not dir_:
        raise HTTPException(status_code=404, detail="Dirección no encontrada")
    return dir_
