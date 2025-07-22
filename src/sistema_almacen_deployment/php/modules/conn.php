<?php

$server = "db";
$user = "root";
$password = "rootpass";
$database = "almacen";

try {
    $connection = new PDO("mysql:host=$server;dbname=$database;charset=utf8", $user, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

?>
