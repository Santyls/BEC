-- =============================================================
-- FIX: Resetea todos los sequences de BEC al valor correcto
-- Ejecutar con:
--   docker exec bec_db_postgres psql -U postgres -d BEC -f /tmp/fix_sequences.sql
-- =============================================================

-- Verifica el MAX actual antes de corregir
SELECT 'ANTES:' AS info, 'direcciones' AS tabla, MAX("Id_Direccion") AS max_id FROM direcciones
UNION ALL SELECT 'ANTES:', 'usuarios',     MAX("id_Usuario")    FROM usuarios
UNION ALL SELECT 'ANTES:', 'albergues',    MAX("Id_Albergue")   FROM albergues
UNION ALL SELECT 'ANTES:', 'campanas',     MAX("id_Campana")    FROM campanas
UNION ALL SELECT 'ANTES:', 'donaciones',   MAX("Id_Donacion")   FROM donaciones
UNION ALL SELECT 'ANTES:', 'voluntariados',MAX("Id_Voluntariado") FROM voluntariados
UNION ALL SELECT 'ANTES:', 'colonias',     MAX("Id_Colonia")    FROM colonias
UNION ALL SELECT 'ANTES:', 'municipios',   MAX("Id_Municipio")  FROM municipios
UNION ALL SELECT 'ANTES:', 'estados_rep',  MAX("Id_Estado")     FROM estados_rep;

-- Resetear cada sequence al MAX(pk)+1
SELECT setval('direcciones_Id_Direccion_seq',  COALESCE((SELECT MAX("Id_Direccion")   FROM direcciones),  0) + 1, false);
SELECT setval('usuarios_id_Usuario_seq',        COALESCE((SELECT MAX(id_Usuario)       FROM usuarios),     0) + 1, false);
SELECT setval('albergues_Id_Albergue_seq',      COALESCE((SELECT MAX("Id_Albergue")   FROM albergues),    0) + 1, false);
SELECT setval('campanas_id_Campana_seq',        COALESCE((SELECT MAX(id_Campana)      FROM campanas),     0) + 1, false);
SELECT setval('donaciones_Id_Donacion_seq',     COALESCE((SELECT MAX("Id_Donacion")   FROM donaciones),   0) + 1, false);
SELECT setval('voluntariados_Id_Voluntariado_seq', COALESCE((SELECT MAX("Id_Voluntariado") FROM voluntariados), 0) + 1, false);
SELECT setval('colonias_Id_Colonia_seq',        COALESCE((SELECT MAX("Id_Colonia")    FROM colonias),     0) + 1, false);
SELECT setval('municipios_Id_Municipio_seq',    COALESCE((SELECT MAX("Id_Municipio")  FROM municipios),   0) + 1, false);
SELECT setval('estados_rep_Id_Estado_seq',      COALESCE((SELECT MAX("Id_Estado")     FROM estados_rep),  0) + 1, false);

-- Verifica el resultado
SELECT 'DESPUES:' AS info, 'direcciones' AS tabla, MAX("Id_Direccion") AS max_id FROM direcciones
UNION ALL SELECT 'DESPUES:', 'usuarios',     MAX("id_Usuario")    FROM usuarios
UNION ALL SELECT 'DESPUES:', 'albergues',    MAX("Id_Albergue")   FROM albergues
UNION ALL SELECT 'DESPUES:', 'campanas',     MAX("id_Campana")    FROM campanas;

\echo '✅  Sequences corregidos correctamente.'
