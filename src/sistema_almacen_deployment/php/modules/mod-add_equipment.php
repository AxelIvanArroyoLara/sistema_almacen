<?php
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aparato     = $_POST['aparato'] ?? '';
    $marca       = $_POST['marca'] ?? '';
    $modelo      = $_POST['modelo'] ?? '';
    $numero_ser  = $_POST['numero_ser'] ?? '';
    $encargado   = $_POST['encargado'] ?? '';
    $posicion    = $_POST['posicion'] ?? '';
    $status      = $_POST['status'] ?? '';
    $fecha_inv   = $_POST['fecha_inv'] ?? '';

    try {
        $sql = "INSERT INTO equipo (aparato, marca, modelo, numero_ser, encargado, posicion, status, fecha_inv)
                VALUES (:aparato, :marca, :modelo, :numero_ser, :encargado, :posicion, :status, :fecha_inv)";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':aparato'    => $aparato,
            ':marca'      => $marca,
            ':modelo'     => $modelo,
            ':numero_ser' => $numero_ser,
            ':encargado'  => $encargado,
            ':posicion'   => $posicion,
            ':status'     => $status,
            ':fecha_inv'  => $fecha_inv
        ]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'error: ' . $e->getMessage();
    }
}
