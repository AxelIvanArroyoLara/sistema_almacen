<?php
session_start();
include_once '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id_input = trim((string) filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $user_password_input = trim((string) filter_input(INPUT_POST, 'user-password', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if (empty($user_password_input) || empty($user_id_input)) {
        die("Faltan datos obligatorios.");
    }

    try {
        // Verificar que el usuario existe
        $stmt_user = $connection->prepare("SELECT * FROM usuarios WHERE NUMERO = :user_id");
        $stmt_user->execute([':user_id' => $user_id_input]);
        $user_exists = $stmt_user->fetch(PDO::FETCH_ASSOC);
        // Validar contraseña del usuario
        $stmt_auth = $connection->prepare("SELECT * FROM usuarios WHERE NUMERO = :user_id AND CLAVE = :user_password");
        $stmt_auth->execute([
            ':user_id' => $user_id_input,
            ':user_password' => $user_password_input
        ]);
        $user_auth = $stmt_auth->fetch(PDO::FETCH_ASSOC);

        // Verificar autenticación completa
        if ($user_exists && $user_auth) {
            $_SESSION['user-id'] = $user_id_input;

            header("Location: ../../forms/form-beca-time.php");
            exit;
        } else {
            header("Location: ../failure.php");
            exit;
        }

    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
} else {
    die("Solicitud no válida.");
}
