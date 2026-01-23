<?php
session_start();
include_once '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = filter_input(INPUT_POST, 'admin-id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (empty($admin_id)) {
        die("Faltan datos obligatorios.");
    }

    try {
        // Preparar la consulta SQL para buscar el ID en la tabla empleado
        $sql_check_empleado = "SELECT * FROM empleado WHERE NUMERO = :admin_id";
        $stmt_empleado = $connection->prepare($sql_check_empleado);
        $stmt_empleado->execute([':admin_id' => $admin_id]);
        $admin_exists = $stmt_empleado->fetch(PDO::FETCH_ASSOC);

        // Verificar si el administrador existe
        if ($admin_exists) {
            $_SESSION['admin-id'] = $admin_id;
            header("Location: ../../forms/form-estadisticas.php");
            exit;
        } else {
            die("Credenciales de administrador incorrectas.");
        }

    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }

} else {
    die("Solicitud no válida.");
}
?>
