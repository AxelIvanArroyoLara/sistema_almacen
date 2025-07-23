<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $art_no   = trim($_POST['art_no'] ?? '');
    $cantidad = (int)($_POST['cantidad'] ?? 0);
    $modo     = trim($_POST['modo'] ?? '');
    $user_id  = trim($_POST['user_id'] ?? '');
    $admin_id = trim($_POST['admin_id'] ?? '');

    if (empty($art_no) || $cantidad <= 0 || $modo !== 'devolver') {
        echo 'Error: Datos inválidos o incompletos.';
        exit;
    }

    try {
        // Verificar existencia actual
        $stmt = $connection->prepare("SELECT EXISTENCIA FROM conexion WHERE ART_NO = :art_no");
        $stmt->execute([':art_no' => $art_no]);
        $conexion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$conexion) {
            echo 'Error: Artículo no encontrado.';
            exit;
        }

        // Obtener datos del usuario
        $stmtUser = $connection->prepare("
            SELECT NUMERO AS numero_control, NOMBRE AS nombre_completo 
            FROM usuarios 
            WHERE NUMERO = :user_id
        ");
        $stmtUser->execute([':user_id' => $user_id]);
        $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo 'Error: Usuario no encontrado.';
            exit;
        }

        // Actualizar existencia (sumar)
        $existenciaActual = (int)$conexion['EXISTENCIA'];
        $nuevaExistencia  = $existenciaActual + $cantidad;

        $stmtUpdate = $connection->prepare("
            UPDATE conexion 
            SET EXISTENCIA = :existencia 
            WHERE ART_NO = :art_no
        ");
        $stmtUpdate->execute([
            ':existencia' => $nuevaExistencia,
            ':art_no'     => $art_no
        ]);

        // Registrar en prim14a
        $sqlInsert = "
            INSERT INTO prim14a (
                NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA, CANT0MULTA, REAL_VAL, DEUDOR
            ) VALUES (
                :numero, :nombre, :nompar, 'DEVOLUCION', CURRENT_DATE, :encargado, :hora, :cantidad, 0, 0
            )
        ";

        $stmtInsert = $connection->prepare($sqlInsert);
        $stmtInsert->execute([
            ':numero'    => $usuario['numero_control'],
            ':nombre'    => $usuario['nombre_completo'],
            ':nompar'    => $art_no,
            ':encargado' => $admin_id,
            ':hora' => date('H:i:s'),
            ':cantidad' => $cantidad
        ]);

        echo 'success';
    } catch (PDOException $e) {
        echo 'Error en la base de datos: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo 'Error: Método no permitido.';
}
