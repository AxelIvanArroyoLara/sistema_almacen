-- Script de datos de prueba para testing
-- Ejecutar desde: docker compose exec -T db mysql -u root -prootpass almacen < test-data.sql

-- ========================================
-- 1. CREAR USUARIO DE PRUEBA (si no existe)
-- ========================================

-- Verificar si el usuario 999999 existe
SELECT 'Verificando usuario de prueba...' as status;

-- Insertar usuario de prueba si no existe
INSERT IGNORE INTO usuarios (numero, nombre, CLAVE, status, permiso, nivel)
VALUES 
(999999, 'Usuario de Prueba', 'test123', 'Activo', 'Usuario', 'Alumno');

SELECT 'Usuario de prueba creado/verificado: 999999' as status;
SELECT 'Contraseña: test123' as password;

-- ========================================
-- 2. DATOS PARA CONTROL BECARIO
-- ========================================

SELECT 'Insertando datos en control_becario...' as status;

INSERT INTO control_becario (user_id, fecha, hora_entrada, hora_salida, horas_trabajadas) 
VALUES 
('999999', '2026-01-28', '2026-01-28 09:00:00', '2026-01-28 13:00:00', '04:00:00'),
('999999', '2026-01-27', '2026-01-27 10:00:00', '2026-01-27 14:30:00', '04:30:00'),
('999999', '2026-01-26', '2026-01-26 08:00:00', '2026-01-26 12:00:00', '04:00:00'),
('999999', '2026-01-25', '2026-01-25 09:30:00', '2026-01-25 13:30:00', '04:00:00'),
('999999', '2026-01-24', '2026-01-24 10:00:00', '2026-01-24 15:00:00', '05:00:00');

SELECT 'Datos de control becario insertados' as status;

-- ========================================
-- 3. DATOS PARA PRÉSTAMOS ACTIVOS
-- ========================================

SELECT 'Insertando datos en prestamos...' as status;

INSERT INTO prestamos (NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, HORA, ENCARGADO, CANT0MULTA, REAL_VAL) 
VALUES 
(999999, 'Usuario de Prueba', 'Multímetro Digital Fluke 87V', 'Préstamo', '2026-01-28', '10:00:00', 12345, 0, 350.00),
(999999, 'Usuario de Prueba', 'Osciloscopio Tektronix TDS2024C', 'Préstamo', '2026-01-27', '11:30:00', 12345, 0, 2500.00);

SELECT 'Datos de préstamos activos insertados' as status;

-- ========================================
-- 4. DATOS PARA HISTORIAL DE PRÉSTAMOS
-- ========================================

SELECT 'Insertando datos en prim14a (historial)...' as status;

INSERT INTO prim14a (NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, HORA, ENCARGADO, CANT0MULTA) 
VALUES 
-- Préstamo 1 (completo)
(999999, 'Usuario de Prueba', 'Generador de Funciones', 'Préstamo', '2026-01-20', '09:00:00', 12345, 0),
(999999, 'Usuario de Prueba', 'Generador de Funciones', 'Devolución', '2026-01-22', '16:00:00', 12345, 0),

-- Préstamo 2 (completo)
(999999, 'Usuario de Prueba', 'Fuente de Alimentación', 'Préstamo', '2026-01-15', '10:30:00', 12345, 0),
(999999, 'Usuario de Prueba', 'Fuente de Alimentación', 'Devolución', '2026-01-18', '14:00:00', 12345, 0),

-- Préstamo 3 (completo)
(999999, 'Usuario de Prueba', 'Protoboard Grande', 'Préstamo', '2026-01-10', '11:00:00', 12345, 0),
(999999, 'Usuario de Prueba', 'Protoboard Grande', 'Devolución', '2026-01-12', '15:30:00', 12345, 0),

-- Préstamo 4 (aún activo, en tabla prestamos)
(999999, 'Usuario de Prueba', 'Multímetro Digital Fluke 87V', 'Préstamo', '2026-01-28', '10:00:00', 12345, 0),
(999999, 'Usuario de Prueba', 'Osciloscopio Tektronix TDS2024C', 'Préstamo', '2026-01-27', '11:30:00', 12345, 0);

SELECT 'Datos de historial de préstamos insertados' as status;

-- ========================================
-- RESUMEN
-- ========================================

SELECT '========================================' as '=';
SELECT 'DATOS DE PRUEBA INSERTADOS EXITOSAMENTE' as status;
SELECT '========================================' as '=';
SELECT '' as '';
SELECT 'CREDENCIALES DE PRUEBA:' as '';
SELECT 'Usuario: 999999' as '';
SELECT 'Contraseña: test123' as '';
SELECT '' as '';
SELECT 'DATOS DISPONIBLES:' as '';

SELECT CONCAT('Control Becario: ', COUNT(*), ' registros') as info 
FROM control_becario WHERE user_id = '999999';

SELECT CONCAT('Préstamos Activos: ', COUNT(*), ' registros') as info 
FROM prestamos WHERE NUMERO = 999999;

SELECT CONCAT('Historial Préstamos: ', COUNT(*), ' registros') as info 
FROM prim14a WHERE NUMERO = 999999;

SELECT '' as '';
SELECT 'CÓMO USAR:' as '';
SELECT '1. Accede a: http://localhost:8080/php/forms/credentials/form-beca-check_user_id.php' as '';
SELECT '2. Usuario: 999999' as '';
SELECT '3. Contraseña: test123' as '';
SELECT '4. Verás tus datos en el dashboard' as '';
