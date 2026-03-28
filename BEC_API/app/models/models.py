from sqlalchemy import Column, Integer, String, BigInteger, Float, Date, Time, Text, ForeignKey, TIMESTAMP, Enum as SAEnum
from sqlalchemy.sql import func
import enum
from app.data.database import Base

# --- Enums de Python ---
class CategoriaDonacion(str, enum.Enum):
    ROPA = 'Ropa'
    ALIMENTOS = 'Alimentos'
    HIGIENE = 'Higiene personal'
    MEDICAMENTOS = 'Medicamentos'

class CondicionDonacion(str, enum.Enum):
    NUEVO = 'Nuevo/Sellado'
    BUEN_ESTADO = 'Buen estado (Usado)'
    REGULAR = 'Regular'

class UnidadMedida(str, enum.Enum):
    CAJAS = 'Cajas'
    PIEZAS = 'Piezas'
    KG = 'Kilogramos (kg)'
    MG = 'Miligramos (mg)'
    L = 'Litros (L)'
    ML = 'Mililitros (ml)'

class EstadoCampana(str, enum.Enum):
    PROGRAMADA = 'Programada (Futura)'
    ACTIVA = 'Activa'
    FINALIZADA = 'Finalizada'

class EstadoVoluntariado(str, enum.Enum):
    ACTIVO = 'Activo/Próximo'
    CANCELADO = 'Cancelado'
    FINALIZADO = 'Finalizado'

# --- Modelos SQLAlchemy ---
class Usuario(Base):
    __tablename__ = "usuarios"

    id_Usuario = Column(Integer, primary_key=True, index=True)
    Nombre = Column(String(100), nullable=False)
    Apellido_P = Column(String(100), nullable=False)
    Apellido_M = Column(String(100), nullable=False)
    Correo = Column(String(100), unique=True, index=True, nullable=False)
    Password = Column(String(250), nullable=False)
    Edad = Column(Integer, nullable=False)
    Id_Direccion = Column(Integer, nullable=False)
    Id_Albergue = Column(Integer, ForeignKey("albergues.Id_Albergue"), nullable=True)
    Id_Rol = Column(Integer, nullable=False)
    Tel = Column(BigInteger, nullable=False)
    Id_Genero = Column(Integer, nullable=False)
    Fecha_Registro = Column(TIMESTAMP(timezone=True), server_default=func.now())

class Albergue(Base):
    __tablename__ = "albergues"

    Id_Albergue = Column(Integer, primary_key=True, index=True)
    Capacidad_Max = Column(Integer, nullable=False)
    Tel_Contacto = Column(BigInteger, nullable=False)
    Id_Direccion = Column(Integer, nullable=False)

class Donacion(Base):
    __tablename__ = "donaciones"

    Id_Donacion = Column(Integer, primary_key=True, index=True)
    Id_Usuario = Column(Integer, ForeignKey("usuarios.id_Usuario"), nullable=False)
    id_Categoria = Column(SAEnum(CategoriaDonacion), nullable=False)
    Id_Condicion = Column(SAEnum(CondicionDonacion), nullable=False)
    Cantidad = Column(Float, nullable=False)
    Id_Unidad = Column(SAEnum(UnidadMedida), nullable=False)
    Marca = Column(String(100), nullable=True)
    Id_Albergue = Column(Integer, ForeignKey("albergues.Id_Albergue"), nullable=False)

class Campana(Base):
    __tablename__ = "campanas"

    id_Campana = Column(Integer, primary_key=True, index=True)
    Fecha_Inicio = Column(Date, nullable=False)
    Fecha_Fin = Column(Date, nullable=False)
    id_Estado_campana = Column(SAEnum(EstadoCampana), nullable=False)
    Descripcion_Objetivos = Column(Text, nullable=False)

class Voluntariado(Base):
    __tablename__ = "voluntariados"

    Id_Voluntariado = Column(Integer, primary_key=True, index=True)
    Nombre_Voluntariado = Column(String(150), nullable=False)
    Id_albergue = Column(Integer, ForeignKey("albergues.Id_Albergue"), nullable=True)
    id_campana = Column(Integer, ForeignKey("campanas.id_Campana"), nullable=True)
    Fecha_prog = Column(Date, nullable=False)
    Cupo_Max = Column(Integer, nullable=True)
    Hora_inicio = Column(Time, nullable=False)
    Hora_Fin = Column(Time, nullable=False)
    Estado_Voluntariado = Column(SAEnum(EstadoVoluntariado), nullable=False)
    Descripcion_Requisitos = Column(Text, nullable=False)
