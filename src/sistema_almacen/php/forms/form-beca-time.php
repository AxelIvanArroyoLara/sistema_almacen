<?php
// Validar y limpiar ID de sesión antes de iniciar
if (isset($_COOKIE['PHPSESSID']) && strlen($_COOKIE['PHPSESSID']) > 40) {
    // ID de sesión demasiado largo, forzar uno nuevo
    unset($_COOKIE['PHPSESSID']);
    setcookie('PHPSESSID', '', time() - 3600, '/');
}
session_start();
// Regenerar ID si es necesario
if (strlen(session_id()) > 40) {
    session_regenerate_id(true);
}
include_once '../modules/conn.php';

if (!isset($_SESSION['user-id'])) {
    header("Location: ../modules/failure.php");
    exit;
}

$user_id = $_SESSION['user-id'];

// Recuperar nombre de usuario
$user_stmt = $connection->prepare("SELECT nombre FROM usuarios WHERE numero = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);
$user_name = $user ? $user['nombre'] : 'Usuario';

// Consultar historial
$stmt = $connection->prepare("SELECT * FROM control_becario WHERE user_id = ? ORDER BY fecha DESC, hora_entrada DESC");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total de horas
$total_segundos = 0;
foreach ($rows as $row) {
    if ($row['horas_trabajadas']) {
        [$h, $m, $s] = explode(':', $row['horas_trabajadas']);
        $total_segundos += $h * 3600 + $m * 60 + $s;
    }
}
$horas_totales = gmdate("H:i:s", $total_segundos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control Becario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Estilos compartidos -->
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/styles-check_id.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Barra superior -->
    <nav id="main-nav">
        <div class="logo-container">
            <img src="../../resources/images/logo-udlap.png" alt="Logo UDLAP" class="img-fluid" id="logo-udlap">
        </div>
        <div class="header-container" id="header-container">
            <header id="nav-departamento">Departamento de Electrónica</header>
            <header id="nav-titulo">-Control de Jornada Becario-</header>
        </div>
    </nav>

    <br>

    <!-- Encabezado -->
    <header class="encabezado-wrapper my-5">
        <a href="../../index.html" class="button-return" aria-label="Volver">
            <img src="../../resources/images/icon-return.png" alt="Regresar">
        </a>
        <h3 class="mb-1">Bienvenido <br> <?= htmlspecialchars($user_name) ?></h3>
        <br>
        <p class="mb-0">Desde aquí puede registrar sus jornadas de servicio becario (entrada y salida del laboratorio).</p>
    </header>

    <!-- Contenedor principal -->
    <div class="container-general">
        <div class="box-white">
            <!-- Total de horas -->
            <h5 class="text-center">Total acumulado: <strong><?= $horas_totales ?></strong></h5>
            <br>
            <!-- Formulario de registro -->
            <form action="../modules/bkend-beca-time.php" method="POST" class="text-center mb-4">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                <button name="accion" value="entrada" class="btn btn-success btn-lg mr-2">Registrar Entrada</button>
                <button name="accion" value="salida" class="btn btn-danger btn-lg">Registrar Salida</button>
                <br><br>
            </form>

            

            <!-- Tabla de registros -->
            <div class="table-responsive px-3">
                <table class="table table-bordered table-hover text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Hora de Entrada</th>
                            <th>Hora de Salida</th>
                            <th>Horas Trabajadas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['fecha']) ?></td>
                                <td><?= htmlspecialchars($r['hora_entrada']) ?></td>
                                <td><?= $r['hora_salida'] ? htmlspecialchars($r['hora_salida']) : '—' ?></td>
                                <td><?= $r['horas_trabajadas'] ?? '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($rows) === 0): ?>
                            <tr>
                                <td colspan="4">No hay registros disponibles.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
