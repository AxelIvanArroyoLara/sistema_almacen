<?php
require_once 'conn.php';
require_once 'security.php';

requireUserAuthentication();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_ser = trim($_POST['numero_ser'] ?? '');
    $admin_id = trim($_POST['admin_id'] ?? '');
    $user_id = trim($_POST['user_id'] ?? '');

    if (empty($numero_ser) || empty($admin_id) || empty($user_id)) {
        echo 'error: Faltan datos obligatorios.';
        exit;
    }

    try {
        // 1. Obtener nombre del usuario
        $stmtUser = $connection->prepare("SELECT NOMBRE AS nombre_completo FROM usuarios WHERE NUMERO = :user_id");
        $stmtUser->execute([':user_id' => $user_id]);
        $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo 'error: Usuario no encontrado.';
            exit;
        }

        // 2. Verificar que el usuario sea quien realizó el préstamo
        $stmtCheck = $connection->prepare("
            SELECT * FROM prestamos 
            WHERE NOMPAR = :numero_ser AND TIPMOV = 'PRESTAMO'
        ");
        $stmtCheck->execute([':numero_ser' => $numero_ser]);
        $prestamo = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$prestamo) {
            echo 'error: No se encontró préstamo activo para este equipo.';
            exit;
        }

        if ($prestamo['NUMERO'] != $user_id) {
            echo 'error: Este equipo fue prestado a otro usuario.';
            exit;
        }

        // 3. Registrar devolución en prim14a
        $sqlInsert = "
            INSERT INTO prim14a (
                NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA, CANT0MULTA, REAL_VAL, DEUDOR
            ) VALUES (
                :numero, :nombre, :nompar, :tipmov, CURRENT_DATE, :encargado, :hora, 0, 0, 0
            )
        ";
        $stmtInsert = $connection->prepare($sqlInsert);
        $stmtInsert->execute([
            ':numero'    => $user_id,
            ':nombre'    => $usuario['nombre_completo'],
            ':nompar'    => $numero_ser,
            ':tipmov'    => 'DEVOLUCIÓN',
            ':encargado' => $admin_id,
            ':hora'      => date('H:i:s')
        ]);

        // 4. Eliminar el préstamo correspondiente
        $stmtDelete = $connection->prepare("
            DELETE FROM prestamos
            WHERE 
                NUMERO = :numero AND
                NOMPAR = :nompar AND
                TIPMOV = 'PRESTAMO'
        ");
        $stmtDelete->execute([
            ':numero' => $user_id,
            ':nompar' => $numero_ser
        ]);

        // 5. Cambiar estado a "DISPONIBLE" en equipo
        $stmtUpdate = $connection->prepare("UPDATE equipo SET status = 'DISPONIBLE' WHERE numero_ser = :numero_ser");
        $stmtUpdate->execute([':numero_ser' => $numero_ser]);

        echo 'success';

    } catch (PDOException $e) {
        echo 'error: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo 'error: Método no permitido.';
}
