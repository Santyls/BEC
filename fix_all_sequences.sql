-- =============================================================
-- FIX: Resetea todos los sequences de la base de datos BEC
-- Este script es robusto y maneja las comillas para nombres de columnas case-sensitive.
-- =============================================================

DO $$
DECLARE
    max_val BIGINT;
BEGIN
    -- 1. direcciones ("Id_Direccion")
    SELECT MAX("Id_Direccion") INTO max_val FROM direcciones;
    PERFORM setval('direcciones_Id_Direccion_seq', COALESCE(max_val, 0) + 1, false);
    
    -- 2. usuarios ("id_Usuario")
    SELECT MAX("id_Usuario") INTO max_val FROM usuarios;
    PERFORM setval('usuarios_id_Usuario_seq', COALESCE(max_val, 0) + 1, false);
    
    -- 3. albergues ("Id_Albergue")
    SELECT MAX("Id_Albergue") INTO max_val FROM albergues;
    PERFORM setval('albergues_Id_Albergue_seq', COALESCE(max_val, 0) + 1, false);
    
    -- 4. campanas ("id_Campana")
    SELECT MAX("id_Campana") INTO max_val FROM campanas;
    PERFORM setval('campanas_id_Campana_seq', COALESCE(max_val, 0) + 1, false);
    
    -- 5. colonias ("Id_Colonia")
    SELECT MAX("Id_Colonia") INTO max_val FROM colonias;
    PERFORM setval('colonias_Id_Colonia_seq', COALESCE(max_val, 0) + 1, false);
    
    -- 6. municipios ("Id_Municipio")
    SELECT MAX("Id_Municipio") INTO max_val FROM municipios;
    PERFORM setval('municipios_Id_Municipio_seq', COALESCE(max_val, 0) + 1, false);
    
    -- 7. donaciones ("Id_Donacion")
    SELECT MAX("Id_Donacion") INTO max_val FROM donaciones;
    PERFORM setval('donaciones_Id_Donacion_seq', COALESCE(max_val, 0) + 1, false);
    
    -- 8. voluntariados ("Id_Voluntariado")
    SELECT MAX("Id_Voluntariado") INTO max_val FROM voluntariados;
    PERFORM setval('voluntariados_Id_Voluntariado_seq', COALESCE(max_val, 0) + 1, false);

    RAISE NOTICE 'Todos los sequences clave han sido reseteados al MAX(id) + 1.';
END $$;
