<?php
require_once 'conn.php';
require_once 'security.php';
requireAdminAuthentication();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $art_no     = $_POST['ART_NO']     ?? '';
    $posicionx  = $_POST['POSICIONX']  ?? '';
    $etiqueta   = $_POST['ETIQUETA']   ?? '';
    $conector   = $_POST['CONECTOR']   ?? '';
    $descrip1   = $_POST['DESCRIP1']   ?? '';
    $descrip2   = $_POST['DESCRIP2']   ?? '';
    $existencia = $_POST['EXISTENCIA'] ?? '';

    try {
        $sql = "INSERT INTO conexion (ART_NO, POSICIONX, ETIQUETA, CONECTOR, DESCRIP1, DESCRIP2, EXISTENCIA)
                VALUES (:art_no, :posicionx, :etiqueta, :conector, :descrip1, :descrip2, :existencia)";
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':art_no'     => $art_no,
            ':posicionx'  => $posicionx,
            ':etiqueta'   => $etiqueta,
            ':conector'   => $conector,
            ':descrip1'   => $descrip1,
            ':descrip2'   => $descrip2,
            ':existencia' => $existencia
        ]);
        echo 'success';
    } catch (PDOException $e) {
        echo 'error: ' . $e->getMessage();
    }
}

