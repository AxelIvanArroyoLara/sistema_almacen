<?php

// Incluir el archivo de conexión a la base de datos
include_once 'conn.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recoger y sanitizar el ID del formulario
    $user_id = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_STRING);

    // Preparar la consulta SQL para buscar el ID en la base de datos
    $sql_check = "SELECT * FROM usuarios WHERE NUMERO = :user_id";
    $stmt = $connection->prepare($sql_check);
    $stmt->execute([':user_id' => $user_id]);

    // Verificar si el ID existe en la base de datos
    if ($stmt->rowCount() > 0) {
        // Si el ID existe, redirigir al formulario de edición con los datos del usuario
        header("Location: editar_usuario.php?user_id=" . urlencode($user_id));
        exit;
    } else {
        // Si el ID no existe, redirigir al formulario para añadir un nuevo usuario
        header("Location: agregar_usuario.php?user_id=" . urlencode($user_id));
        exit;
    }

} else {
    // Si el formulario no fue enviado correctamente
    die("Solicitud no válida.");
}

?>