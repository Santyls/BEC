from sqlalchemy import Column, Integer, String, BigInteger, Float, Date, Time, Text, ForeignKey, TIMESTAMP
from sqlalchemy.sql import func
from app.data.database import Base

#Modelos de las categorias 

class Rol(Base):
    __tablename__ = "roles"
    Id_Rol = Column(Integer, primary_key=True, index=True)
    Nombre_Rol = Column(String(50), nullable=False)

class Estado_Rep(Base):
    __tablename__ = "estados_rep"
    Id_Estado = Column(Integer, primary_key=True, index=True)
    Nombre_Estado = Column(String(50), nullable=False)

class Genero(Base):
    __tablename__ = "generos"
    Id_Genero = Column(Integer, primary_key=True, index=True)
    Nombre_genero = Column(String(50), nullable=False)

class Municipio(Base):
    __tablename__ = "municipios"
    Id_Municipio = Column(Integer, primary_key=True, index=True)
    Nombre_Municipio = Column(String(50), nullable=False)
    Id_Estado = Column(Integer, ForeignKey("estados_rep.Id_Estado"), nullable=False)

class Colonia(Base):
    __tablename__ = "colonias"
    Id_Colonia = Column(Integer, primary_key=True, index=True)
    Nombre_Colonia = Column(String(100), nullable=False)
    Id_mucipio = Column(Integer, ForeignKey("municipios.Id_Municipio"), nullable=False)
    Cp = Column(Integer, nullable=False)

class Direccion(Base):
    __tablename__ = "direcciones"
    Id_Direccion = Column(Integer, primary_key=True, index=True, autoincrement=True)
    Id_Colonia = Column(Integer, ForeignKey("colonias.Id_Colonia"), nullable=False)
    Calle = Column(String(100), nullable=False)
    No_exterior = Column(String(20), nullable=False)

class Unidad(Base):
    __tablename__ = "unidades"
    Id_Unidad = Column(Integer, primary_key=True, index=True)
    Nombre_Unidad = Column(String(50), nullable=False)

class Categoria(Base):
    __tablename__ = "categorias"
    Id_Categoria = Column(Integer, primary_key=True, index=True)
    Nombre_Categoria = Column(String(50), nullable=False)

class EstadoCampana(Base):
    __tablename__ = "estados_campanas"
    Id_Estado_Campana = Column(Integer, primary_key=True, index=True)
    Nombre_Estado = Column(String(50), nullable=False)

#Modelos Principales

class Usuario(Base):
    __tablename__ = "usuarios"
    id_Usuario = Column(Integer, primary_key=True, index=True)
    Nombre = Column(String(100), nullable=False)
    Apellido_P = Column(String(100), nullable=False)
    Apellido_M = Column(String(100), nullable=False)
    Correo = Column(String(100), unique=True, index=True, nullable=False)
    Password = Column(String(250), nullable=False)
    Edad = Column(Integer, nullable=False)
    Id_Direccion = Column(Integer, ForeignKey("direcciones.Id_Direccion"), nullable=True)  # Opcional — dirección del usuario
    Id_Albergue = Column(Integer, ForeignKey("albergues.Id_Albergue"), nullable=True)
    Id_Rol = Column(Integer, nullable=False)        # FK lógica a roles
    Tel = Column(BigInteger, nullable=False)
    Id_Genero = Column(Integer, nullable=False)     # FK lógica a generos
    Fecha_Registro = Column(TIMESTAMP(timezone=True), server_default=func.now())

class Albergue(Base):
    __tablename__ = "albergues"
    Id_Albergue = Column(Integer, primary_key=True, index=True)
    Nombre_Albergue = Column(String(150), nullable=False)
    Capacidad_Max = Column(Integer, nullable=False)
    Tel_Contacto = Column(BigInteger, nullable=False)
    Id_Direccion = Column(Integer, nullable=False) # FK lógica a direcciones

class Campana(Base):
    __tablename__ = "campanas"
    id_Campana = Column(Integer, primary_key=True, index=True)
    Nombre_Campana = Column(String(150), nullable=False)
    Fecha_Inicio = Column(Date, nullable=False)
    Fecha_Fin = Column(Date, nullable=False)
    id_Estado_campana = Column(Integer, ForeignKey("estados_campanas.Id_Estado_Campana"), nullable=False)
    Descripcion_Objetivos = Column(Text, nullable=False)

class Donacion(Base):
    __tablename__ = "donaciones"
    Id_Donacion = Column(Integer, primary_key=True, index=True)
    Id_Usuario = Column(Integer, ForeignKey("usuarios.id_Usuario"), nullable=False)
    id_Categoria = Column(Integer, ForeignKey("categorias.Id_Categoria"), nullable=False)
    Id_Condicion = Column(String(50), nullable=False)
    Cantidad = Column(Float, nullable=False)
    Id_Unidad = Column(Integer, ForeignKey("unidades.Id_Unidad"), nullable=False)
    Marca = Column(String(100), nullable=True)
    Id_Albergue = Column(Integer, ForeignKey("albergues.Id_Albergue"), nullable=False)
    Fecha_Donacion = Column(Date, server_default=func.current_date(), nullable=False)

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
    Estado_Voluntariado = Column(String(50), nullable=False)
    Descripcion_Requisitos = Column(Text, nullable=False)
