<?php
// Incluir el archivo de conexión a la base de datos
include_once 'conn.php';

// Obtener los datos de la tabla 'equipo'
function getEquipments($connection) {
    $sql = "SELECT * FROM equipo";
    $stmt = $connection->prepare($sql);
    
    // Check for errors in query execution
    if (!$stmt->execute()) {
        echo "Error executing query: "  . "Procesar datos";
        return false; // Return false on error
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener un equipo específico por su número de serie
function getEquipmentBySerial($connection, $numero_ser) {
    $sql = "SELECT * FROM equipo WHERE numero_ser = :numero_ser";
    $stmt = $connection->prepare($sql);
    
    // Check for errors in query execution
    if (!$stmt->execute([':numero_ser' => $numero_ser])) {
        echo "Error executing query: "  . "Procesar datos";
        return false;
    }

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Actualizar un equipo
function updateEquipment($connection, $numero_ser, $aparato, $marca, $modelo, $encargado, $posicion, $status, $fecha_inv) {
    $sql = "UPDATE equipo SET aparato = :aparato, marca = :marca, modelo = :modelo, encargado = :encargado, 
            posicion = :posicion, status = :status, fecha_inv = :fecha_inv WHERE numero_ser = :numero_ser";
    $stmt = $connection->prepare($sql);
    
    // Check for errors in query execution
    if (!$stmt->execute([
        ':numero_ser' => $numero_ser,
        ':aparato' => $aparato,
        ':marca' => $marca,
        ':modelo' => $modelo,
        ':encargado' => $encargado,
        ':posicion' => $posicion,
        ':status' => $status,
        ':fecha_inv' => $fecha_inv
    ])) {
        echo "Error updating equipment: "  . "Procesar datos";
    }
}

// Eliminar un equipo
function deleteEquipment($connection, $numero_ser) {
    $sql = "DELETE FROM equipo WHERE numero_ser = :numero_ser";
    $stmt = $connection->prepare($sql);
    
    // Check for errors in query execution
    if (!$stmt->execute([':numero_ser' => $numero_ser])) {
        echo "Error deleting equipment: "  . "Procesar datos";
    }
}

// Procesar la edición o eliminación dependiendo de la acción
function processRequest($connection) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Procesar la edición
        if (isset($_POST['edit'])) {
            $numero_ser = $_POST['numero_ser'];
            $aparato = $_POST['aparato'];
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $encargado = $_POST['encargado'];
            $posicion = $_POST['posicion'];
            $status = $_POST['status'];
            $fecha_inv = $_POST['fecha_inv'];
            updateEquipment($connection, $numero_ser, $aparato, $marca, $modelo, $encargado, $posicion, $status, $fecha_inv);
            header("Location: search_equipment.php?success=edit");
            exit;
        }

        // Procesar la eliminación
        if (isset($_POST['delete'])) {
            $numero_ser = $_POST['numero_ser'];
            deleteEquipment($connection, $numero_ser);
            header("Location: search_equipment.php?success=delete");
            exit;
        }
    }
}
?>

<?php
// Incluir el archivo de conexión a la base de datos
include_once '../modules/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $query = "DELETE FROM equipo WHERE numero_ser = :id";
    $stmt = $connection->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>

