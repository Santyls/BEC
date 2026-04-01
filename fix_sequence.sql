-- Diagnóstico: ver el máximo ID actual y el valor de la secuencia
SELECT 'MAX Id_Direccion en tabla' AS descripcion, COALESCE(MAX("Id_Direccion"), 0) AS valor FROM direcciones
UNION ALL
SELECT 'Valor actual de la secuencia', last_value FROM "direcciones_Id_Direccion_seq";

-- CORRECCIÓN: sincronizar la secuencia al MAX(Id_Direccion) + 1
SELECT setval(
    '"direcciones_Id_Direccion_seq"',
    (SELECT COALESCE(MAX("Id_Direccion"), 0) FROM direcciones) + 1,
    false
);

-- Verificación final
SELECT 'Secuencia actualizada a' AS descripcion, last_value AS valor FROM "direcciones_Id_Direccion_seq";
