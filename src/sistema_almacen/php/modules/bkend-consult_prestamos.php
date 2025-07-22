<?php  
// Incluir el archivo de conexión a la base de datos
include_once 'conn.php';

// Obtener los datos de la tabla 'prim14a'
function getPrestamos($connection) {
    $sql = "SELECT * FROM prim14a";
    $stmt = $connection->prepare($sql);

    // Busca errores en cada solicitud
    if (!$stmt->execute()) {
        echo "Error executing query: " . $stmt->errorInfo()[2];
        return false;
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener un préstamo por número de ID
function getPrestamoByID($connection, $numero) {
    $sql = "SELECT * FROM equipo WHERE numero = :numero";
    $stmt = $connection->prepare($sql);

    // Busca errores en cada solicitud
    if (!$stmt->execute(['numero' => $numero])) {
        echo "Error executing query: " . $stmt->errorInfo()[2];
        return false;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Actualizar registros
function updatePrestamo($connection, $numero, $nompar, $tipmov, $fecha, $encargado, $hora, $cant0multa, $real_val, $deudor) {
    $sql = "UPDATE prim14a SET numero = :numero, nombre = :nombre, nompar = :nompar, tipmov = :tipmov, fecha = :fecha, encargado = :encargado,
            hora = :hora, cant0multa = :cant0multa, real_val = :real_val, deudor = :deudor WHERE numero = :numero";
    $stmt = $connection->prepare($sql);

    if (!$stmt->execute([
        ':numero' => $numero,
        ':nombre' => $nombre,
        ':nompar' => $nompar,
        ':tipmov'      => $tipmov,
        ':fecha'       => $fecha,
        ':encargado'   => $encargado,
        ':hora'        => $hora,
        ':cant0multa'  => $cant0multa,
        ':real_val'    => $real_val,
        ':deudor'      => $deudor
    ])) {
        echo "Error updating the registry: " . $stmt->errorInfo()[2];
    }
}

// Eliminar registros
function deletePrestamo($connection, $numero) {
    $sql = "DELETE FROM prim14a WHERE numero = :numero";
    $stmt = $connection->prepare($sql);

    // Busca errores en cada solicitud
    if (!$stmt->execute([':numero' => $numero])) {
        echo "Error deleting the registry: " . $stmt->errorInfo()[2]; 
    }
}

// Procesar ediciones y eliminaciones
function processRequest($connection) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Procesar la edición
        $numero     = $_POST['numero'] ?? '';
        $nompar     = $_POST['nompar'] ?? '';
        $tipmov     = $_POST['tipmov'] ?? '';
        $fecha      = $_POST['fecha'] ?? '';
        $encargado  = $_POST['encargado'] ?? '';
        $hora       = $_POST['hora'] ?? '';
        $cant0multa = $_POST['cant0multa'] ?? '';
        $real_val   = $_POST['real_val'] ?? '';
        $deudor     = $_POST['deudor'] ?? '';
        updatePrestamo($connection, $numero, $nompar, $tipmov, $fecha, $encargado, $hora, $cant0multa, $real_val, $deudor);
        header("Location: search_prestamo.php?success=edit");
        exit;
    }
    if (isset($_POST['delete'])) {
            if (deletePrestamo($connection, $numero)) {
                header("Location: search_prestamo.php?success=delete");
            } else {
                header("Location: search_prestamo.php?error=delete");
            }
            exit;
        }
    }
?>

<?php
// Incluir el archivo de conexión a la base de datos
include_once '../modules/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $query = "DELETE FROM prim14a WHERE numero = :id";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>