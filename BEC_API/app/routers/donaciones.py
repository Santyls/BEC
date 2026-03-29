from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from pydantic import BaseModel
from typing import List, Optional
from app.data.database import get_db
from app.models.models import Donacion, Usuario, Categoria, Unidad, Albergue
from app.security.security import get_current_user, get_admin_user, get_recepcionista_or_admin

router = APIRouter()

#Schemas

class DonacionCreate(BaseModel):
    Id_Usuario: int
    id_Categoria: int
    Id_Condicion: str
    Cantidad: float
    Id_Unidad: int
    Marca: Optional[str] = None
    # Id_Albergue no se recibe en el body, se saca del token/usuario

class DonacionUpdate(BaseModel):
    Id_Usuario: Optional[int] = None
    id_Categoria: Optional[int] = None
    Id_Condicion: Optional[str] = None
    Cantidad: Optional[float] = None
    Id_Unidad: Optional[int] = None
    Marca: Optional[str] = None
    Id_Albergue: Optional[int] = None

class DonacionResponse(BaseModel):
    Id_Donacion: int
    Id_Usuario: int
    id_Categoria: int
    Id_Condicion: str
    Cantidad: float
    Id_Unidad: int
    Marca: Optional[str] = None
    Id_Albergue: int

    class Config:
        from_attributes = True

# --- Endpoints ---

@router.post("/", status_code=status.HTTP_201_CREATED)
def crear_donacion(
    donacion_in: DonacionCreate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_recepcionista_or_admin)
):
    # Verificacion de foraneas
    usuario_existe = db.query(Usuario).filter(Usuario.id_Usuario == donacion_in.Id_Usuario).first()
    if not usuario_existe:
        raise HTTPException(status_code=400, detail="El Id_Usuario especificado no existe")
        
    categoria_existe = db.query(Categoria).filter(Categoria.Id_Categoria == donacion_in.id_Categoria).first()
    if not categoria_existe:
        raise HTTPException(status_code=400, detail="La id_Categoria especificada no existe")
        
    unidad_existe = db.query(Unidad).filter(Unidad.Id_Unidad == donacion_in.Id_Unidad).first()
    if not unidad_existe:
        raise HTTPException(status_code=400, detail="El Id_Unidad especificado no existe")

    if current_user.Id_Albergue is None:
        raise HTTPException(status_code=400, detail="El usuario actual no tiene un albergue asgnado para recibir donaciones")

    nueva_donacion = Donacion(
        Id_Usuario=donacion_in.Id_Usuario,
        id_Categoria=donacion_in.id_Categoria,
        Id_Condicion=donacion_in.Id_Condicion,
        Cantidad=donacion_in.Cantidad,
        Id_Unidad=donacion_in.Id_Unidad,
        Marca=donacion_in.Marca,
        Id_Albergue=current_user.Id_Albergue
    )
    
    db.add(nueva_donacion)
    db.commit()
    db.refresh(nueva_donacion)
    
    return {
        "mensaje": "Donación agregada correctamente", 
        "status": 201, 
        "data": nueva_donacion
    }

@router.get("/")
def listar_donaciones(db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)):
    return db.query(Donacion).all()

@router.get("/{id}")
def obtener_donacion(id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)):
    donacion = db.query(Donacion).filter(Donacion.Id_Donacion == id).first()
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")
    return donacion

@router.put("/{id}")
def actualizar_donacion_completa(
    id: int, 
    donacion_in: DonacionUpdate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    donacion = db.query(Donacion).filter(Donacion.Id_Donacion == id).first()
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")
        
    # Validaciones foraneas
    if donacion_in.Id_Usuario is not None:
        usuario_existe = db.query(Usuario).filter(Usuario.id_Usuario == donacion_in.Id_Usuario).first()
        if not usuario_existe:
            raise HTTPException(status_code=400, detail="El Id_Usuario especificado no existe")
            
    if donacion_in.id_Categoria is not None:
        categoria_existe = db.query(Categoria).filter(Categoria.Id_Categoria == donacion_in.id_Categoria).first()
        if not categoria_existe:
            raise HTTPException(status_code=400, detail="La id_Categoria especificada no existe")
            
    if donacion_in.Id_Unidad is not None:
        unidad_existe = db.query(Unidad).filter(Unidad.Id_Unidad == donacion_in.Id_Unidad).first()
        if not unidad_existe:
            raise HTTPException(status_code=400, detail="El Id_Unidad especificado no existe")

    if donacion_in.Id_Albergue is not None:
        albergue_existe = db.query(Albergue).filter(Albergue.Id_Albergue == donacion_in.Id_Albergue).first()
        if not albergue_existe:
            raise HTTPException(status_code=400, detail="El Id_Albergue especificado no existe")

    update_data = donacion_in.model_dump(exclude_unset=True)
    for key, value in update_data.items():
        setattr(donacion, key, value)
        
    db.commit()
    db.refresh(donacion)
    
    return {
        "mensaje": "Donación editada correctamente", 
        "status": 200, 
        "data": donacion
    }

@router.patch("/{id}")
def modificar_donacion(id: int, donacion_in: DonacionUpdate, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)):
    return actualizar_donacion_completa(id, donacion_in, db, current_user)

@router.delete("/{id}")
def eliminar_donacion(id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)):
    donacion = db.query(Donacion).filter(Donacion.Id_Donacion == id).first()
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")
        
    db.delete(donacion)
    db.commit()
    
    return {
        "mensaje": f"Donación eliminada por {current_user.Nombre}", 
        "status": 200
    }
