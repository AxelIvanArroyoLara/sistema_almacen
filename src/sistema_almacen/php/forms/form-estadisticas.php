<?php
session_start();
include_once '../modules/conn.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['admin-id'])) {
    header("Location: credentials/form-estadisticas-check_admin_id.php");
    exit;
}

$query = "
    SELECT p.*
    FROM prestamos p
    INNER JOIN (
        SELECT NUMERO, MAX(FECHA) AS ultima_fecha
        FROM prestamos
        GROUP BY NUMERO
    ) ultimos
    ON p.NUMERO = ultimos.NUMERO AND p.FECHA = ultimos.ultima_fecha
    WHERE p.DEUDOR = 1
    ORDER BY p.FECHA DESC
";

$stmt = $connection->prepare($query);
$stmt->execute();
$deudores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deudores actuales</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegación principal -->
    <div>
        <nav id="main-nav">
            <div class="logo-container">
                <img src="../../resources/images/logo-udlap.png" alt="" class="img-fluid" id="logo-udlap">
            </div>
            <div class="header-container" id="header-container">
                <header id="nav-departamento">Departamento de Electrónica</header>
                <header id="nav-titulo">-Lista de Deudores Actuales-</header>
            </div>
        </nav>
    </div>

    <header class="encabezado-wrapper my-5">
        <a href="../../index.html" class="button-return" aria-label="Volver">
            <img src="../../resources/images/icon-return.png" alt="">
        </a>

        <h3 class="mb-1">Deudores actuales</h3>
        <p class="mb-0">A continuación se muestra la lista de usuarios con préstamos activos sin devolución:</p>
    </header>

    <div class="container-general">
        <div class="box-white table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>NUMERO</th>
                        <th>NOMBRE</th>
                        <th>NOMPAR</th>
                        <th>TIPMOV</th>
                        <th>FECHA</th>
                        <th>ENCARGADO</th>
                        <th>HORA</th>
                        <th>CANT0MULTA</th>
                        <th>REAL_VAL</th>
                        <th>DEUDOR</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if (!empty($deudores)): ?>
                        <?php foreach ($deudores as $fila): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['NUMERO'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['NOMBRE'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['NOMPAR'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['TIPMOV'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['FECHA'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['ENCARGADO'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['HORA'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['CANT0MULTA'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['REAL_VAL'] ?? '') ?></td>
                                <td><?= htmlspecialchars($fila['DEUDOR'] ?? '') ?></td>                    
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10">No hay deudores registrados actualmente.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
