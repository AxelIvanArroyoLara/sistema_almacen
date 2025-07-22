<?php
include_once '../modules/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $aparato = $_POST['aparato'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $encargado = $_POST['encargado'];
    $posicion = $_POST['posicion'];
    $status = $_POST['status'];

    $query = "UPDATE equipo SET aparato = :aparato, marca = :marca, modelo = :modelo, encargado = :encargado, posicion = :posicion, status = :status WHERE numero_ser = :id";
    $stmt = $connection->prepare($query);
    
    $stmt->bindParam(':aparato', $aparato);
    $stmt->bindParam(':marca', $marca);
    $stmt->bindParam(':modelo', $modelo);
    $stmt->bindParam(':encargado', $encargado);
    $stmt->bindParam(':posicion', $posicion);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);
//181763
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
