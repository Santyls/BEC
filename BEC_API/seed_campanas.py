import os
import sys

if not os.getenv("DATABASE_URL"):
    os.environ["DATABASE_URL"] = "postgresql://postgres:12345@localhost:5432/BEC"

from app.data.database import engine, SessionLocal
from app.models.models import Base, EstadoCampana, Campana, Albergue, Voluntariado
from datetime import date, time

Base.metadata.create_all(bind=engine)

db = SessionLocal()
try:
    # Mostrar campañas existentes
    campanas = db.query(Campana).all()
    print(f"Campañas en BD: {len(campanas)}")
    for c in campanas:
        print(f"  [{c.id_Campana}] {c.Nombre_Campana}")

    # Mostrar voluntariados existentes
    vols = db.query(Voluntariado).all()
    print(f"\nVoluntariados en BD: {len(vols)}")
    for v in vols:
        print(f"  [{v.Id_Voluntariado}] {v.Nombre_Voluntariado} | campana={v.id_campana} | estado={v.Estado_Voluntariado}")

    # Vincular campañas a los voluntariados existentes sin campaña
    if campanas:
        camp_ids = [c.id_Campana for c in campanas]
        for i, v in enumerate(vols):
            if v.id_campana is None:
                v.id_campana = camp_ids[i % len(camp_ids)]
                print(f"\n  Vinculando voluntariado [{v.Id_Voluntariado}] a campaña [{v.id_campana}]")
        db.commit()

    # Asegurar que hay al menos 3 voluntariados activos
    vols_activos = db.query(Voluntariado).filter(Voluntariado.Estado_Voluntariado == 'Activo').count()
    print(f"\nVoluntariados activos: {vols_activos}")

    if vols_activos < 3 and campanas:
        alb = db.query(Albergue).first()
        id_alb = alb.Id_Albergue if alb else None
        extras = [
            Voluntariado(
                Nombre_Voluntariado="Colecta de Ropa y Víveres",
                Id_albergue=id_alb,
                id_campana=campanas[0].id_Campana,
                Fecha_prog=date(2026, 8, 20),
                Cupo_Max=15,
                Hora_inicio=time(10, 0),
                Hora_Fin=time(15, 0),
                Estado_Voluntariado="Activo",
                Descripcion_Requisitos="Mayor de edad, ropa cómoda."
            ),
            Voluntariado(
                Nombre_Voluntariado="Apoyo en Comedor Comunitario",
                Id_albergue=id_alb,
                id_campana=campanas[-1].id_Campana,
                Fecha_prog=date(2026, 9, 5),
                Cupo_Max=10,
                Hora_inicio=time(9, 0),
                Hora_Fin=time(13, 0),
                Estado_Voluntariado="Activo",
                Descripcion_Requisitos="Sin requisitos previos, traer mandil."
            ),
        ]
        # Agregar solo los que faltan para llegar a 3
        faltantes = 3 - vols_activos
        for e in extras[:faltantes]:
            db.add(e)
        db.commit()
        print(f"  Se agregaron {faltantes} voluntariados activos adicionales.")

    print("\n✅ ¡Datos listos! Revisión final:")
    for v in db.query(Voluntariado).all():
        print(f"  [{v.Id_Voluntariado}] {v.Nombre_Voluntariado} | campana={v.id_campana} | estado={v.Estado_Voluntariado}")

except Exception as e:
    db.rollback()
    print(f"❌ Error: {e}")
    import traceback; traceback.print_exc()
finally:
    db.close()
