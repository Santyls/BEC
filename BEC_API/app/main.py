import os

from fastapi import FastAPI, HTTPException, status
from fastapi.staticfiles import StaticFiles
from sqlalchemy import text

from app.data.database import SessionLocal
from app.routers import (
    albergues,
    auth,
    campanas,
    catalogos,
    donaciones,
    inscripciones,
    usuarios,
    voluntariados,
)

# El esquema lo maneja Alembic (ver /alembic), no Base.metadata.create_all():
# así el modelo en código y la BD real nunca se desincronizan (ver §8 del
# análisis de la BD vieja).

app = FastAPI(
    title="BEC API",
    description="API RESTful para manejo de albergues, donaciones y voluntariados",
    version="2.0.0",
)

app.include_router(auth.router)
app.include_router(usuarios.router)
app.include_router(catalogos.router)
app.include_router(albergues.router)
app.include_router(campanas.router)
app.include_router(voluntariados.router)
app.include_router(inscripciones.router)
app.include_router(donaciones.router)

os.makedirs("uploads/usuarios", exist_ok=True)
app.mount("/uploads", StaticFiles(directory="uploads"), name="uploads")


@app.get("/")
def home():
    return {"message": "Bienvenido a BEC API"}


@app.get("/health")
def health():
    """Usado por HAProxy (httpchk) para sacar de rotación una instancia con la BD caída.
    Debe devolver un status code distinto de 200 si falla, no solo un cuerpo distinto —
    HAProxy revisa `http-check expect status 200`, no el JSON."""
    try:
        db = SessionLocal()
        db.execute(text("SELECT 1"))
        db.close()
        return {"status": "ok", "db": "up"}
    except Exception:
        raise HTTPException(status_code=status.HTTP_503_SERVICE_UNAVAILABLE, detail="db down")
