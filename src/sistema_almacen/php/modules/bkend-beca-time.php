<?php
session_start();
include_once '../modules/conn.php';

if (!isset($_SESSION['user-id']) || $_SESSION['user-id'] !== $_POST['user_id']) {
    die("Acceso no autorizado.");
}

$user_id = $_SESSION['user-id'];
$accion = $_POST['accion'] ?? '';
$fecha_actual = date('Y-m-d');
$hora_actual = date('Y-m-d H:i:s');

try {
    if ($accion === 'entrada') {
        $insert = $connection->prepare("INSERT INTO control_becario (user_id, fecha, hora_entrada) VALUES (?, ?, ?)");
        $insert->execute([$user_id, $fecha_actual, $hora_actual]);

    } elseif ($accion === 'salida') {
        $stmt = $connection->prepare("SELECT * FROM control_becario WHERE user_id = ? AND hora_salida IS NULL ORDER BY hora_entrada DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registro) {
            $entrada = new DateTime($registro['hora_entrada']);
            $salida = new DateTime($hora_actual);
            $intervalo = $entrada->diff($salida);
            $horas = $intervalo->format('%H:%I:%S');

            $update = $connection->prepare("UPDATE control_becario SET hora_salida = ?, horas_trabajadas = ? WHERE id = ?");
            $update->execute([$hora_actual, $horas, $registro['id']]);
        }
    }

    header("Location: ../forms/form-beca-time.php");
    exit;

} catch (PDOException $e) {
    die("Error al procesar asistencia: " . $e->getMessage());
}
