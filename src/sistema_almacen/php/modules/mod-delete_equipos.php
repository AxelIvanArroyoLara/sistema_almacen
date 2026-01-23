<?php
// Incluir el módulo de conexión a la base de datos
include_once '../modules/conn.php';

// Verificar si se recibió el ID del equipo
if (isset($_POST['id'])) {
    // Obtener el ID del equipo desde la solicitud POST
    $id = $_POST['id'];

    // Preparar la consulta SQL para eliminar el equipo por su número de serie
    $sql = "DELETE FROM equipo WHERE numero_ser = :id";

    try {
        // Preparar la consulta
        $stmt = $connection->prepare($sql);
        //181763
        // Vincular el parámetro 'id' a la consulta
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo 'success';  // Devolver respuesta exitosa
        } else {
            echo 'Error al eliminar el equipo.';  // Si la consulta no se ejecuta correctamente
        }
    } catch (PDOException $e) {
        // Si ocurre un error durante la ejecución de la consulta
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo 'ID del equipo no proporcionado.';  // Si no se recibe el ID
}
?>

