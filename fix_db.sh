#!/bin/bash
psql -U postgres -d BEC <<EOF
DO \$\$
DECLARE
    max_val BIGINT;
BEGIN
    SELECT MAX("Id_Direccion") INTO max_val FROM direcciones;
    PERFORM setval('direcciones_Id_Direccion_seq', COALESCE(max_val, 0) + 1, false);
    
    SELECT MAX("id_Usuario") INTO max_val FROM usuarios;
    PERFORM setval('usuarios_id_Usuario_seq', COALESCE(max_val, 0) + 1, false);
    
    RAISE NOTICE 'Sequences fixed.';
END \$\$;
EOF
