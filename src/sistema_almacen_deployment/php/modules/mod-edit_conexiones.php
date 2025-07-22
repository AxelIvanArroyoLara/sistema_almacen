<?php
// Incluir el archivo de conexión a la base de datos
include_once '../modules/conn.php';

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos enviados por el cliente
    $art_no = $_POST['art_no'] ?? '';
    $posicionx = $_POST['posicionx'] ?? '';
    $etiqueta = $_POST['etiqueta'] ?? '';
    $conector = $_POST['conector'] ?? '';
    $descripcion1 = $_POST['descripcion1'] ?? '';
    $descripcion2 = $_POST['descripcion2'] ?? '';
    $existencia = $_POST['existencia'] ?? '';

    // Validar que todos los datos necesarios estén presentes
    if (empty($art_no) || empty($posicionx) || empty($etiqueta) || empty($conector) || empty($descripcion1) || empty($descripcion2) || $existencia === '') {
        echo 'Error: Datos incompletos.';
        exit;
    }

    // Sanitizar los datos manualmente
    $art_no = htmlspecialchars($art_no, ENT_QUOTES, 'UTF-8');
    $posicionx = htmlspecialchars($posicionx, ENT_QUOTES, 'UTF-8');
    $etiqueta = htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8');
    $conector = htmlspecialchars($conector, ENT_QUOTES, 'UTF-8');
    $descripcion1 = htmlspecialchars($descripcion1, ENT_QUOTES, 'UTF-8');
    $descripcion2 = htmlspecialchars($descripcion2, ENT_QUOTES, 'UTF-8');
    $existencia = intval($existencia);

    try {
        // Preparar la consulta SQL de actualización
        $sql = "UPDATE conexion 
                SET POSICIONX = :posicionx, 
                    ETIQUETA = :etiqueta, 
                    CONECTOR = :conector, 
                    DESCRIP1 = :descripcion1, 
                    DESCRIP2 = :descripcion2, 
                    EXISTENCIA = :existencia 
                WHERE ART_NO = :art_no";

        $stmt = $connection->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute([
            ':art_no' => $art_no,
            ':posicionx' => $posicionx,
            ':etiqueta' => $etiqueta,
            ':conector' => $conector,
            ':descripcion1' => $descripcion1,
            ':descripcion2' => $descripcion2,
            ':existencia' => $existencia
        ]);

        // Verificar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            echo 'success'; // Respuesta para indicar éxito
        } else {
            echo 'Error: No se encontró la conexión o no se realizaron cambios.';
        }
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        echo 'Error en la base de datos: ' . $e->getMessage();
    }
} else {
    echo 'Error: Método no permitido.';
}
