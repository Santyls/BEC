import os
from sqlalchemy import create_engine
from sqlalchemy.orm import declarative_base, sessionmaker

# Aquí leemos la variable que configuraste en el docker-compose
SQLALCHEMY_DATABASE_URL = os.getenv("DATABASE_URL")

# Creamos el motor de la base de datos
engine = create_engine(SQLALCHEMY_DATABASE_URL)

# Creamos la sesión
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# Clase base para nuestros modelos
Base = declarative_base()

# Dependencia para obtener la base de datos en los endpoints
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()