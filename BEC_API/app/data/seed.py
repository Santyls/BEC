"""Seed de catálogos + datos mínimos de prueba.

Uso (dentro del contenedor, una vez que las migraciones ya corrieron):
    docker compose run --rm bec_api python -m app.data.seed

Es idempotente: si ya existen filas en una tabla, no la vuelve a sembrar.
"""

import csv
import os
from datetime import date

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
    Noticia,
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


RUTA_CSV_SEPOMEX = os.path.join(os.path.dirname(__file__), "sepomex", "codigos_postales.csv")

# El catálogo fuente (SEPOMEX) usa el nombre oficial completo para estos 4 —
# se normalizan a los nombres cortos que ya usamos en ESTADOS_MEXICO.
ALIAS_ESTADOS_SEPOMEX = {
    "Coahuila de Zaragoza": "Coahuila",
    "Michoacán de Ocampo": "Michoacán",
    "Veracruz de Ignacio de la Llave": "Veracruz",
    "México": "Estado de México",
}


def cargar_codigos_postales(db):
    """Importa el catálogo nacional completo (~146k asentamientos) desde el CSV
    fuente. Perezoso: si la tabla ya tiene datos, no hace nada (igual que
    sembrar_si_vacio, pero con lectura de archivo + dedup, así que va aparte)."""
    if db.query(CodigoPostal).first():
        return

    id_por_estado = {nombre: i + 1 for i, nombre in enumerate(ESTADOS_MEXICO)}

    vistos = set()
    filas = []
    with open(RUTA_CSV_SEPOMEX, encoding="utf-8") as archivo:
        for fila in csv.DictReader(archivo):
            nombre_estado = ALIAS_ESTADOS_SEPOMEX.get(fila["estado"], fila["estado"])
            estado_id = id_por_estado.get(nombre_estado)
            if estado_id is None:
                continue  # no debería pasar con el mapeo completo, pero por seguridad

            # Algunos CP del CSV fuente perdieron el cero inicial (ej. "1000" en vez
            # de "01000" para Ciudad de México) — se restaura con zfill.
            cp = fila["cp"].strip().zfill(5)
            municipio = fila["municipio"].strip()
            colonia = fila["asentamiento"].strip()

            clave = (cp, estado_id, municipio, colonia)
            if clave in vistos:
                continue
            vistos.add(clave)
            filas.append({
                "codigo_postal": cp, "estado_id": estado_id, "municipio": municipio, "colonia": colonia,
            })

    TAMANO_BLOQUE = 5000
    for inicio in range(0, len(filas), TAMANO_BLOQUE):
        db.bulk_insert_mappings(CodigoPostal, filas[inicio:inicio + TAMANO_BLOQUE])
    db.commit()
    print(f"Códigos postales importados: {len(filas)}")


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
            {"id": 4, "nombre": "No asistió"},
        ])
        # sembrar_si_vacio no sirve para agregar esta fila en una BD que ya tenía la
        # tabla poblada desde antes (se salta por completo si ya hay filas) — se
        # inserta aparte, a mano, de forma idempotente.
        if not db.query(EstadoInscripcion).filter(EstadoInscripcion.id == 4).first():
            db.add(EstadoInscripcion(id=4, nombre="No asistió"))
            db.commit()

        sembrar_si_vacio(
            db, Estado, [{"id": i + 1, "nombre": nombre} for i, nombre in enumerate(ESTADOS_MEXICO)]
        )

        sembrar_si_vacio(db, Noticia, [
            {
                "id": 1,
                "titulo": "BEC supera las 500 donaciones este semestre",
                "resumen": "Gracias a la comunidad, alcanzamos una nueva marca de apoyo a los albergues de Querétaro.",
                "contenido": (
                    "Gracias al esfuerzo de cientos de ciudadanos, este semestre superamos las 500 "
                    "donaciones registradas en la plataforma. Ropa, alimentos, cobijas y artículos de "
                    "higiene personal llegaron a los albergues asociados de Querétaro, beneficiando "
                    "directamente a las familias que ahí residen.\n\n"
                    "Este logro fue posible gracias a las campañas de recolección organizadas junto con "
                    "los albergues, y al compromiso de quienes usan la app para dar seguimiento a sus "
                    "aportaciones. ¡Gracias por ser parte de esta comunidad!"
                ),
                "fecha": date(2026, 7, 1),
            },
            {
                "id": 2,
                "titulo": "Nueva campaña de voluntariado en Corregidora",
                "resumen": "Se abrieron nuevos espacios para voluntarios en el comedor comunitario.",
                "contenido": (
                    "El Comedor Comunitario Corregidora abrió una nueva convocatoria de voluntariado "
                    "para apoyar en el reparto de despensas y la atención a familias de la zona.\n\n"
                    "Si tienes disponibilidad los fines de semana, revisa la sección de Voluntariados "
                    "en la app y busca los programas disponibles en tu zona — puedes inscribirte "
                    "directamente desde ahí."
                ),
                "fecha": date(2026, 6, 20),
            },
            {
                "id": 3,
                "titulo": "Jornada de invierno: recolección de abrigo",
                "resumen": "Súmate a la recolección de chamarras y cobijas para la temporada de frío.",
                "contenido": (
                    "Con la temporada de frío acercándose, iniciamos la jornada anual de recolección de "
                    "chamarras, cobijas y ropa de invierno para los albergues de la región.\n\n"
                    "Puedes registrar tu donación desde la sección de Donaciones de la app, eligiendo la "
                    "categoría 'Ropa' o 'Cobijas'. Cada prenda cuenta para mantener abrigadas a las "
                    "familias que más lo necesitan este invierno."
                ),
                "fecha": date(2026, 6, 5),
            },
        ])
        db.commit()
        id_queretaro = db.query(Estado).filter(Estado.nombre == "Querétaro").first().id

        # Catálogo nacional completo de códigos postales (SEPOMEX, ~146k asentamientos).
        cargar_codigos_postales(db)

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
