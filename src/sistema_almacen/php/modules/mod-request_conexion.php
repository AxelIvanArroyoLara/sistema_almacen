<?php
require_once 'conn.php';
require_once 'security.php';

requireUserAuthentication();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $art_no   = trim($_POST['art_no'] ?? '');
    $cantidad = (int)($_POST['cantidad'] ?? 0);
    $modo     = trim($_POST['modo'] ?? ''); // 'prestamo' o 'devolver'
    $user_id    = trim($_POST['user_id'] ?? '');
    $admin_id   = trim($_POST['admin_id'] ?? '');

    if (empty($art_no) || $cantidad <= 0 || !in_array($modo, ['prestamo', 'devolver'])) {
        echo 'Error: Datos inválidos o incompletos.';
        exit;
    }

    try {
        // Verificar existencia actual
        $stmt = $connection->prepare("SELECT EXISTENCIA FROM conexion WHERE ART_NO = :art_no");
        $stmt->execute([':art_no' => $art_no]);
        $conexion = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener datos del usuario
        $stmtUser = $connection->prepare("
            SELECT NUMERO AS numero_control, NOMBRE AS nombre_completo 
            FROM usuarios 
            WHERE NUMERO = :user_id
        ");
        $stmtUser->execute([':user_id' => $user_id]);
        $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo 'error: Usuario no encontrado.';
            exit;
        }

        if (!$conexion) {
            echo 'Error: Artículo no encontrado.';
            exit;
        }

        $existenciaActual = (int)$conexion['EXISTENCIA'];

        if ($modo === 'prestamo') {
            if ($cantidad > $existenciaActual) {
                echo 'Error: No hay suficientes unidades disponibles.';
                exit;
            }
            $nuevaExistencia = $existenciaActual - $cantidad;
        } else { // devolver
            $nuevaExistencia = $existenciaActual + $cantidad;
        }

        // Actualizar existencia
        $stmtUpdate = $connection->prepare("UPDATE conexion SET EXISTENCIA = :existencia WHERE ART_NO = :art_no");
        $stmtUpdate->execute([
            ':existencia' => $nuevaExistencia,
            ':art_no'     => $art_no
        ]);

        // Inserción en prim14a
        $sqlInsert = "
                    INSERT INTO prim14a (
                        NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA, CANT0MULTA, REAL_VAL, DEUDOR
                    ) VALUES (
                        :numero, :nombre, :nompar, :tipmov, CURRENT_DATE, :encargado, :hora, :cantidad, :real_val, :deudor
                    )
                ";
                    
                $stmtInsert = $connection->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':numero'    => $usuario['numero_control'],
                    ':nombre'    => $usuario['nombre_completo'],
                    ':nompar'    => $art_no,
                    ':tipmov'    => 'PRÉSTAMO',
                    ':encargado' => $admin_id,
                    ':hora' => date('H:i:s'),
                    ':cantidad'  => $cantidad,
                    ':real_val'  => 0,
                    ':deudor'    => 1
                ]);
        
        // Inserción en prestamos
        $sqlInsertPrestamos = "
                    INSERT INTO prestamos (
                        TIPO, NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA, CANT0MULTA, REAL_VAL, DEUDOR
                    ) VALUES (
                        :tipo, :numero, :nombre, :nompar, :tipmov, CURRENT_DATE, :encargado, :hora, :cantidad, :real_val, :deudor
                    )
                ";
                    
                $stmtInsertPrestamos = $connection->prepare($sqlInsertPrestamos);
                $stmtInsertPrestamos->execute([
                    ':tipo'      => 'Conexión',
                    ':numero'    => $usuario['numero_control'],
                    ':nombre'    => $usuario['nombre_completo'],
                    ':nompar'    => $art_no,
                    ':tipmov'    => 'PRESTAMO',
                    ':encargado' => $admin_id,
                    ':hora' => date('H:i:s'),
                    ':cantidad'  => $cantidad,
                    ':real_val'  => 0,
                    ':deudor'    => 1
                ]);



        echo 'success';
    } catch (PDOException $e) {
        echo 'Error en la base de datos: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo 'Error: Método no permitido.';
}
