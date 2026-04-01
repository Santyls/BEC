"""
=============================================================
  SCRIPT DE POBLADO DE BASE DE DATOS  —  BEC API
  Datos realistas de Santiago de Querétaro, Querétaro
  -----------------------------------------------------
  Ejecutar desde el contenedor:
    docker exec bec_api_app python /app/app/poblar_bd.py
=============================================================
"""
from app.data.database import SessionLocal
from app.models.models import (
    Rol, Estado_Rep, Genero, Municipio, Colonia, Direccion,
    Unidad, Categoria, EstadoCampana,
    Usuario, Albergue, Campana, Donacion, Voluntariado
)
from app.security.security import obtener_password_hash
from datetime import date, time

def poblar():
    db = SessionLocal()

    try:
        # =============================================
        # 1. ROLES
        # =============================================
        roles = [
            Rol(Id_Rol=1, Nombre_Rol="Admin"),
            Rol(Id_Rol=2, Nombre_Rol="Recepcionista"),
            Rol(Id_Rol=3, Nombre_Rol="Ciudadano"),
        ]
        for r in roles:
            existe = db.query(Rol).filter(Rol.Id_Rol == r.Id_Rol).first()
            if not existe:
                db.add(r)
        db.commit()
        print("✅ Tabla 'roles' poblada correctamente.")

        # =============================================
        # 2. ESTADOS DE LA REPÚBLICA
        # =============================================
        estados = [
            Estado_Rep(Id_Estado=1, Nombre_Estado="Querétaro"),
            Estado_Rep(Id_Estado=2, Nombre_Estado="Ciudad de México"),
            Estado_Rep(Id_Estado=3, Nombre_Estado="Guanajuato"),
        ]
        for e in estados:
            existe = db.query(Estado_Rep).filter(Estado_Rep.Id_Estado == e.Id_Estado).first()
            if not existe:
                db.add(e)
        db.commit()
        print("✅ Tabla 'estados_rep' poblada correctamente.")

        # =============================================
        # 3. GÉNEROS
        # =============================================
        generos = [
            Genero(Id_Genero=1, Nombre_genero="Masculino"),
            Genero(Id_Genero=2, Nombre_genero="Femenino"),
            Genero(Id_Genero=3, Nombre_genero="Otro"),
        ]
        for g in generos:
            existe = db.query(Genero).filter(Genero.Id_Genero == g.Id_Genero).first()
            if not existe:
                db.add(g)
        db.commit()
        print("✅ Tabla 'generos' poblada correctamente.")

        # =============================================
        # 4. MUNICIPIOS (Querétaro)
        # =============================================
        municipios = [
            Municipio(Id_Municipio=1, Nombre_Municipio="Santiago de Querétaro", Id_Estado=1),
            Municipio(Id_Municipio=2, Nombre_Municipio="El Marqués", Id_Estado=1),
            Municipio(Id_Municipio=3, Nombre_Municipio="Corregidora", Id_Estado=1),
        ]
        for m in municipios:
            existe = db.query(Municipio).filter(Municipio.Id_Municipio == m.Id_Municipio).first()
            if not existe:
                db.add(m)
        db.commit()
        print("✅ Tabla 'municipios' poblada correctamente.")

        # =============================================
        # 5. COLONIAS (CPs reales de Querétaro)
        # =============================================
        colonias = [
            Colonia(Id_Colonia=1, Nombre_Colonia="Centro Histórico",       Id_mucipio=1, Cp=76000),
            Colonia(Id_Colonia=2, Nombre_Colonia="Menchaca",               Id_mucipio=1, Cp=76140),
            Colonia(Id_Colonia=3, Nombre_Colonia="San Francisquito",       Id_mucipio=1, Cp=76030),
            Colonia(Id_Colonia=4, Nombre_Colonia="Epigmenio González",     Id_mucipio=1, Cp=76038),
            Colonia(Id_Colonia=5, Nombre_Colonia="La Negreta",             Id_mucipio=1, Cp=76020),
            Colonia(Id_Colonia=6, Nombre_Colonia="Zaragoza",               Id_mucipio=2, Cp=76246),
        ]
        for c in colonias:
            existe = db.query(Colonia).filter(Colonia.Id_Colonia == c.Id_Colonia).first()
            if not existe:
                db.add(c)
        db.commit()
        print("✅ Tabla 'colonias' poblada correctamente.")

        # =============================================
        # 6. DIRECCIONES
        # =============================================
        direcciones = [
            Direccion(Id_Direccion=1, Id_Colonia=1, Calle="Av. Madero",             No_exterior="45"),
            Direccion(Id_Direccion=2, Id_Colonia=2, Calle="Calle Menchaca",         No_exterior="102"),
            Direccion(Id_Direccion=3, Id_Colonia=3, Calle="Av. San Francisquito",   No_exterior="78"),
            Direccion(Id_Direccion=4, Id_Colonia=4, Calle="Blvd. Epigmenio Glz.",   No_exterior="250"),
            Direccion(Id_Direccion=5, Id_Colonia=5, Calle="Calle La Negreta",       No_exterior="18"),
            Direccion(Id_Direccion=6, Id_Colonia=1, Calle="Calle Corregidora Sur",  No_exterior="33"),
        ]
        for d in direcciones:
            existe = db.query(Direccion).filter(Direccion.Id_Direccion == d.Id_Direccion).first()
            if not existe:
                db.add(d)
        db.commit()
        print("✅ Tabla 'direcciones' poblada correctamente.")

        # =============================================
        # 7. UNIDADES DE MEDIDA
        # =============================================
        unidades = [
            Unidad(Id_Unidad=1, Nombre_Unidad="Piezas"),
            Unidad(Id_Unidad=2, Nombre_Unidad="Kilogramos (kg)"),
            Unidad(Id_Unidad=3, Nombre_Unidad="Litros (L)"),
            Unidad(Id_Unidad=4, Nombre_Unidad="Cajas"),
            Unidad(Id_Unidad=5, Nombre_Unidad="Paquetes"),
        ]
        for u in unidades:
            existe = db.query(Unidad).filter(Unidad.Id_Unidad == u.Id_Unidad).first()
            if not existe:
                db.add(u)
        db.commit()
        print("✅ Tabla 'unidades' poblada correctamente.")

        # =============================================
        # 8. CATEGORÍAS DE DONACIÓN
        # =============================================
        categorias = [
            Categoria(Id_Categoria=1, Nombre_Categoria="Ropa"),
            Categoria(Id_Categoria=2, Nombre_Categoria="Alimentos"),
            Categoria(Id_Categoria=3, Nombre_Categoria="Cobijas"),
            Categoria(Id_Categoria=4, Nombre_Categoria="Higiene personal"),
            Categoria(Id_Categoria=5, Nombre_Categoria="Medicamentos"),
        ]
        for cat in categorias:
            existe = db.query(Categoria).filter(Categoria.Id_Categoria == cat.Id_Categoria).first()
            if not existe:
                db.add(cat)
        db.commit()
        print("✅ Tabla 'categorias' poblada correctamente.")

        # =============================================
        # 9. ESTADOS DE CAMPAÑA
        # =============================================
        estados_campana = [
            EstadoCampana(Id_Estado_Campana=1, Nombre_Estado="Programada (Futura)"),
            EstadoCampana(Id_Estado_Campana=2, Nombre_Estado="Activa"),
            EstadoCampana(Id_Estado_Campana=3, Nombre_Estado="Finalizada"),
        ]
        for ec in estados_campana:
            existe = db.query(EstadoCampana).filter(EstadoCampana.Id_Estado_Campana == ec.Id_Estado_Campana).first()
            if not existe:
                db.add(ec)
        db.commit()
        print("✅ Tabla 'estados_campanas' poblada correctamente.")

        # =============================================
        # 10. ALBERGUES  (dependen de Direcciones)
        # =============================================
        albergues = [
            Albergue(
                Id_Albergue=1,
                Nombre_Albergue="Albergue Yimpathí",
                Capacidad_Max=80,
                Tel_Contacto=4421234567,
                Id_Direccion=1
            ),
            Albergue(
                Id_Albergue=2,
                Nombre_Albergue="Centro de Día Meni",
                Capacidad_Max=50,
                Tel_Contacto=4429876543,
                Id_Direccion=3
            ),
        ]
        for a in albergues:
            existe = db.query(Albergue).filter(Albergue.Id_Albergue == a.Id_Albergue).first()
            if not existe:
                db.add(a)
        db.commit()
        print("✅ Tabla 'albergues' poblada correctamente.")

        # =============================================
        # 11. USUARIOS  (dependen de Direcciones, Albergues, Roles, Generos)
        # =============================================
        usuarios = [
            # Admin General (sin albergue asignado, tiene acceso total)
            Usuario(
                Nombre="Carlos",
                Apellido_P="Ramírez",
                Apellido_M="López",
                Correo="admin@bec.com",
                Password=obtener_password_hash("admin123"),
                Edad=35,
                Id_Direccion=2,
                Id_Albergue=None,
                Id_Rol=1,
                Tel=4421110000,
                Id_Genero=1
            ),
            # Recepcionista asignado al Albergue Yimpathí
            Usuario(
                Nombre="María",
                Apellido_P="González",
                Apellido_M="Hernández",
                Correo="recepcion@yimpathi.org",
                Password=obtener_password_hash("recep123"),
                Edad=28,
                Id_Direccion=4,
                Id_Albergue=1,
                Id_Rol=2,
                Tel=4422220000,
                Id_Genero=2
            ),
            # Ciudadano donante
            Usuario(
                Nombre="Juan",
                Apellido_P="Pérez",
                Apellido_M="Sánchez",
                Correo="juan.perez@gmail.com",
                Password=obtener_password_hash("ciudadano123"),
                Edad=42,
                Id_Direccion=5,
                Id_Albergue=None,
                Id_Rol=3,
                Tel=4423330000,
                Id_Genero=1
            ),
        ]
        for u in usuarios:
            existe = db.query(Usuario).filter(Usuario.Correo == u.Correo).first()
            if not existe:
                db.add(u)
        db.commit()
        print("✅ Tabla 'usuarios' poblada correctamente.")

        # =============================================
        # 12. CAMPAÑAS (dependen de EstadoCampana)
        # =============================================
        campanas = [
            Campana(
                Nombre_Campana="Colecta Invernal Qro 2026",
                Fecha_Inicio=date(2026, 11, 1),
                Fecha_Fin=date(2026, 12, 31),
                id_Estado_campana=1,  # Programada (Futura)
                Descripcion_Objetivos="Recolección de cobijas, ropa de invierno y alimentos no perecederos para personas en situación de calle durante la temporada invernal en Santiago de Querétaro."
            ),
            Campana(
                Nombre_Campana="Comedor Comunitario Centro",
                Fecha_Inicio=date(2026, 3, 1),
                Fecha_Fin=date(2026, 6, 30),
                id_Estado_campana=2,  # Activa
                Descripcion_Objetivos="Operación de un comedor comunitario en el Centro Histórico de Querétaro, ofreciendo desayunos y comidas calientes a personas vulnerables."
            ),
        ]
        for camp in campanas:
            existe = db.query(Campana).filter(Campana.Nombre_Campana == camp.Nombre_Campana).first()
            if not existe:
                db.add(camp)
        db.commit()
        print("✅ Tabla 'campanas' poblada correctamente.")

        # =============================================
        # 13. DONACIONES (dependen de Usuarios, Categorías, Unidades, Albergues)
        # =============================================
        # Obtenemos el ciudadano para asociar la donación
        ciudadano = db.query(Usuario).filter(Usuario.Correo == "juan.perez@gmail.com").first()

        if ciudadano:
            donaciones = [
                Donacion(
                    Id_Usuario=ciudadano.id_Usuario,
                    id_Categoria=1,        # Ropa
                    Id_Condicion="Buen estado (Usado)",
                    Cantidad=15.0,
                    Id_Unidad=1,           # Piezas
                    Marca=None,
                    Id_Albergue=1          # Albergue Yimpathí
                ),
                Donacion(
                    Id_Usuario=ciudadano.id_Usuario,
                    id_Categoria=2,        # Alimentos
                    Id_Condicion="Nuevo/Sellado",
                    Cantidad=10.0,
                    Id_Unidad=2,           # Kilogramos
                    Marca="La Costeña",
                    Id_Albergue=2          # Centro de Día Meni
                ),
                Donacion(
                    Id_Usuario=ciudadano.id_Usuario,
                    id_Categoria=3,        # Cobijas
                    Id_Condicion="Nuevo/Sellado",
                    Cantidad=5.0,
                    Id_Unidad=1,           # Piezas
                    Marca="San Marcos",
                    Id_Albergue=1          # Albergue Yimpathí
                ),
            ]
            for don in donaciones:
                db.add(don)
            db.commit()
            print("✅ Tabla 'donaciones' poblada correctamente.")
        else:
            print("⚠️  No se encontró el ciudadano. Se omitieron las donaciones.")

        # =============================================
        # 14. VOLUNTARIADOS (dependen de Albergues y Campañas)
        # =============================================
        campana_invernal = db.query(Campana).filter(Campana.Nombre_Campana == "Colecta Invernal Qro 2026").first()
        campana_comedor  = db.query(Campana).filter(Campana.Nombre_Campana == "Comedor Comunitario Centro").first()

        voluntariados = [
            Voluntariado(
                Nombre_Voluntariado="Reparto de cobijas en el Centro",
                Id_albergue=1,
                id_campana=campana_invernal.id_Campana if campana_invernal else None,
                Fecha_prog=date(2026, 12, 15),
                Cupo_Max=30,
                Hora_inicio=time(9, 0),
                Hora_Fin=time(14, 0),
                Estado_Voluntariado="Activo/Próximo",
                Descripcion_Requisitos="Mayores de 18 años. Traer ropa cómoda y ganas de ayudar. Se proporcionará chaleco identificador."
            ),
            Voluntariado(
                Nombre_Voluntariado="Apoyo en Comedor Comunitario",
                Id_albergue=2,
                id_campana=campana_comedor.id_Campana if campana_comedor else None,
                Fecha_prog=date(2026, 4, 5),
                Cupo_Max=15,
                Hora_inicio=time(7, 0),
                Hora_Fin=time(12, 0),
                Estado_Voluntariado="Activo/Próximo",
                Descripcion_Requisitos="Se requiere certificado médico vigente. Actividades: preparación de alimentos, servicio en mesas y limpieza."
            ),
        ]
        for vol in voluntariados:
            db.add(vol)
        db.commit()
        print("✅ Tabla 'voluntariados' poblada correctamente.")

        # =============================================
        print("\n🎉 ¡BASE DE DATOS POBLADA EXITOSAMENTE!")
        print("=" * 50)
        print("Credenciales de prueba:")
        print("  Admin:        admin@bec.com        / admin123")
        print("  Recepcionista: recepcion@yimpathi.org / recep123")
        print("  Ciudadano:    juan.perez@gmail.com  / ciudadano123")
        print("=" * 50)

    except Exception as e:
        db.rollback()
        print(f"\n❌ ERROR durante el poblado: {e}")
        raise
    finally:
        db.close()

if __name__ == "__main__":
    poblar()
