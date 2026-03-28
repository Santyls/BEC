from datetime import timedelta
from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy.orm import Session
from app.data.database import get_db
from app.models.models import Usuario
import app.security.security as security

router = APIRouter(tags=["Autenticación"])

@router.post("/login")
def login(form_data: OAuth2PasswordRequestForm = Depends(), db: Session = Depends(get_db)):
    # Buscamos al usuario por correo (username en OAuth2PasswordRequestForm representa el Correo)
    usuario = db.query(Usuario).filter(Usuario.Correo == form_data.username).first()
    
    if not usuario or not security.verificar_password(form_data.password, usuario.Password):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Correo o contraseña incorrectos",
            headers={"WWW-Authenticate": "Bearer"},
        )
    
    # Generar Token JWT
    access_token_expires = timedelta(minutes=security.ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = security.crear_token_acceso(
        data={"sub": usuario.Correo, "rol": usuario.Id_Rol},
        expires_delta=access_token_expires
    )
    
    return {"access_token": access_token, "token_type": "bearer"}
