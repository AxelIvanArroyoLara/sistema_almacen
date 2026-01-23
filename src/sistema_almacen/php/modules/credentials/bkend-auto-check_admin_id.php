<?php
session_start();
include_once '../conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id_input = trim((string) filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $admin_id_input = trim((string) filter_input(INPUT_POST, 'admin-id', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $user_password_input = trim((string) filter_input(INPUT_POST, 'user-password', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if (empty($admin_id_input) || empty($user_password_input) || empty($user_id_input)) {
        die("Faltan datos obligatorios.");
    }

    try {
        // Verificar que el usuario existe - solo traer CLAVE para validar
        $stmt_user = $connection->prepare("SELECT NUMERO, NOMBRE, CLAVE FROM usuarios WHERE NUMERO = :user_id");
        $stmt_user->execute([':user_id' => $user_id_input]);
        $user_exists = $stmt_user->fetch(PDO::FETCH_ASSOC);

        // Verificar que el administrador existe en la tabla empleado
        $stmt_admin = $connection->prepare("SELECT NUMERO FROM empleado WHERE NUMERO = :admin_id");
        $stmt_admin->execute([':admin_id' => $admin_id_input]);
        $admin_exists = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        // Validar contraseña del usuario usando password_verify
        $user_auth = false;
        if ($user_exists && !empty($user_exists['CLAVE'])) {
            $user_auth = password_verify($user_password_input, $user_exists['CLAVE']);
        }

        // Verificar autenticación completa
        if ($user_exists && $admin_exists && $user_auth) {
            $_SESSION['user-id'] = $user_id_input;
            $_SESSION['admin-id'] = $admin_id_input;

            header("Location: ../../forms/form-auto-select_type.php");
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
