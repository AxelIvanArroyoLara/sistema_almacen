<?php

// Incluir el archivo de conexión a la base de datos
include_once 'conn.php';

// Verificar si el formulario fue enviado usando el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger los datos del formulario sin filtrar
    $user_id = $_POST['user-id'];
    $user_name = $_POST['user-name'];
    $user_level = $_POST['user-level'];
    $user_absences = $_POST['user-absences'];
    $user_status = $_POST['user-status'];
    $user_email = $_POST['user-email'];
    $user_phone = $_POST['user-phone'];
    $user_password = $_POST['user-password'];
    $user_password_confirm = $_POST['user-password-confirm'];

    // Validación de que las contraseñas coinciden
    if ($user_password !== $user_password_confirm) {
        die("Las contraseñas no coinciden.");
    }

    // Validación de que el PIN sea numérico
    if (!is_numeric($user_password)) {
        die("El PIN debe contener solo números.");
    }

    // Hash del PIN usando password_hash()
    $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

    // Preparar la consulta SQL para insertar los datos en la tabla 'usuarios'
    $sql_insert = "INSERT INTO usuarios (NUMERO, NOMBRE, NIVEL, FALTAS, STATUS, E_MAIL, TELEFONO, CLAVE) 
                   VALUES (:user_id, :user_name, :user_level, :user_absences, :user_status, :user_email, :user_phone, :hashed_password)";

    // Preparar la declaración SQL
    $stmt = $connection->prepare($sql_insert);

    // Ejecutar la declaración SQL con los valores del formulario
    try {
        $stmt->execute([
            ':user_id' => $user_id,
            ':user_name' => $user_name,
            ':user_level' => $user_level,
            ':user_absences' => $user_absences,
            ':user_status' => $user_status,
            ':user_email' => $user_email,
            ':user_phone' => $user_phone,
            ':hashed_password' => $hashed_password
        ]);

        // Confirmar al usuario que la inserción fue exitosa
        //echo "¡Usuario registrado exitosamente!";
        header("Location: success.php");
        exit;
    } catch (PDOException $e) {
        // Mostrar un mensaje de error si la inserción falla
        echo "Error: " . $e->getMessage();
    }

} else {
    // Mostrar un mensaje si el formulario no fue enviado correctamente
    die("Solicitud no válida.");
}

?>