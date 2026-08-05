from datetime import datetime, timedelta, timezone

from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordRequestForm
from pydantic import BaseModel, EmailStr, Field
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Usuario
from app.security import security

router = APIRouter(prefix="/auth", tags=["Autenticación"])

ROL_CIUDADANO = 3


# --- Schemas ---
class RegistroCiudadano(BaseModel):
    """Coincide exactamente con lo que captura la pantalla de Registro del móvil
    (RF03 original): nombre, apellidos, correo, contraseña y términos. Teléfono, fecha
    de nacimiento y género se completan después en Mi Perfil, no aquí."""

    nombre: str = Field(min_length=1, max_length=100)
    apellido_paterno: str = Field(min_length=1, max_length=100)
    apellido_materno: str = Field(min_length=1, max_length=100)
    correo: EmailStr
    password: str = Field(min_length=6)
    terminos_aceptados: bool


class TokenResponse(BaseModel):
    access_token: str
    refresh_token: str
    token_type: str = "bearer"


class RefreshRequest(BaseModel):
    refresh_token: str


class LogoutRequest(BaseModel):
    refresh_token: str


# --- Rutas ---

@router.post("/registro", status_code=status.HTTP_201_CREATED)
def registrar_ciudadano(datos: RegistroCiudadano, db: Session = Depends(get_db)):
    """Autorregistro público (app móvil). Siempre crea rol Ciudadano — nunca se acepta
    el rol desde el cliente, a diferencia de POST /usuarios/ que sí lo permite pero
    requiere estar autenticado como Recepcionista/Admin."""
    if not datos.terminos_aceptados:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Debes aceptar los términos y condiciones para registrarte.",
        )

    existe = db.query(Usuario).filter(Usuario.correo == datos.correo).first()
    if existe:
        raise HTTPException(status_code=400, detail="Este correo ya se encuentra registrado.")

    nuevo_usuario = Usuario(
        nombre=datos.nombre,
        apellido_paterno=datos.apellido_paterno,
        apellido_materno=datos.apellido_materno,
        correo=datos.correo,
        password_hash=security.obtener_password_hash(datos.password),
        rol_id=ROL_CIUDADANO,
        terminos_aceptados=True,
        fecha_aceptacion_terminos=datetime.now(timezone.utc),
    )
    db.add(nuevo_usuario)
    db.commit()
    return {"status": "success", "message": "Cuenta creada correctamente."}


@router.post("/login", response_model=TokenResponse)
def login(
    plataforma: str = "web",
    form_data: OAuth2PasswordRequestForm = Depends(),
    db: Session = Depends(get_db),
):
    registro_intentos = security.verificar_intentos_login(db, form_data.username)

    usuario = db.query(Usuario).filter(Usuario.correo == form_data.username, Usuario.activo.is_(True)).first()

    if not usuario or not usuario.password_hash or not security.verificar_password(
        form_data.password, usuario.password_hash
    ):
        security.registrar_intento_fallido(db, registro_intentos)
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Correo o contraseña incorrectos",
            headers={"WWW-Authenticate": "Bearer"},
        )

    security.registrar_intento_exitoso(db, registro_intentos)

    access_token = security.crear_token_acceso(
        data={"sub": usuario.correo, "rol": usuario.rol_id},
        expires_delta=timedelta(minutes=security.ACCESS_TOKEN_EXPIRE_MINUTES),
    )
    refresh_token = security.crear_sesion_refresh(db, usuario, plataforma=plataforma)

    return TokenResponse(access_token=access_token, refresh_token=refresh_token)


@router.post("/refresh", response_model=TokenResponse)
def refrescar_token(datos: RefreshRequest, db: Session = Depends(get_db)):
    """Rota el refresh token en cada uso: el viejo queda revocado y se entrega uno nuevo,
    junto con un access token nuevo. Así una fuga de un refresh token viejo no sirve de nada."""
    sesion = security.verificar_refresh_token(db, datos.refresh_token)
    usuario = sesion.usuario

    sesion.revocada = True
    db.commit()

    access_token = security.crear_token_acceso(
        data={"sub": usuario.correo, "rol": usuario.rol_id},
        expires_delta=timedelta(minutes=security.ACCESS_TOKEN_EXPIRE_MINUTES),
    )
    nuevo_refresh = security.crear_sesion_refresh(db, usuario, plataforma=sesion.plataforma)

    return TokenResponse(access_token=access_token, refresh_token=nuevo_refresh)


@router.post("/logout", status_code=status.HTTP_204_NO_CONTENT)
def logout(datos: LogoutRequest, db: Session = Depends(get_db)):
    security.revocar_refresh_token(db, datos.refresh_token)
    return None


# No hay endpoints públicos de recuperación de contraseña.
#
# El envío de códigos por correo se retiró: obligaba a mantener credenciales de un
# servidor SMTP y dejaba dos rutas sin autenticar expuestas a internet. En BEC el
# restablecimiento lo hace personal identificado — Recepcionista o Admin, vía
# PUT /usuarios/{id}, que sí exige token y rol. La app móvil solo muestra una
# pantalla informativa indicando acudir a recepción.
