<?php
// Incluir el archivo de conexión a la base de datos
include_once 'conn.php';

// Obtener el ID del usuario de la URL y sanitizarlo correctamente
$user_id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($user_id) {
    try {
        // Preparar la consulta SQL para obtener los datos del usuario
        $sql_fetch = "SELECT * FROM usuarios WHERE NUMERO = :user_id";
        $stmt = $connection->prepare($sql_fetch);
        $stmt->execute([':user_id' => $user_id]);

        // Verificar si el usuario existe
        if ($stmt->rowCount() > 0) {
            // Obtener los datos del usuario y devolver como JSON
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            header('Content-Type: application/json'); // Asegurar que se devuelva JSON
            echo json_encode($user);
        } else {
            // Devolver error en formato JSON si no se encuentra el usuario
            echo json_encode(['error' => 'Usuario no encontrado']);
        }
    } catch (Exception $e) {
        // Manejo de excepciones y devolución de un error en JSON
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // Devolver un error en JSON si no se proporciona un ID válido
    echo json_encode(['error' => 'ID de usuario no válido']);
}
?>
