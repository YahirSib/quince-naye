-- Script para configurar el sistema de invitados con tokens
-- Ejecutar una sola vez en la base de datos quince_rapunzel

-- 1. Agregar columna invitado_id a la tabla asistentes (si no existe)
ALTER TABLE asistentes
ADD COLUMN invitado_id INT(11) DEFAULT NULL AFTER id,
ADD UNIQUE KEY unique_invitado (invitado_id);

-- 2. Corregir charset de la tabla invitados
ALTER TABLE invitados CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 3. Generar tokens únicos para todos los registros que tengan token NULL
UPDATE invitados
SET token = CONCAT(
    SUBSTR(MD5(RAND()), 1, 8),
    SUBSTR(MD5(CONCAT(id, RAND())), 1, 8),
    SUBSTR(MD5(CONCAT(nombre, RAND())), 1, 8)
)
WHERE token IS NULL OR token = '';

-- 4. Verificar que todos los registros tengan token
SELECT id, nombre, cantidad, token FROM invitados ORDER BY id;
