<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $art_no   = trim($_POST['art_no'] ?? '');
    $cantidad = (int)($_POST['cantidad'] ?? 0);
    $modo     = trim($_POST['modo'] ?? '');
    $user_id  = trim($_POST['user_id'] ?? '');
    $admin_id = trim($_POST['admin_id'] ?? '');

    if (empty($art_no) || $cantidad <= 0 || $modo !== 'prestamo') {
        echo 'Error: Datos inválidos o incompletos.';
        exit;
    }

    try {
        // Verificar chip
        $stmt = $connection->prepare("SELECT EXISTENCIA, PRECIO FROM chips WHERE ART_NO = :art_no");
        $stmt->execute([':art_no' => $art_no]);
        $chip = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$chip) {
            echo 'Error: Chip no encontrado.';
            exit;
        }

        $existencia = (int)$chip['EXISTENCIA'];
        $precio     = (float)$chip['PRECIO'];

        if ($cantidad > $existencia) {
            echo 'Error: No hay suficientes unidades disponibles.';
            exit;
        }

        // Verificar usuario
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

        // Actualizar existencia
        $stmtUpdate = $connection->prepare("
            UPDATE chips SET EXISTENCIA = EXISTENCIA - :cantidad WHERE ART_NO = :art_no
        ");
        $stmtUpdate->execute([
            ':cantidad' => $cantidad,
            ':art_no'   => $art_no
        ]);

        // Registrar en prim14a
        $stmtPrim = $connection->prepare("
            INSERT INTO prim14a (
                NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA,
                CANT0MULTA, REAL_VAL, DEUDOR
            ) VALUES (
                :numero, :nombre, :nompar, 'PRÉSTAMO', CURRENT_DATE, :encargado, :hora,
                :cantidad, :real_val, 1
            )
        ");
        $stmtPrim->execute([
            ':numero'    => $usuario['numero_control'],
            ':nombre'    => $usuario['nombre_completo'],
            ':nompar'    => $art_no,
            ':encargado' => $admin_id,
            ':hora'      => date('H:i:s'),
            ':cantidad'  => $cantidad,
            ':real_val'  => $precio
        ]);

        // Insertar o actualizar en prestamos
        $stmtSelect = $connection->prepare("
            SELECT CANT0MULTA FROM prestamos
            WHERE NUMERO = :numero AND NOMPAR = :nompar AND TIPMOV = 'PRESTAMO'
        ");
        $stmtSelect->execute([
            ':numero' => $usuario['numero_control'],
            ':nompar' => $art_no
        ]);
        $existePrestamo = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($existePrestamo) {
            // Actualizar préstamo existente
            $stmtUpdatePrestamo = $connection->prepare("
                UPDATE prestamos SET CANT0MULTA = CANT0MULTA + :cantidad
                WHERE NUMERO = :numero AND NOMPAR = :nompar AND TIPMOV = 'PRESTAMO'
            ");
            $stmtUpdatePrestamo->execute([
                ':cantidad' => $cantidad,
                ':numero'   => $usuario['numero_control'],
                ':nompar'   => $art_no
            ]);
        } else {
            // Insertar nuevo préstamo
            $stmtInsertPrestamo = $connection->prepare("
                INSERT INTO prestamos (
                    TIPO, NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA,
                    CANT0MULTA, REAL_VAL, DEUDOR
                ) VALUES (
                    :tipo, :numero, :nombre, :nompar, 'PRESTAMO', CURRENT_DATE, :encargado, :hora,
                    :cantidad, :real_val, 1
                )
            ");
            $stmtInsertPrestamo->execute([
                ':tipo'      => 'Chip',
                ':numero'    => $usuario['numero_control'],
                ':nombre'    => $usuario['nombre_completo'],
                ':nompar'    => $art_no,
                ':encargado' => $admin_id,
                ':hora'      => date('H:i:s'),
                ':cantidad'  => $cantidad,
                ':real_val'  => $precio
            ]);
        }

        echo 'success';
    } catch (PDOException $e) {
        echo 'Error en la base de datos: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo 'Error: Método no permitido.';
}
