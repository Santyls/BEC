from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from pydantic import BaseModel
from typing import List, Optional
from datetime import date, time
from app.data.database import get_db
from app.models.models import Voluntariado, Usuario, Albergue, Campana, InscripcionVoluntariado
from app.security.security import get_current_user, get_admin_user

router = APIRouter()

#Schemas

class VoluntariadoCreate(BaseModel):
    Nombre_Voluntariado: str
    Id_albergue: Optional[int] = None
    id_campana: Optional[int] = None
    Fecha_prog: date
    Cupo_Max: Optional[int] = None
    Hora_inicio: time
    Hora_Fin: time
    Estado_Voluntariado: str
    Descripcion_Requisitos: str

class VoluntariadoUpdate(BaseModel):
    Nombre_Voluntariado: Optional[str] = None
    Id_albergue: Optional[int] = None
    id_campana: Optional[int] = None
    Fecha_prog: Optional[date] = None
    Cupo_Max: Optional[int] = None
    Hora_inicio: Optional[time] = None
    Hora_Fin: Optional[time] = None
    Estado_Voluntariado: Optional[str] = None
    Descripcion_Requisitos: Optional[str] = None

class VoluntariadoResponse(BaseModel):
    Id_Voluntariado: int
    Nombre_Voluntariado: str
    Id_albergue: Optional[int] = None
    id_campana: Optional[int] = None
    Fecha_prog: date
    Cupo_Max: Optional[int] = None
    Hora_inicio: time
    Hora_Fin: time
    Estado_Voluntariado: str
    Descripcion_Requisitos: str

    class Config:
        from_attributes = True

# --- Endpoints ---

@router.post("/", status_code=status.HTTP_201_CREATED)
def crear_voluntariado(
    voluntariado_in: VoluntariadoCreate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    # Verificaciones fk
    if voluntariado_in.Id_albergue is not None:
        albergue_existe = db.query(Albergue).filter(Albergue.Id_Albergue == voluntariado_in.Id_albergue).first()
        if not albergue_existe:
            raise HTTPException(status_code=400, detail="El Id_albergue especificado no existe")

    if voluntariado_in.id_campana is not None:
        campana_existe = db.query(Campana).filter(Campana.id_Campana == voluntariado_in.id_campana).first()
        if not campana_existe:
            raise HTTPException(status_code=400, detail="El id_campana especificado no existe")

    nuevo_voluntariado = Voluntariado(
        Nombre_Voluntariado=voluntariado_in.Nombre_Voluntariado,
        Id_albergue=voluntariado_in.Id_albergue,
        id_campana=voluntariado_in.id_campana,
        Fecha_prog=voluntariado_in.Fecha_prog,
        Cupo_Max=voluntariado_in.Cupo_Max,
        Hora_inicio=voluntariado_in.Hora_inicio,
        Hora_Fin=voluntariado_in.Hora_Fin,
        Estado_Voluntariado=voluntariado_in.Estado_Voluntariado,
        Descripcion_Requisitos=voluntariado_in.Descripcion_Requisitos
    )
    
    db.add(nuevo_voluntariado)
    db.commit()
    db.refresh(nuevo_voluntariado)
    
    return {
        "mensaje": "Voluntariado agregado correctamente", 
        "status": 201, 
        "data": nuevo_voluntariado
    }

@router.get("/")
def listar_voluntariados(db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)):
    voluntariados = db.query(Voluntariado).all()
    result = []
    for v in voluntariados:
        albergue_nombre = None
        if v.Id_albergue:
            alb = db.query(Albergue).filter(Albergue.Id_Albergue == v.Id_albergue).first()
            if alb:
                albergue_nombre = alb.Nombre_Albergue
        result.append({
            "Id_Voluntariado": v.Id_Voluntariado,
            "Nombre_Voluntariado": v.Nombre_Voluntariado,
            "Id_albergue": v.Id_albergue,
            "nombre_albergue": albergue_nombre,
            "id_campana": v.id_campana,
            "Fecha_prog": v.Fecha_prog,
            "Cupo_Max": v.Cupo_Max,
            "Hora_inicio": v.Hora_inicio,
            "Hora_Fin": v.Hora_Fin,
            "Estado_Voluntariado": v.Estado_Voluntariado,
            "Descripcion_Requisitos": v.Descripcion_Requisitos,
        })
    return result

@router.get("/{id}")
def obtener_voluntariado(id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.Id_Voluntariado == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
    return voluntariado

@router.put("/{id}")
def actualizar_voluntariado(
    id: int, 
    voluntariado_in: VoluntariadoUpdate, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_admin_user)
):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.Id_Voluntariado == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
        
    update_data = voluntariado_in.model_dump(exclude_unset=True)
    
    if "Id_albergue" in update_data and update_data["Id_albergue"] is not None:
        albergue_existe = db.query(Albergue).filter(Albergue.Id_Albergue == update_data["Id_albergue"]).first()
        if not albergue_existe:
            raise HTTPException(status_code=400, detail="El Id_albergue especificado no existe")

    if "id_campana" in update_data and update_data["id_campana"] is not None:
        campana_existe = db.query(Campana).filter(Campana.id_Campana == update_data["id_campana"]).first()
        if not campana_existe:
            raise HTTPException(status_code=400, detail="El id_campana especificado no existe")

    for key, value in update_data.items():
        setattr(voluntariado, key, value)
        
    db.commit()
    db.refresh(voluntariado)
    
    return {
        "mensaje": "Voluntariado editado correctamente", 
        "status": 200, 
        "data": voluntariado
    }

@router.patch("/{id}")
def modificar_voluntariado(id: int, voluntariado_in: VoluntariadoUpdate, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)):
    return actualizar_voluntariado(id, voluntariado_in, db, current_user)

@router.delete("/{id}")
def eliminar_voluntariado(id: int, db: Session = Depends(get_db), current_user: Usuario = Depends(get_admin_user)):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.Id_Voluntariado == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
        
    db.delete(voluntariado)
    db.commit()
    
    return {
        "mensaje": f"Voluntariado eliminado por {current_user.Nombre}", 
        "status": 200
    }

# --- Inscripciones a Voluntariados ---
@router.post("/{id}/inscribirse", status_code=status.HTTP_201_CREATED)
def inscribirse_voluntariado(
    id: int, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_current_user)
):
    voluntariado = db.query(Voluntariado).filter(Voluntariado.Id_Voluntariado == id).first()
    if not voluntariado:
        raise HTTPException(status_code=404, detail="Voluntariado no encontrado")
        
    inscripcion_existente = db.query(InscripcionVoluntariado).filter(
        InscripcionVoluntariado.Id_Usuario == current_user.id_Usuario,
        InscripcionVoluntariado.Id_Voluntariado == id
    ).first()
    
    if inscripcion_existente:
        raise HTTPException(status_code=400, detail="Ya estás inscrito a este voluntariado")
        
    nueva_inscripcion = InscripcionVoluntariado(
        Id_Usuario=current_user.id_Usuario,
        Id_Voluntariado=id
    )
    db.add(nueva_inscripcion)
    db.commit()
    
    return {"mensaje": "Inscrito exitosamente", "status": 201}

@router.delete("/{id}/cancelar-inscripcion")
def cancelar_inscripcion_voluntariado(
    id: int, 
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_current_user)
):
    inscripcion = db.query(InscripcionVoluntariado).filter(
        InscripcionVoluntariado.Id_Usuario == current_user.id_Usuario,
        InscripcionVoluntariado.Id_Voluntariado == id
    ).first()
    
    if not inscripcion:
        raise HTTPException(status_code=404, detail="No estás inscrito a este voluntariado")
        
    db.delete(inscripcion)
    db.commit()
    
    return {"mensaje": "Inscripción cancelada exitosamente", "status": 200}

@router.get("/inscripciones/me")
def mis_inscripciones(
    db: Session = Depends(get_db), 
    current_user: Usuario = Depends(get_current_user)
):
    inscripciones = db.query(InscripcionVoluntariado).filter(
        InscripcionVoluntariado.Id_Usuario == current_user.id_Usuario
    ).all()

    ids_voluntariados = [i.Id_Voluntariado for i in inscripciones]

    mis_vols = db.query(Voluntariado).filter(
        Voluntariado.Id_Voluntariado.in_(ids_voluntariados)
    ).all()

    result = []
    for v in mis_vols:
        albergue_nombre = None
        if v.Id_albergue:
            alb = db.query(Albergue).filter(Albergue.Id_Albergue == v.Id_albergue).first()
            if alb:
                albergue_nombre = alb.Nombre_Albergue
        result.append({
            "Id_Voluntariado": v.Id_Voluntariado,
            "Nombre_Voluntariado": v.Nombre_Voluntariado,
            "Id_albergue": v.Id_albergue,
            "nombre_albergue": albergue_nombre,
            "id_campana": v.id_campana,
            "Fecha_prog": v.Fecha_prog,
            "Cupo_Max": v.Cupo_Max,
            "Hora_inicio": v.Hora_inicio,
            "Hora_Fin": v.Hora_Fin,
            "Estado_Voluntariado": v.Estado_Voluntariado,
            "Descripcion_Requisitos": v.Descripcion_Requisitos,
        })
    return result
