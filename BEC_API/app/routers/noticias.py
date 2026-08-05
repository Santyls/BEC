from datetime import date
from typing import List

from fastapi import APIRouter, Depends
from pydantic import BaseModel
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Noticia, Usuario
from app.security.security import get_current_user

router = APIRouter(prefix="/noticias", tags=["Noticias"])


class NoticiaResponse(BaseModel):
    id: int
    titulo: str
    resumen: str
    contenido: str
    fecha: date

    class Config:
        from_attributes = True


@router.get("/", response_model=List[NoticiaResponse])
def listar_noticias(db: Session = Depends(get_db), current_user: Usuario = Depends(get_current_user)):
    """Lo que llena la sección 'Noticias BEC' del móvil. Sin CRUD todavía — se capturan
    a mano (ver seed.py) hasta que se decida quién y desde dónde las publica."""
    return db.query(Noticia).order_by(Noticia.fecha.desc()).all()
