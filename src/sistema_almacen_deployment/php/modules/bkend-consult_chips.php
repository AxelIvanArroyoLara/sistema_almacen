<?php
// Conexión a la base de datos
include_once 'conn.php';

// Obtener todos los chips
function getChips($connection) {
    $sql = "SELECT * FROM chips";
    $stmt = $connection->prepare($sql);

    if (!$stmt->execute()) {
        echo "Error al ejecutar la consulta: " . $stmt->errorInfo()[2];
        return [];
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Actualizar un chip
function updateChip($connection, $art_no, $posicionx, $etiqueta, $conector, $descrip1, $descrip2, $existencia) {
    $sql = "UPDATE chips SET 
                POSICIONX = :posicionx,
                ETIQUETA = :etiqueta,
                CONECTOR = :conector,
                DESCRIP1 = :descrip1,
                DESCRIP2 = :descrip2,
                EXISTENCIA = :existencia
            WHERE ART_NO = :art_no";

    $stmt = $connection->prepare($sql);

    if (!$stmt->execute([
        ':art_no'     => $art_no,
        ':posicionx'  => $posicionx,
        ':etiqueta'   => $etiqueta,
        ':conector'   => $conector,
        ':descrip1'   => $descrip1,
        ':descrip2'   => $descrip2,
        ':existencia' => $existencia
    ])) {
        echo "Error al actualizar: " . $stmt->errorInfo()[2];
    } else {
        echo "success";
    }
}

// Eliminar un chip
function deleteChip($connection, $art_no) {
    $sql = "DELETE FROM chips WHERE ART_NO = :art_no";
    $stmt = $connection->prepare($sql);

    if (!$stmt->execute([':art_no' => $art_no])) {
        echo "Error al eliminar: " . $stmt->errorInfo()[2];
    } else {
        echo "success";
    }
}

// Procesar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        updateChip(
            $connection,
            $_POST['art_no'],
            $_POST['posicionx'],
            $_POST['etiqueta'],
            $_POST['conector'],
            $_POST['descrip1'],
            $_POST['descrip2'],
            $_POST['existencia']
        );
        exit;
    }

    if (isset($_POST['delete'])) {
        deleteChip($connection, $_POST['art_no']);
        exit;
    }
}
?>

