<?php
// Iniciar sesión si no ha sido iniciada aún
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para obtener préstamos según el usuario autenticado
function getPrestamos($connection, $user_id) {
    // Validar que se proporcionó el ID del usuario
    if (empty($user_id)) {
        error_log("getPrestamos: ID de usuario no proporcionado.");
        return [];
    }

    try {
        // Consulta segura con parámetro nombrado
        $sql = "SELECT * FROM prim14a WHERE NUMERO = :user_id ORDER BY fecha DESC, hora DESC";
        $stmt = $connection->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute([':user_id' => $user_id]);

        // Retornar todos los resultados como arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Registrar el error en el log del servidor
        error_log("Error al obtener préstamos: " . $e->getMessage());
        return false;
    }
}

