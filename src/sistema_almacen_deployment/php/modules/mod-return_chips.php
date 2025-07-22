<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $art_no = $_POST['art_no'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $admin_id = $_POST['admin_id'] ?? '';

    if (empty($art_no) || empty($user_id) || empty($admin_id)) {
        echo 'error: Datos faltantes';
        exit;
    }

    try {
        $stmtUser = $connection->prepare("SELECT NUMERO AS numero, NOMBRE AS nombre FROM usuarios WHERE NUMERO = :user_id");
        $stmtUser->execute([':user_id' => $user_id]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        $stmt = $connection->prepare("INSERT INTO prim14a (NUMERO, NOMBRE, NOMPAR, TIPMOV, FECHA, ENCARGADO, HORA, CANT0MULTA, REAL_VAL, DEUDOR)
                                      VALUES (:numero, :nombre, :nompar, 'DEVOLUCION', CURRENT_DATE, :encargado, :hora, 0, 0, 0)");
        $stmt->execute([
            ':numero' => $user['numero'],
            ':nombre' => $user['nombre'],
            ':nompar' => $art_no,
            ':encargado' => $admin_id,
            ':hora' => date('Hi'),
        ]);

        $connection->prepare("UPDATE chips SET EXISTENCIA = EXISTENCIA + 1 WHERE ART_NO = :art_no")
                   ->execute([':art_no' => $art_no]);

        echo 'success';
    } catch (PDOException $e) {
        echo 'error: ' . $e->getMessage();
    }
}
?>
