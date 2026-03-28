from fastapi import FastAPI
from app.data.database import engine
from app.models.models import Base
from app.routers import auth, usuarios

# 1. Crear las tablas de la DB si no existen
Base.metadata.create_all(bind=engine)

# 2. Instancia de FastAPI
app = FastAPI(
    title="BEC API",
    description="API RESTful para manejo de albergues y donaciones",
    version="1.0.0"
)

# 3. Incluir las Rutas (Routers)
app.include_router(auth.router)
app.include_router(usuarios.router)

@app.get("/")
def home():
    return {"message": "Bienvenido a BEC API"}