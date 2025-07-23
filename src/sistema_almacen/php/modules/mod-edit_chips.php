<?php
include_once '../modules/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $art_no     = $_POST['art_no'];
    $posicionx  = $_POST['posicionx'];
    $etiqueta   = $_POST['etiqueta'];
    $conector   = $_POST['conector'];
    $descrip1   = $_POST['descrip1'];
    $descrip2   = $_POST['descrip2'];
    $minimo     = $_POST['minimo'];
    $existencia = $_POST['existencia'];
    $pedidos    = $_POST['pedidos'];
    $conector2  = $_POST['conector_2'];
    $pedido     = $_POST['pedido'];
    $precio     = $_POST['precio'];
    $fecha_adq  = $_POST['fecha_adq'];
    $proveedor  = $_POST['proveedor'];
    $chkx       = $_POST['chkx'];
    $cont_1     = $_POST['cont_1'];
    $cont_2     = $_POST['cont_2'];
    $no_provee  = $_POST['no_provee'];
    $stock      = $_POST['stock'];

    try {
        $query = "UPDATE chips SET 
                    POSICIONX = :posicionx,
                    ETIQUETA = :etiqueta,
                    CONECTOR = :conector,
                    DESCRIP1 = :descrip1,
                    DESCRIP2 = :descrip2,
                    MINIMO = :minimo,
                    EXISTENCIA = :existencia,
                    PEDIDOS = :pedidos,
                    CONECTOR_2 = :conector2,
                    PEDIDO = :pedido,
                    PRECIO = :precio,
                    FECHA_ADQ = :fecha_adq,
                    PROVEEDOR = :proveedor,
                    CHKX = :chkx,
                    CONT_1 = :cont_1,
                    CONT_2 = :cont_2,
                    NO_PROVEE = :no_provee,
                    STOCK = :stock
                WHERE ART_NO = :art_no";

        $stmt = $connection->prepare($query);
        $stmt->bindParam(':posicionx', $posicionx);
        $stmt->bindParam(':etiqueta', $etiqueta);
        $stmt->bindParam(':conector', $conector);
        $stmt->bindParam(':descrip1', $descrip1);
        $stmt->bindParam(':descrip2', $descrip2);
        $stmt->bindParam(':minimo', $minimo);
        $stmt->bindParam(':existencia', $existencia);
        $stmt->bindParam(':pedidos', $pedidos);
        $stmt->bindParam(':conector2', $conector2);
        $stmt->bindParam(':pedido', $pedido);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':fecha_adq', $fecha_adq);
        $stmt->bindParam(':proveedor', $proveedor);
        $stmt->bindParam(':chkx', $chkx);
        $stmt->bindParam(':cont_1', $cont_1);
        $stmt->bindParam(':cont_2', $cont_2);
        $stmt->bindParam(':no_provee', $no_provee);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':art_no', $art_no);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } catch (PDOException $e) {
        echo 'error: ' . $e->getMessage();
    }
}
?>
