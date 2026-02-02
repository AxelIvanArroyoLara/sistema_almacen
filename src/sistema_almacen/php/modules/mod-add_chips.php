<?php
require_once 'conn.php';
require_once 'security.php';

requireAdminAuthentication();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $art_no     = $_POST['ART_NO']     ?? '';
    $posicionx  = $_POST['POSICIONX']  ?? '';
    $etiqueta   = $_POST['ETIQUETA']   ?? '';
    $conector   = $_POST['CONECTOR']   ?? '';
    $descrip1   = $_POST['DESCRIP1']   ?? '';
    $descrip2   = $_POST['DESCRIP2']   ?? '';
    $minimo     = $_POST['MINIMO']     ?? '';
    $existencia = $_POST['EXISTENCIA'] ?? '';
    $pedidos    = $_POST['PEDIDOS']    ?? '';
    $conector2  = $_POST['CONECTOR_2'] ?? '';
    $pedido     = $_POST['PEDIDO']     ?? '';
    $precio     = $_POST['PRECIO']     ?? '';
    $fecha_adq  = $_POST['FECHA_ADQ']  ?? '';
    $proveedor  = $_POST['PROVEEDOR']  ?? '';
    $chkx       = $_POST['CHKX']       ?? '';
    $cont_1     = $_POST['CONT_1']     ?? '';
    $cont_2     = $_POST['CONT_2']     ?? '';
    $no_provee  = $_POST['NO_PROVEE']  ?? '';
    $stock      = $_POST['STOCK']      ?? '';
}

    try {
    $sql = "INSERT INTO chips (
                ART_NO, POSICIONX, ETIQUETA, CONECTOR, DESCRIP1, DESCRIP2, MINIMO,
                EXISTENCIA, PEDIDOS, CONECTOR_2, PEDIDO, PRECIO, FECHA_ADQ,
                PROVEEDOR, CHKX, CONT_1, CONT_2, NO_PROVEE, STOCK
            ) VALUES (
                :art_no, :posicionx, :etiqueta, :conector, :descrip1, :descrip2, :minimo,
                :existencia, :pedidos, :conector2, :pedido, :precio, :fecha_adq,
                :proveedor, :chkx, :cont_1, :cont_2, :no_provee, :stock
            )";

    $stmt = $connection->prepare($sql);
    $stmt->execute([
        ':art_no'     => $art_no,
        ':posicionx'  => $posicionx,
        ':etiqueta'   => $etiqueta,
        ':conector'   => $conector,
        ':descrip1'   => $descrip1,
        ':descrip2'   => $descrip2,
        ':minimo'     => $minimo,
        ':existencia' => $existencia,
        ':pedidos'    => $pedidos,
        ':conector2'  => $conector2,
        ':pedido'     => $pedido,
        ':precio'     => $precio,
        ':fecha_adq'  => $fecha_adq,
        ':proveedor'  => $proveedor,
        ':chkx'       => $chkx,
        ':cont_1'     => $cont_1,
        ':cont_2'     => $cont_2,
        ':no_provee'  => $no_provee,
        ':stock'      => $stock
    ]);

    echo 'success';
} catch (PDOException $e) {
    echo 'error: ' . $e->getMessage();
}
