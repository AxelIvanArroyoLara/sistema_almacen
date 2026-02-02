<?php
// Incluir el módulo de conexión
include_once '../modules/conn.php';

// Función para eliminar una conexión
function deleteConexion($connection, $art_no) {
    // SQL para eliminar una conexión
    $sql = "DELETE FROM conexion WHERE ART_NO = :art_no";
    $stmt = $connection->prepare($sql);

    // Ejecutar la consulta con el parámetro ART_NO
    if (!$stmt->execute([':art_no' => $art_no])) {
        // Si ocurre un error en la eliminación
        echo "Error al eliminar la conexión";
        return false;
    }

    // Si todo sale bien
    return true;
}

// Verificar que el parámetro 'art_no' ha sido enviado por POST
if (isset($_POST['art_no'])) {
    $art_no = $_POST['art_no'];

    // Intentar eliminar la conexión
    if (deleteConexion($connection, $art_no)) {
        echo 'success';  // Responder con éxito
    } else {
        echo 'Error al eliminar la conexión';  // Responder con error
    }
} else {
    echo 'ART_NO no proporcionado';  // Si no se recibe ART_NO
}
?>

