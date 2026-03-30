from fastapi import FastAPI
from app.data.database import engine
from app.models.models import Base
from app.routers import auth, usuarios, albergues, campanas, donaciones, voluntariados, predicciones
from fastapi.middleware.cors import CORSMiddleware

Base.metadata.create_all(bind=engine)

#Instancia de FastAPI
app = FastAPI(
    title="BEC API",
    description="API RESTful para manejo de albergues y donaciones",
    version="1.0.0"
)

# Configuración de CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"], # En desarrollo permitimos todo, puedes restringir a ["http://localhost:8003"]
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

#
app.include_router(auth.router)
app.include_router(usuarios.router)
app.include_router(albergues.router, prefix="/albergues", tags=["Albergues"])
app.include_router(campanas.router, prefix="/campanas", tags=["Campañas"])
app.include_router(donaciones.router, prefix="/donaciones", tags=["Donaciones"])
app.include_router(voluntariados.router, prefix="/voluntariados", tags=["Voluntariados"])
app.include_router(predicciones.router, prefix="/predicciones", tags=["Analítica ML"])

@app.get("/")
def home():
    return {"message": "Bienvenido a BEC API"}
    