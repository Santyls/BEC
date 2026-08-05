from sqlalchemy import (
    Column,
    Integer,
    String,
    Float,
    Boolean,
    Date,
    Time,
    Text,
    ForeignKey,
    TIMESTAMP,
    UniqueConstraint,
)
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.data.database import Base


class TimestampMixin:
    """created_at / updated_at para toda tabla que lo necesite (ver §7.7 del modelo de BD)."""

    created_at = Column(TIMESTAMP(timezone=True), server_default=func.now(), nullable=False)
    updated_at = Column(
        TIMESTAMP(timezone=True), server_default=func.now(), onupdate=func.now(), nullable=False
    )


# ======================================================================
# Catálogos de dominio
# ======================================================================

class Rol(Base):
    __tablename__ = "roles"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


class Genero(Base):
    __tablename__ = "generos"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


class Categoria(Base):
    __tablename__ = "categorias"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


class Unidad(Base):
    __tablename__ = "unidades"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


class Condicion(Base):
    """Reemplaza la columna de texto libre Id_Condicion del esquema viejo (§7.5)."""

    __tablename__ = "condiciones"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


class EstadoCampana(Base):
    __tablename__ = "estados_campanas"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


class EstadoVoluntariado(Base):
    """Ciclo de vida del EVENTO (lo controla el admin/recepcionista): Programado, Activo, Finalizado, Cancelado."""

    __tablename__ = "estados_voluntariado"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


class EstadoInscripcion(Base):
    """Estado de la participación de UN ciudadano en un voluntariado: Próximo, Completado, Cancelado.
    Es el que consume la app móvil (pantalla Mis Voluntariados)."""

    __tablename__ = "estados_inscripcion"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


# ======================================================================
# Geografía — solo "estados" va normalizado (ver decisión con el equipo).
# municipio/colonia quedan como texto libre en direcciones, y
# codigos_postales es una tabla de solo consulta (seed desde SEPOMEX)
# para autocompletar por CP, no una FK.
# ======================================================================

class Estado(Base):
    __tablename__ = "estados"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(50), nullable=False, unique=True)


class CodigoPostal(Base):
    __tablename__ = "codigos_postales"

    id = Column(Integer, primary_key=True)
    codigo_postal = Column(String(5), nullable=False, index=True)
    estado_id = Column(Integer, ForeignKey("estados.id"), nullable=False)
    municipio = Column(String(100), nullable=False)
    colonia = Column(String(150), nullable=False)

    estado = relationship("Estado")


class Direccion(Base, TimestampMixin):
    __tablename__ = "direcciones"

    id = Column(Integer, primary_key=True)
    estado_id = Column(Integer, ForeignKey("estados.id"), nullable=False)
    municipio = Column(String(100), nullable=False)
    colonia = Column(String(150), nullable=False)
    calle = Column(String(150), nullable=False)
    numero_exterior = Column(String(20), nullable=False)
    numero_interior = Column(String(20), nullable=True)
    codigo_postal = Column(String(5), nullable=False)

    estado = relationship("Estado")


# ======================================================================
# Entidades principales
# ======================================================================

class Albergue(Base, TimestampMixin):
    __tablename__ = "albergues"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(150), nullable=False)
    capacidad_max = Column(Integer, nullable=False)
    telefono = Column(String(20), nullable=False)
    direccion_id = Column(Integer, ForeignKey("direcciones.id"), nullable=False)
    activo = Column(Boolean, nullable=False, default=True)
    # Se estampa al desactivar; limpia al reactivar. Da pie a la purga automática
    # a los 30 días (ver purgar_albergues_vencidos en albergues.py).
    fecha_desactivacion = Column(TIMESTAMP(timezone=True), nullable=True)

    direccion = relationship("Direccion")


class Usuario(Base, TimestampMixin):
    __tablename__ = "usuarios"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(100), nullable=False)
    apellido_paterno = Column(String(100), nullable=False)
    apellido_materno = Column(String(100), nullable=False)
    # correo/password/fecha_nacimiento/genero son NULL para el "alta rápida" que hace el
    # recepcionista en el mostrador (BEC_PRF): ahí solo se captura nombre, apellidos y
    # teléfono, sin cuenta con la que se pueda iniciar sesión. Cuando el ciudadano se
    # autorregistra (móvil) sí llegan completos. UNIQUE en correo funciona igual con NULL:
    # Postgres permite múltiples filas con correo NULL sin violar la restricción.
    correo = Column(String(100), nullable=True, unique=True, index=True)
    password_hash = Column(String(250), nullable=True)
    fecha_nacimiento = Column(Date, nullable=True)
    # Tampoco es obligatorio al autorregistrarse desde el móvil (RF03 original: solo
    # nombre/apellidos/correo/contraseña/términos); se completa después en Mi Perfil.
    telefono = Column(String(20), nullable=True)
    direccion_id = Column(Integer, ForeignKey("direcciones.id"), nullable=True)
    albergue_id = Column(Integer, ForeignKey("albergues.id"), nullable=True)  # solo staff (recepcionista)
    rol_id = Column(Integer, ForeignKey("roles.id"), nullable=False)
    genero_id = Column(Integer, ForeignKey("generos.id"), nullable=True)
    foto_url = Column(String(255), nullable=True)
    terminos_aceptados = Column(Boolean, nullable=False, default=False)
    fecha_aceptacion_terminos = Column(TIMESTAMP(timezone=True), nullable=True)
    fecha_registro = Column(TIMESTAMP(timezone=True), server_default=func.now(), nullable=False)
    activo = Column(Boolean, nullable=False, default=True)
    # Se estampa al desactivar; limpia al reactivar. Da pie a la purga automática
    # a los 30 días (ver purgar_usuarios_vencidos en usuarios.py).
    fecha_desactivacion = Column(TIMESTAMP(timezone=True), nullable=True)
    # Veto general de cuenta: bloquea nuevas inscripciones a voluntariados
    # (no cancela las que ya tenía). Lo activa un Admin desde el panel.
    vetado = Column(Boolean, nullable=False, default=False)
    motivo_veto = Column(Text, nullable=True)
    # SIN USO. Sobran de la recuperación de contraseña por correo, que se retiró:
    # ahora la restablece Recepción o un Admin vía PUT /usuarios/{id}. Se dejan las
    # columnas (nullable, siempre NULL) en vez de borrarlas con una migración porque
    # no estorban; si algún día se limpia el esquema, van fuera junto con esta nota.
    reset_password_codigo_hash = Column(String(64), nullable=True)
    reset_password_expira_en = Column(TIMESTAMP(timezone=True), nullable=True)

    direccion = relationship("Direccion")
    albergue = relationship("Albergue")
    rol = relationship("Rol")
    genero = relationship("Genero")


class IntentoLogin(Base):
    """Control de fuerza bruta por correo (independiente de la plataforma que lo use:
    BEC_PAL, BEC_PRF o el móvil, todos pasan por /auth/login). Ver security.py."""

    __tablename__ = "intentos_login"

    id = Column(Integer, primary_key=True)
    correo = Column(String(100), nullable=False, unique=True, index=True)
    intentos = Column(Integer, nullable=False, default=0)
    estado = Column(String(20), nullable=False, default="normal")  # normal | espera_corta | espera_larga
    bloqueado_hasta = Column(TIMESTAMP(timezone=True), nullable=True)
    actualizado_en = Column(
        TIMESTAMP(timezone=True), server_default=func.now(), onupdate=func.now(), nullable=False
    )


class Sesion(Base):
    """Refresh tokens. El access token es autocontenido (JWT) y no se guarda aquí;
    solo el refresh token vive en BD (hasheado) para poder revocarlo/rotarlo."""

    __tablename__ = "sesiones"

    id = Column(Integer, primary_key=True)
    usuario_id = Column(Integer, ForeignKey("usuarios.id"), nullable=False)
    refresh_token_hash = Column(String(255), nullable=False, unique=True, index=True)
    plataforma = Column(String(30), nullable=True)  # 'mobile', 'web', etc. (informativo)
    creado_en = Column(TIMESTAMP(timezone=True), server_default=func.now(), nullable=False)
    expira_en = Column(TIMESTAMP(timezone=True), nullable=False)
    revocada = Column(Boolean, nullable=False, default=False)

    usuario = relationship("Usuario")


class Campana(Base, TimestampMixin):
    __tablename__ = "campanas"

    id = Column(Integer, primary_key=True)
    nombre = Column(String(150), nullable=False)
    fecha_inicio = Column(Date, nullable=False)
    fecha_fin = Column(Date, nullable=False)
    estado_id = Column(Integer, ForeignKey("estados_campanas.id"), nullable=False)
    descripcion_objetivos = Column(Text, nullable=False)

    estado = relationship("EstadoCampana")


class Voluntariado(Base, TimestampMixin):
    __tablename__ = "voluntariados"

    id = Column(Integer, primary_key=True)
    nombre_programa = Column(String(150), nullable=False)
    albergue_id = Column(Integer, ForeignKey("albergues.id"), nullable=True)
    campana_id = Column(Integer, ForeignKey("campanas.id"), nullable=True)
    # Ubicación libre: cubre el caso donde la actividad no ocurre en un albergue
    # registrado (ej. jornada en un parque). Si hay albergue_id, se puede usar como
    # referencia adicional (ej. "Patio trasero", "Entrada principal").
    ubicacion = Column(String(255), nullable=True)
    fecha_programada = Column(Date, nullable=False)
    cupo_maximo = Column(Integer, nullable=True)
    hora_inicio = Column(Time, nullable=False)
    hora_fin = Column(Time, nullable=False)
    estado_id = Column(Integer, ForeignKey("estados_voluntariado.id"), nullable=False)
    descripcion_requisitos = Column(Text, nullable=False)

    albergue = relationship("Albergue")
    campana = relationship("Campana")
    estado = relationship("EstadoVoluntariado")


class InscripcionVoluntariado(Base):
    __tablename__ = "inscripciones_voluntariados"
    __table_args__ = (
        UniqueConstraint("usuario_id", "voluntariado_id", name="uq_inscripcion_usuario_voluntariado"),
    )

    id = Column(Integer, primary_key=True)
    # Nullable: si el usuario se elimina permanentemente (tras 30 días desactivado o a
    # petición del admin), esta fila se conserva con usuario_id=NULL para no perder el
    # historial de participación del voluntariado, solo se pierde la identidad.
    usuario_id = Column(Integer, ForeignKey("usuarios.id"), nullable=True)
    voluntariado_id = Column(Integer, ForeignKey("voluntariados.id"), nullable=False)
    estado_id = Column(Integer, ForeignKey("estados_inscripcion.id"), nullable=False)
    fecha_inscripcion = Column(TIMESTAMP(timezone=True), server_default=func.now(), nullable=False)
    fecha_cancelacion = Column(TIMESTAMP(timezone=True), nullable=True)

    usuario = relationship("Usuario")
    voluntariado = relationship("Voluntariado")
    estado = relationship("EstadoInscripcion")


class VoluntariadoEncargado(Base):
    """Recepcionista(s) asignados como responsables de un voluntariado: son quienes
    pueden pasar lista de asistencia y finalizar el evento (ver security.py). Un
    voluntariado puede tener cero, uno o varios encargados."""

    __tablename__ = "voluntariados_encargados"
    __table_args__ = (
        UniqueConstraint("voluntariado_id", "usuario_id", name="uq_encargado_voluntariado_usuario"),
    )

    id = Column(Integer, primary_key=True)
    voluntariado_id = Column(Integer, ForeignKey("voluntariados.id"), nullable=False)
    usuario_id = Column(Integer, ForeignKey("usuarios.id"), nullable=False)
    asignado_en = Column(TIMESTAMP(timezone=True), server_default=func.now(), nullable=False)

    voluntariado = relationship("Voluntariado")
    usuario = relationship("Usuario")


class Donacion(Base):
    __tablename__ = "donaciones"

    id = Column(Integer, primary_key=True)
    usuario_id = Column(Integer, ForeignKey("usuarios.id"), nullable=True)  # NULL = donación anónima
    categoria_id = Column(Integer, ForeignKey("categorias.id"), nullable=False)
    condicion_id = Column(Integer, ForeignKey("condiciones.id"), nullable=False)
    cantidad = Column(Float, nullable=False)
    unidad_id = Column(Integer, ForeignKey("unidades.id"), nullable=False)
    marca = Column(String(100), nullable=True)
    # Nullable: si el albergue destino se elimina permanentemente, la donación se
    # conserva (con albergue_id=NULL) en vez de perderse — el monto/categoría siguen
    # siendo parte del historial aunque ya no exista el albergue.
    albergue_id = Column(Integer, ForeignKey("albergues.id"), nullable=True)
    fecha_donacion = Column(Date, server_default=func.current_date(), nullable=False)

    usuario = relationship("Usuario")
    categoria = relationship("Categoria")
    condicion = relationship("Condicion")
    unidad = relationship("Unidad")
    albergue = relationship("Albergue")


class Noticia(Base):
    """Contenido informativo que consume la sección 'Noticias BEC' del móvil. Por ahora
    solo se leen (seed manual) — no hay CRUD para crearlas desde ningún portal todavía."""

    __tablename__ = "noticias"

    id = Column(Integer, primary_key=True)
    titulo = Column(String(150), nullable=False)
    resumen = Column(String(255), nullable=False)
    contenido = Column(Text, nullable=False)
    fecha = Column(Date, server_default=func.current_date(), nullable=False)
