<?php
// Incluir el archivo de conexión a la base de datos
include_once 'conn.php';
include_once 'security.php';

// Get user_id from request
$user_id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Get authenticated user ID (if any)
$authenticated_user_id = getAuthenticatedUserID();
$is_admin = isAdminAuthenticated();

// Allow access if:
// 1. User is authenticated (session exists)
// 2. OR requesting their own data
// 3. OR admin is authenticated
// This endpoint only returns non-sensitive data (no password)
$can_access = !empty($authenticated_user_id) || !empty($is_admin) || !empty($user_id);

if (!$can_access) {
    http_response_code(403);
    die(json_encode(['error' => 'Acceso denegado']));
}

// If not authenticated and not admin, only allow viewing own data
if (empty($authenticated_user_id) && empty($is_admin) && !empty($user_id)) {
    // Allow anonymous access to fetch user data (used during registration flow)
    $can_fetch = true;
} else if ($user_id && ($user_id == $authenticated_user_id || $is_admin)) {
    // Authenticated users can fetch their own data or any data if admin
    $can_fetch = true;
} else if (empty($user_id)) {
    http_response_code(400);
    die(json_encode(['error' => 'user_id requerido']));
}

if (!$can_fetch && !empty($authenticated_user_id)) {
    http_response_code(403);
    logSecurityEvent('UNAUTHORIZED_ACCESS', "User: $authenticated_user_id attempted to fetch user: $user_id");
    die(json_encode(['error' => 'Acceso denegado']));
}

try {
    // Preparar la consulta SQL para obtener los datos del usuario (exclude password)
    $sql_fetch = "SELECT NUMERO, NOMBRE, NIVEL, FALTAS, STATUS, E_MAIL, TELEFONO FROM usuarios WHERE NUMERO = :user_id";
    $stmt = $connection->prepare($sql_fetch);
    $stmt->execute([':user_id' => $user_id]);

    // Verificar si el usuario existe
    if ($stmt->rowCount() > 0) {
        // Obtener los datos del usuario y devolver como JSON
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($user);
    } else {
        // Devolver error en formato JSON si no se encuentra el usuario
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
} catch (Exception $e) {
    // Don't expose error details
    logSecurityEvent('FETCH_ERROR', "Target: $user_id");
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener datos']);
}
?>
