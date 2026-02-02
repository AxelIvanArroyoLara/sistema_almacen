<?php

// Incluir el archivo de conexión a la base de datos
include_once '../conn.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar los datos del formulario
    $admin_id = filter_input(INPUT_POST, 'admin-id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    //$admin_clave = filter_input(INPUT_POST, 'admin-password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validar que los campos no estén vacíos
    if (empty($admin_id) /*|| empty($admin_clave)*/) {
        die("Faltan datos obligatorios.");
    }

    try {
        // Preparar la consulta SQL para buscar el ID en la tabla empleado
        $sql_check_empleado = "SELECT * FROM empleado WHERE NUMERO = :admin_id";
        $stmt_empleado = $connection->prepare($sql_check_empleado);
        $stmt_empleado->execute([':admin_id' => $admin_id]);
        $admin_exists = $stmt_empleado->fetch(PDO::FETCH_ASSOC);

        // Preparar la consulta SQL para buscar la clave en la tabla usuarios
        /*$sql_check_usuario = "SELECT * FROM usuarios WHERE CLAVE = :admin_clave AND NUMERO = :admin_id";
        $stmt_usuario = $connection->prepare($sql_check_usuario);
        $stmt_usuario->execute([':admin_clave' => $admin_clave, ':admin_id' => $admin_id]);*/

        // Redirigir al formulario de selección de tipo
        if ($admin_exists) {
            header("Location: ../../../php/forms/form-select_type.php?user_id=" . urlencode($admin_id));
            exit;
        } else {
            die("Credenciales de administrador incorrectas.");
        }

    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        die("Error en la base de datos: " . $e->getMessage());
    }

} else {
    // Si el formulario no fue enviado correctamente
    //181763
    die("Solicitud no válida.");
}

?>



