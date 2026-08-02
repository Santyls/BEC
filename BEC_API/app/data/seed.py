"""Seed de catálogos + datos mínimos de prueba.

Uso (dentro del contenedor, una vez que las migraciones ya corrieron):
    docker compose run --rm bec_api python -m app.data.seed

Es idempotente: si ya existen filas en una tabla, no la vuelve a sembrar.
"""

from app.data.database import SessionLocal
from app.models.models import (
    Albergue,
    Categoria,
    Condicion,
    Direccion,
    Estado,
    EstadoCampana,
    EstadoInscripcion,
    EstadoVoluntariado,
    CodigoPostal,
    Genero,
    Rol,
    Unidad,
    Usuario,
)
from app.security.security import obtener_password_hash

ESTADOS_MEXICO = [
    "Aguascalientes", "Baja California", "Baja California Sur", "Campeche", "Chiapas",
    "Chihuahua", "Ciudad de México", "Coahuila", "Colima", "Durango", "Estado de México",
    "Guanajuato", "Guerrero", "Hidalgo", "Jalisco", "Michoacán", "Morelos", "Nayarit",
    "Nuevo León", "Oaxaca", "Puebla", "Querétaro", "Quintana Roo", "San Luis Potosí",
    "Sinaloa", "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala", "Veracruz", "Yucatán",
    "Zacatecas",
]


def sembrar_si_vacio(db, modelo, filas):
    if db.query(modelo).first():
        return
    db.bulk_insert_mappings(modelo, filas)
    db.commit()


def seed():
    db = SessionLocal()
    try:
        sembrar_si_vacio(db, Rol, [
            {"id": 1, "nombre": "Admin"},
            {"id": 2, "nombre": "Recepcionista"},
            {"id": 3, "nombre": "Ciudadano"},
        ])

        sembrar_si_vacio(db, Genero, [
            {"id": 1, "nombre": "Masculino"},
            {"id": 2, "nombre": "Femenino"},
            {"id": 3, "nombre": "Otro"},
        ])

        sembrar_si_vacio(db, Categoria, [
            {"id": 1, "nombre": "Ropa"},
            {"id": 2, "nombre": "Alimentos"},
            {"id": 3, "nombre": "Cobijas"},
            {"id": 4, "nombre": "Higiene personal"},
            {"id": 5, "nombre": "Medicamentos"},
        ])

        sembrar_si_vacio(db, Unidad, [
            {"id": 1, "nombre": "Piezas"},
            {"id": 2, "nombre": "Kilogramos (kg)"},
            {"id": 3, "nombre": "Litros (L)"},
            {"id": 4, "nombre": "Cajas"},
            {"id": 5, "nombre": "Paquetes"},
        ])

        sembrar_si_vacio(db, Condicion, [
            {"id": 1, "nombre": "Nuevo/Sellado"},
            {"id": 2, "nombre": "Buen estado (Usado)"},
            {"id": 3, "nombre": "Regular"},
        ])

        sembrar_si_vacio(db, EstadoCampana, [
            {"id": 1, "nombre": "Programada (Futura)"},
            {"id": 2, "nombre": "Activa"},
            {"id": 3, "nombre": "Finalizada"},
        ])

        sembrar_si_vacio(db, EstadoVoluntariado, [
            {"id": 1, "nombre": "Programado"},
            {"id": 2, "nombre": "Activo"},
            {"id": 3, "nombre": "Finalizado"},
            {"id": 4, "nombre": "Cancelado"},
        ])

        sembrar_si_vacio(db, EstadoInscripcion, [
            {"id": 1, "nombre": "Próximo"},
            {"id": 2, "nombre": "Completado"},
            {"id": 3, "nombre": "Cancelado"},
        ])

        sembrar_si_vacio(
            db, Estado, [{"id": i + 1, "nombre": nombre} for i, nombre in enumerate(ESTADOS_MEXICO)]
        )
        db.commit()
        id_queretaro = db.query(Estado).filter(Estado.nombre == "Querétaro").first().id

        # Muestra de códigos postales (Querétaro) para probar el autocompletado por CP.
        # La importación completa del catálogo SEPOMEX queda como tarea aparte.
        sembrar_si_vacio(db, CodigoPostal, [
            {"codigo_postal": "76000", "estado_id": id_queretaro, "municipio": "Santiago de Querétaro", "colonia": "Centro Histórico"},
            {"codigo_postal": "76140", "estado_id": id_queretaro, "municipio": "Santiago de Querétaro", "colonia": "Menchaca"},
            {"codigo_postal": "76030", "estado_id": id_queretaro, "municipio": "Santiago de Querétaro", "colonia": "San Francisquito"},
            {"codigo_postal": "76038", "estado_id": id_queretaro, "municipio": "Santiago de Querétaro", "colonia": "Epigmenio González"},
            {"codigo_postal": "76020", "estado_id": id_queretaro, "municipio": "Santiago de Querétaro", "colonia": "La Negreta"},
            {"codigo_postal": "76246", "estado_id": id_queretaro, "municipio": "El Marqués", "colonia": "Zaragoza"},
        ])

        # Direcciones + albergues de ejemplo
        if not db.query(Albergue).first():
            dir1 = Direccion(estado_id=id_queretaro, municipio="Santiago de Querétaro", colonia="Centro Histórico", calle="Av. Reforma", numero_exterior="120", codigo_postal="76000")
            dir2 = Direccion(estado_id=id_queretaro, municipio="Santiago de Querétaro", colonia="San Francisquito", calle="Calle Morelos", numero_exterior="45", codigo_postal="76030")
            db.add_all([dir1, dir2])
            db.flush()

            db.add_all([
                Albergue(nombre="Albergue Yimpathí", capacidad_max=80, telefono="4421234567", direccion_id=dir1.id),
                Albergue(nombre="Centro de Día Meni", capacidad_max=50, telefono="4429876543", direccion_id=dir2.id),
            ])
            db.commit()

        # Usuarios de prueba (mismas credenciales que la versión anterior, para continuidad del equipo)
        if not db.query(Usuario).filter(Usuario.correo == "admin@bec.com").first():
            db.add(Usuario(
                nombre="Admin", apellido_paterno="BEC", apellido_materno="Sistema",
                correo="admin@bec.com", password_hash=obtener_password_hash("admin123"),
                telefono="4420000000", rol_id=1, terminos_aceptados=True,
            ))
        if not db.query(Usuario).filter(Usuario.correo == "recepcion@yimpathi.org").first():
            albergue = db.query(Albergue).filter(Albergue.nombre == "Albergue Yimpathí").first()
            db.add(Usuario(
                nombre="Recepción", apellido_paterno="Yimpathí", apellido_materno="",
                correo="recepcion@yimpathi.org", password_hash=obtener_password_hash("recep123"),
                telefono="4421234567", rol_id=2, albergue_id=albergue.id if albergue else None,
                terminos_aceptados=True,
            ))
        if not db.query(Usuario).filter(Usuario.correo == "juan.perez@gmail.com").first():
            db.add(Usuario(
                nombre="Juan", apellido_paterno="Pérez", apellido_materno="García",
                correo="juan.perez@gmail.com", password_hash=obtener_password_hash("ciudadano123"),
                telefono="4425551234", rol_id=3, terminos_aceptados=True,
            ))
        db.commit()

        print("Seed completado.")
    finally:
        db.close()


if __name__ == "__main__":
    seed()
