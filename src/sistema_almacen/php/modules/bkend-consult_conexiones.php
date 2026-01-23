<?php
// Incluir el archivo de conexión a la base de datos
include_once 'conn.php';

// Obtener los datos de la tabla 'conexiones'
function getConexiones($connection) {
    $sql = "SELECT * FROM conexion";
    $stmt = $connection->prepare($sql);
    
    // Verificar errores en la ejecución de la consulta
    if (!$stmt->execute()) {
        echo "Error ejecutando la consulta: "  . "Procesar datos";
        return false; // Devolver false en caso de error
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener una conexión específica por su ART_NO
function getConexionByArtNo($connection, $art_no) {
    $sql = "SELECT * FROM conexion WHERE ART_NO = :art_no";
    $stmt = $connection->prepare($sql);
    
    // Verificar errores en la ejecución de la consulta
    if (!$stmt->execute([':art_no' => $art_no])) {
        echo "Error ejecutando la consulta: "  . "Procesar datos";
        return false;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Actualizar una conexión
function updateConexion($connection, $art_no, $posicionx, $etiqueta, $conector, $descripcion1, $descripcion2, $existencia) {
    $sql = "UPDATE conexion 
            SET POSICIONX = :posicionx, ETIQUETA = :etiqueta, CONECTOR = :conector, 
                DESCRIP1 = :descripcion1, DESCRIP2 = :descripcion2, EXISTENCIA = :existencia
            WHERE ART_NO = :art_no";
    $stmt = $connection->prepare($sql);
    
    // Verificar errores en la ejecución de la consulta
    if (!$stmt->execute([ 
        ':art_no' => $art_no,
        ':posicionx' => $posicionx,
        ':etiqueta' => $etiqueta,
        ':conector' => $conector,
        ':descripcion1' => $descripcion1,
        ':descripcion2' => $descripcion2,
        ':existencia' => $existencia
    ])) {
        echo "Error actualizando la conexión: "  . "Procesar datos";
    }
}

// Eliminar una conexión
function deleteConexion($connection, $art_no) {
    $sql = "DELETE FROM conexion WHERE ART_NO = :art_no";
    $stmt = $connection->prepare($sql);
    
    // Verificar errores en la ejecución de la consulta
    if (!$stmt->execute([':art_no' => $art_no])) {
        echo "Error eliminando la conexión: "  . "Procesar datos";
    }
}

// Procesar solicitudes de edición y eliminación
function processRequest($connection) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar si se ha solicitado una edición
        if (isset($_POST['art_no'], $_POST['posicionx'], $_POST['etiqueta'], $_POST['conector'], $_POST['descripcion1'], $_POST['descripcion2'], $_POST['existencia'])) {
            $art_no = $_POST['art_no'];
            $posicionx = $_POST['posicionx'];
            $etiqueta = $_POST['etiqueta'];
            $conector = $_POST['conector'];
            $descripcion1 = $_POST['descripcion1'];
            $descripcion2 = $_POST['descripcion2'];
            $existencia = $_POST['existencia'];
            
            updateConexion($connection, $art_no, $posicionx, $etiqueta, $conector, $descripcion1, $descripcion2, $existencia);
            echo "success"; // Responder con éxito al frontend
        }
        // Verificar si se ha solicitado una eliminación
        elseif (isset($_POST['id'])) {
            $art_no = $_POST['id'];
            deleteConexion($connection, $art_no);
            echo "success"; // Responder con éxito al frontend
        }
    }
}
?>

