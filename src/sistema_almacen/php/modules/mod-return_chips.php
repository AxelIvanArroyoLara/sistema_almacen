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
        // Verificar chip
        $stmt = $connection->prepare("SELECT PRECIO FROM chips WHERE ART_NO = :art_no");
        $stmt->execute([':art_no' => $art_no]);
        $chip = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$chip) {
            echo 'Error: Chip no encontrado.';
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

        // Aumentar existencia
        $stmtUpdate = $connection->prepare("
            UPDATE chips SET EXISTENCIA = EXISTENCIA + :cantidad WHERE ART_NO = :art_no
        ");
        $stmtUpdate->execute([
            ':cantidad' => $cantidad,
            ':art_no'   => $art_no
        ]);

        // Registrar en prim14a
        $stmtInsert = $connection->prepare("
            INSERT INTO prim14a (
                NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA,
                CANT0MULTA, REAL_VAL, DEUDOR
            ) VALUES (
                :numero, :nombre, :nompar, 'DEVOLUCIÓN', CURRENT_DATE, :encargado, :hora,
                :cantidad, 0, 0
            )
        ");
        $stmtInsert->execute([
            ':numero'    => $usuario['numero_control'],
            ':nombre'    => $usuario['nombre_completo'],
            ':nompar'    => $art_no,
            ':encargado' => $admin_id,
            ':hora'      => date('H:i:s'),
            ':cantidad'  => $cantidad
        ]);

        // Validar préstamo activo
        $stmtSelect = $connection->prepare("
            SELECT CANT0MULTA FROM prestamos
            WHERE NUMERO = :numero AND NOMPAR = :nompar AND TIPMOV = 'PRESTAMO'
        ");
        $stmtSelect->execute([
            ':numero' => $usuario['numero_control'],
            ':nompar' => $art_no
        ]);
        $prestamo = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if (!$prestamo) {
            error_log("MOD-RETURN-CHIPS: No se encontró préstamo activo para usuario {$usuario['numero_control']}, chip {$art_no}");
            echo 'Error: No se encontró préstamo activo.';
            exit;
        }

        $restante = (int)$prestamo['CANT0MULTA'] - $cantidad;
        error_log("MOD-RETURN-CHIPS: Usuario {$usuario['numero_control']}, chip {$art_no}, cantidad actual: {$prestamo['CANT0MULTA']}, devolviendo: {$cantidad}, restante: {$restante}");

        if ($restante <= 0) {
            // Eliminar el préstamo
            error_log("MOD-RETURN-CHIPS: Eliminando préstamo completo");
            $stmtDelete = $connection->prepare("
                DELETE FROM prestamos
                WHERE NUMERO = :numero AND NOMPAR = :nompar AND TIPMOV = 'PRESTAMO'
            ");
            $stmtDelete->execute([
                ':numero' => $usuario['numero_control'],
                ':nompar' => $art_no
            ]);
            error_log("MOD-RETURN-CHIPS: Préstamo eliminado, filas afectadas: " . $stmtDelete->rowCount());
        } else {
            // Actualizar préstamo
            error_log("MOD-RETURN-CHIPS: Actualizando préstamo a cantidad: {$restante}");
            $stmtUpdate = $connection->prepare("
                UPDATE prestamos SET CANT0MULTA = :restante
                WHERE NUMERO = :numero AND NOMPAR = :nompar AND TIPMOV = 'PRESTAMO'
            ");
            $stmtUpdate->execute([
                ':restante' => $restante,
                ':numero'   => $usuario['numero_control'],
                ':nompar'   => $art_no
            ]);
            error_log("MOD-RETURN-CHIPS: Préstamo actualizado, filas afectadas: " . $stmtUpdate->rowCount());
        }

        echo 'success';
    } catch (PDOException $e) {
        echo 'Error en la base de datos: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo 'Error: Método no permitido.';
}

