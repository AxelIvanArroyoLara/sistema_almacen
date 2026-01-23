<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Cambio de Semestre</title>
    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/styles-adduser.css">
    <link rel="stylesheet" href="../../css/styles-check_id.css">
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
                <header id="nav-departamento">
                    Departamento de Electrónica
                </header>
                <header id="nav-titulo">
                    -Proceso de Cambio de Semestre-
                </header>
            </div>
        </nav>
    </div>
    <br>
    <!-- Contenedor principal -->
    <div class="container-general">

        <div class="box-white">
            <form action="php/modules/admin/bkend-change-semester.php" method="POST" autocomplete="off">
                <div class="form-group text-center">
                    <br><br>
                    <header class="encabezado-wrapper my-5">
                        <a href="../../index.html" class="button-return" aria-label="Volver">
                            <img src="../../resources/images/icon-return.png" alt="">
                        </a>
                        <h4><strong>¿Está seguro que desea realizar el cambio de semestre?</strong></h4>
                        <p class="text-danger mt-3">
                            Esta acción actualizará automáticamente el semestre en curso de <strong>todos los usuarios</strong> en la base de datos.
                        </p>
                    </header>
                    <p class="text-muted">
                        Esta operación no puede deshacerse.
                    </p>
                    <br>
                    <button type="submit" class="btn btn-danger btn-lg">Sí, realizar cambio de semestre</button>
                    <br><br>
                    <a href="../../index.html" class="btn btn-secondary btn-sm">Cancelar y volver</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
