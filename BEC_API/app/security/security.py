import hashlib
import hmac
import os
import secrets
from datetime import datetime, timedelta, timezone
from typing import Optional

from fastapi import Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer
from jose import JWTError, jwt
from passlib.context import CryptContext
from sqlalchemy.orm import Session

from app.data.database import get_db
from app.models.models import Sesion, Usuario

# --- Configuración (siempre desde variables de entorno, nunca hardcodeada) ---
SECRET_KEY = os.environ["SECRET_KEY"]
ALGORITHM = os.getenv("ALGORITHM", "HS256")
ACCESS_TOKEN_EXPIRE_MINUTES = int(os.getenv("ACCESS_TOKEN_EXPIRE_MINUTES", "60"))
REFRESH_TOKEN_EXPIRE_DAYS = int(os.getenv("REFRESH_TOKEN_EXPIRE_DAYS", "30"))

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
oauth2_scheme = OAuth2PasswordBearer(tokenUrl="auth/login")

# Roles (coinciden con el seed de la tabla `roles`)
ROL_ADMIN = 1
ROL_RECEPCIONISTA = 2
ROL_CIUDADANO = 3


# ======================================================================
# Contraseñas
# ======================================================================

def verificar_password(plain_password: str, hashed_password: str) -> bool:
    return pwd_context.verify(plain_password, hashed_password)


def obtener_password_hash(password: str) -> str:
    return pwd_context.hash(password)


# ======================================================================
# Access token (JWT, sin estado — no se guarda en BD)
# ======================================================================

def crear_token_acceso(data: dict, expires_delta: Optional[timedelta] = None) -> str:
    to_encode = data.copy()
    expire = datetime.now(timezone.utc) + (
        expires_delta if expires_delta else timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    )
    to_encode.update({"exp": expire})
    return jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)


# ======================================================================
# Refresh token (opaco, se guarda hasheado en `sesiones` para poder
# revocarlo — no es JWT porque no necesita decodificarse, solo buscarse)
# ======================================================================

def _hash_refresh_token(token: str) -> str:
    return hashlib.sha256(token.encode("utf-8")).hexdigest()


def crear_sesion_refresh(db: Session, usuario: Usuario, plataforma: Optional[str] = None) -> str:
    token = secrets.token_urlsafe(64)
    sesion = Sesion(
        usuario_id=usuario.id,
        refresh_token_hash=_hash_refresh_token(token),
        plataforma=plataforma,
        expira_en=datetime.now(timezone.utc) + timedelta(days=REFRESH_TOKEN_EXPIRE_DAYS),
        revocada=False,
    )
    db.add(sesion)
    db.commit()
    return token


def verificar_refresh_token(db: Session, token: str) -> Sesion:
    token_hash = _hash_refresh_token(token)
    sesiones = db.query(Sesion).filter(Sesion.revocada.is_(False)).all()

    sesion_valida = None
    for sesion in sesiones:
        if hmac.compare_digest(sesion.refresh_token_hash, token_hash):
            sesion_valida = sesion
            break

    invalido = HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Refresh token inválido")
    if sesion_valida is None:
        raise invalido
    if sesion_valida.expira_en.replace(tzinfo=timezone.utc) < datetime.now(timezone.utc):
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Refresh token expirado")
    return sesion_valida


def revocar_refresh_token(db: Session, token: str) -> None:
    """Usado en /auth/logout. Si el token ya no es válido, no falla — cerrar sesión debe ser idempotente."""
    token_hash = _hash_refresh_token(token)
    sesiones = db.query(Sesion).filter(Sesion.revocada.is_(False)).all()
    for sesion in sesiones:
        if hmac.compare_digest(sesion.refresh_token_hash, token_hash):
            sesion.revocada = True
            db.commit()
            return


# ======================================================================
# Dependencias de autenticación y roles
# ======================================================================

def get_current_user(token: str = Depends(oauth2_scheme), db: Session = Depends(get_db)) -> Usuario:
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="No se pudieron validar las credenciales",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        correo: str = payload.get("sub")
        if correo is None:
            raise credentials_exception
    except JWTError:
        raise credentials_exception

    usuario = db.query(Usuario).filter(Usuario.correo == correo, Usuario.activo.is_(True)).first()
    if usuario is None:
        raise credentials_exception
    return usuario


def get_admin_user(current_user: Usuario = Depends(get_current_user)) -> Usuario:
    if current_user.rol_id != ROL_ADMIN:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No tienes los permisos de Administrador necesarios.",
        )
    return current_user


def get_recepcionista_or_admin(current_user: Usuario = Depends(get_current_user)) -> Usuario:
    if current_user.rol_id not in (ROL_ADMIN, ROL_RECEPCIONISTA):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Operación permitida únicamente a Administradores o Recepcionistas.",
        )
    return current_user
