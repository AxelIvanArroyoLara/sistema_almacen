<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar y sanitizar datos del POST
    $numero_ser = trim($_POST['numero_ser'] ?? '');
    $user_id    = trim($_POST['user_id'] ?? '');
    $admin_id   = trim($_POST['admin_id'] ?? '');

    if (empty($numero_ser) || empty($user_id) || empty($admin_id)) {
        echo 'error: Faltan datos obligatorios.';
        exit;
    }

    try {
        // Obtener datos del usuario
        $stmtUser = $connection->prepare("
            SELECT NUMERO AS numero_control, NOMBRE AS nombre_completo 
            FROM usuarios 
            WHERE NUMERO = :user_id
        ");
        $stmtUser->execute([':user_id' => $user_id]);
        $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

        // Verificar existencia del usuario
        if (!$usuario) {
            echo 'error: Usuario no encontrado.';
            exit;
        }

        // Insertar en prim14a
        $sqlInsert = "
            INSERT INTO prim14a (
                NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA, CANT0MULTA, REAL_VAL, DEUDOR
            ) VALUES (
                :numero, :nombre, :nompar, :tipmov, CURRENT_DATE, :encargado, :hora, 1, 0, 1
            )
        ";
        $stmtInsert = $connection->prepare($sqlInsert);
        $stmtInsert->execute([
            ':numero'    => $usuario['numero_control'],
            ':nombre'    => $usuario['nombre_completo'],
            ':nompar'    => $numero_ser,
            ':tipmov'    => 'PRESTAMO',
            ':encargado' => $admin_id,
            ':hora'      => date('Hi')
        ]);

        // Actualizar el status del equipo a "PRESTADO"
        $stmtUpdate = $connection->prepare("
            UPDATE equipo SET status = 'PRESTADO' WHERE numero_ser = :numero_ser
        ");
        $stmtUpdate->execute([':numero_ser' => $numero_ser]);

        echo 'success';
    } catch (PDOException $e) {
        echo 'error: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo 'error: Método no permitido.';
}

