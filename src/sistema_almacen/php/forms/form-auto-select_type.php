<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Tipo</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navegación principal -->
    <div>
        <nav id="main-nav">
            <div class="logo-container">
                <img src="../../resources/images/logo-udlap.png" alt="Logo UDLAP" class="img-fluid" id="logo-udlap">
            </div>
            <div class="header-container" id="header-container">
                <header id="nav-departamento">
                    Departamento de Electrónica
                </header>
                <header id="nav-titulo">
                    -Selector de consultas del laboratorio de electrónica-
                </header>
            </div>
        </nav>
    </div>

    <!-- Contenedor principal -->
    <div class="container">
        <div class="row justify-content-center mt-5">
            <!-- Encabezado -->
            <div class="col-12 text-center">
                <div class="button-return mb-3">
                    <a href="credentials/form-auto-check_admin_id.php">
                        <img src="../../resources/images/icon-return.png" alt="Volver">
                    </a>
                </div>
                <h3>Consulta de componentes</h3>
                <p>Por favor, seleccione una de las siguientes opciones:</p>
            </div>
        </div>
        
        <br>
        <br>
        <br>

        <!-- Opciones -->
        <div class="row justify-content-center mt-4">
            <div class="col-sm-10 col-md-3 mb-4">
                <a href="form-auto-consult_equipment.php" class="card-option" id="btn-equipos">
                    <img src="../../resources/images/icon-equipos.png" alt="Equipos">
                    <h3>Equipos</h3>
                </a>
            </div>
            <div class="col-sm-10 col-md-3 mb-4">
                <a href="form-auto-consult_conexiones.php" class="card-option" id="btn-conexiones">
                    <img src="../../resources/images/icon-conexiones.png" alt="Conexiones">
                    <h3>Conexiones</h3>
                </a>
            </div>
            <div class="col-sm-10 col-md-3 mb-4">
                <a href="form-auto-consult_chips.php" class="card-option" id="btn-chips">
                    <img src="../../resources/images/icon-chips.png" alt="Chips">
                    <h3>Chips</h3>
                </a>
            </div>
            <div class="col-sm-10 col-md-3 mb-4">
                <a href="form-auto-consult_prestamos.php" class="card-option" id="btn-prestamos">
                    <img src="../../resources/images/icon-prestamo.png" alt="Préstamos">
                    <h3>Mis préstamos</h3>
                </a>
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>
    <!-- Footer -->
    <footer class="mt-5 text-center">
        <p>© 2024 Departamento de Electrónica - UDLAP</p>
    </footer>
</body>
</html>