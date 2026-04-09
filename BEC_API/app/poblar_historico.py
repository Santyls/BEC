from sqlalchemy import text
from app.data.database import SessionLocal, engine
from app.models.models import Donacion, Usuario, Albergue
from datetime import date, timedelta
import random

def generar_historico():
    # 1. Intentar agregar la columna Fecha_Donacion si no existe
    with engine.begin() as conn:
        try:
            conn.execute(text('ALTER TABLE donaciones ADD COLUMN "Fecha_Donacion" DATE DEFAULT CURRENT_DATE NOT NULL'))
            print("✅ Columna Fecha_Donacion agregada existosamente a la base de datos.")
        except Exception as e:
            # Probablemente ya exista
            print("ℹ️  La columna Fecha_Donacion ya existe o hubo un error menor:", e)

    db = SessionLocal()
    try:
        # Verificar si ya existe histórico (donaciones de hace más de 10 días)
        limite = date.today() - timedelta(days=10)
        historico_existente = db.query(Donacion).filter(Donacion.Fecha_Donacion < limite).first()
        
        if historico_existente:
            print("⚠️  El histórico ya existe. Borrando histórico anterior para regenerar...")
            db.query(Donacion).filter(Donacion.Fecha_Donacion < limite).delete()
            db.commit()

        usuarios = db.query(Usuario).all()
        albergues = db.query(Albergue).all()
        if not usuarios or not albergues:
            print("❌ Error: Se necesitan usuarios y albergues en la BD para generar datos.")
            return

        # Generar 365 días de histórico (1 año hacia atrás)
        fechas = [date.today() - timedelta(days=x) for x in range(365, 0, -1)]
        nuevas_donaciones = []

        print("⏳ Generando histórico con estacionalidad de invierno...")

        for d in fechas:
            num_donaciones_base = random.randint(1, 3) # donaciones base por día
            es_invierno = d.month in [11, 12, 1, 2] # Noviembre a Febrero

            if es_invierno:
                # Incremento brusco (Estacionalidad de frío)
                num_donaciones_base += random.randint(3, 8)

            for _ in range(num_donaciones_base):
                u = random.choice(usuarios)
                a = random.choice(albergues)
                
                # Categorías
                # 1: Ropa, 2: Alimentos, 3: Cobijas, 4: Higiene, 5: Medicamentos
                if es_invierno:
                    # En invierno: 35% probabilida de ropa o cobijas respectivamente
                    cat = random.choices([1, 2, 3, 4, 5], weights=[0.35, 0.20, 0.35, 0.05, 0.05])[0]
                    cant = round(random.uniform(10.0, 50.0), 2) if cat in [1, 3] else round(random.uniform(5.0, 20.0), 2)
                else:
                    cat = random.choices([1, 2, 3, 4, 5], weights=[0.20, 0.40, 0.10, 0.15, 0.15])[0]
                    cant = round(random.uniform(2.0, 15.0), 2)

                uni = 1 if cat in [1, 3] else random.choice([2, 3, 4])
                cond = random.choice(["Nuevo/Sellado", "Buen estado (Usado)", "Regular"])

                donacion = Donacion(
                    Id_Usuario=u.id_Usuario,
                    id_Categoria=cat,
                    Id_Condicion=cond,
                    Cantidad=cant,
                    Id_Unidad=uni,
                    Marca="Registro Histórico",
                    Id_Albergue=a.Id_Albergue,
                    Fecha_Donacion=d
                )
                nuevas_donaciones.append(donacion)

        db.add_all(nuevas_donaciones)
        db.commit()
        print(f"🎉 ¡Éxito! Se insertaron {len(nuevas_donaciones)} registros históricos simulando estacionalidad invernal.")

    except Exception as e:
        db.rollback()
        print(f"❌ Error durante la generación: {e}")
    finally:
        db.close()

if __name__ == "__main__":
    generar_historico()
