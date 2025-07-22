<?php

// Incluir el archivo de conexión a la base de datos
include_once 'conn.php';

// Verificar si el formulario fue enviado usando el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger y sanitizar los datos del formulario
    $user_id = filter_input(INPUT_POST, 'user-id');
    $user_name = filter_input(INPUT_POST, 'user-name');
    $user_level = filter_input(INPUT_POST, 'user-level');
    $user_absences = filter_input(INPUT_POST, 'user-absences');
    $user_status = filter_input(INPUT_POST, 'user-status');
    $user_email = filter_input(INPUT_POST, 'user-email');
    $user_phone = filter_input(INPUT_POST, 'user-phone');
    $user_password = filter_input(INPUT_POST, 'user-password');
    $user_password_confirm = filter_input(INPUT_POST, 'user-password-confirm');

    // Validación de que las contraseñas coinciden (solo si se cambian)
    if (!empty($user_password) && $user_password !== $user_password_confirm) {
        die("Las contraseñas no coinciden.");
    }

    // Actualizar el PIN si es necesario
    if (!empty($user_password)) {
        if (!is_numeric($user_password)) {
            die("El PIN debe contener solo números.");
        }
        // Hash del nuevo PIN
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

        // Consulta SQL para actualizar con PIN
        $sql_update = "UPDATE usuarios SET NOMBRE = :user_name, NIVEL = :user_level, FALTAS = :user_absences, STATUS = :user_status, E_MAIL = :user_email, TELEFONO = :user_phone, CLAVE = :hashed_password WHERE NUMERO = :user_id";
    } else {
        // Consulta SQL sin modificar el PIN
        $sql_update = "UPDATE usuarios SET NOMBRE = :user_name, NIVEL = :user_level, FALTAS = :user_absences, STATUS = :user_status, E_MAIL = :user_email, TELEFONO = :user_phone WHERE NUMERO = :user_id";
    }

    // Preparar la declaración SQL
    $stmt = $connection->prepare($sql_update);

    // Ejecutar la declaración SQL con los valores del formulario
    try {
        // Si se cambia el PIN, enviar el hash, si no, sin el hash
        $params = [
            ':user_id' => $user_id,
            ':user_name' => $user_name,
            ':user_level' => $user_level,
            ':user_absences' => $user_absences,
            ':user_status' => $user_status,
            ':user_email' => $user_email,
            ':user_phone' => $user_phone
        ];

        if (!empty($user_password)) {
            $params[':hashed_password'] = $hashed_password;
        }

        $stmt->execute($params);

        // Confirmar al usuario que la actualización fue exitosa
        //echo "¡Usuario actualizado exitosamente!";
        header("Location: success.php");
        exit;
    } catch (PDOException $e) {
        // Mostrar un mensaje de error si la actualización falla
        echo "Error: " . $e->getMessage();
    }

} else {
    // Mostrar un mensaje si el formulario no fue enviado correctamente
    die("Solicitud no válida.");
}

?>
