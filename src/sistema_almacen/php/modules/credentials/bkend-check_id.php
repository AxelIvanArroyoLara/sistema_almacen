<?php
//root:2C603

// Incluir el archivo de conexión a la base de datos
include_once '../conn.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recoger y sanitizar el ID del formulario (forma moderna)
    $user_id = htmlspecialchars(trim($_POST['user-id'] ?? ''));

    // Preparar la consulta SQL para buscar el ID en la base de datos
    $sql_check = "SELECT * FROM usuarios WHERE NUMERO = :user_id";
    $stmt = $connection->prepare($sql_check);
    $stmt->execute([':user_id' => $user_id]);

    // Redirigir según si el ID existe o no
    if ($stmt->rowCount() > 0) {
        header("Location: ../../forms/form-edit_user.php?user_id=" . urlencode($user_id));
    } else {
        header("Location: ../../forms/form-add_user.php?user_id=" . urlencode($user_id));
    }
    exit; // Muy importante: detener ejecución tras redirección

} else {
    // Si el formulario no fue enviado correctamente
    http_response_code(400); // Código de solicitud incorrecta
    echo "❌ Solicitud no válida.";
}
?>
