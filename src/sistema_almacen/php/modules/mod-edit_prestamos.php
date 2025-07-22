<?php
include_once '../modules/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = $_POST['id'] ?? '';
    $nombre      = $_POST['nombre'] ?? '';
    $nompar      = $_POST['nompar'] ?? '';
    $tipmov      = $_POST['tipmov'] ?? '';
    $fecha       = $_POST['fecha'] ?? '';
    $encargado   = $_POST['encargado'] ?? '';
    $hora        = $_POST['hora'] ?? '';
    $cant0multa  = $_POST['cant0multa'] ?? '';
    $real_val    = $_POST['real_val'] ?? '';
    $deudor      = $_POST['deudor'] ?? '';

    if (empty($id) || empty($nombre)) {
        echo 'error: datos faltantes';
        exit;
    }

    $query = "UPDATE prim14a SET 
        nombre = :nombre,
        nompar = :nompar,
        tipmov = :tipmov,
        fecha = :fecha,
        encargado = :encargado,
        hora = :hora,
        cant0multa = :cant0multa,
        real_val = :real_val,
        deudor = :deudor
        WHERE numero = :id";

    $stmt = $connection->prepare($query);

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':nompar', $nompar);
    $stmt->bindParam(':tipmov', $tipmov);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':encargado', $encargado);
    $stmt->bindParam(':hora', $hora);
    $stmt->bindParam(':cant0multa', $cant0multa);
    $stmt->bindParam(':real_val', $real_val);
    $stmt->bindParam(':deudor', $deudor);
    $stmt->bindParam(':id', $id); // Este es el valor correcto para el WHERE

    if ($stmt->execute()) {
        echo 'success';
    } else {
        $error = $stmt->errorInfo();
        echo 'error: ' . $error[2];
    }
}



?>
