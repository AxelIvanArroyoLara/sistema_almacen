<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de ID de Usuario</title>
    <!-- Enlace a hojas de estilo -->
    <link rel="stylesheet" href="../../../css/styles.css">
    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/script-adduser.js"></script>
</head>
<body>
    <!-- Barra de navegación principal -->
    <div>
        <nav id="main-nav">
            <div class="logo-container">
                <img src="../../../resources/images/logo-udlap.png" alt="Logo UDLAP" class="img-fluid" id="logo-udlap">
            </div>
            <div class="header-container" id="header-container">
                <header id="nav-departamento">
                    Departamento de Electrónica
                </header>
                <header id="nav-titulo">
                    -Registro y modificación de usuarios-
                </header>
            </div>
        </nav>
    </div>

<!-- ────────── ENCABEZADO ────────── -->
<header class="encabezado-wrapper my-5">
    <a href="../../../index.html" class="button-return" aria-label="Volver">
        <img src="../../../resources/images/icon-return.png" alt="">
    </a>

    <h3 class="mb-1">Verificación de credenciales</h3>
    <p class="mb-0">Por favor, ingrese la información solicitada:</p>
</header>



    <!-- Contenedor principal para el formulario -->
    <div class="container-general">
        <div class="box-white">
            <form action="../../modules/credentials/bkend-consults-check_admin_id.php" method="POST">
                <div class="form-group">
                    <br><br>
                    <label for="admin-id">Ingrese el ID de un administrador</label>
                    <br><br>
                    <input type="text" class="form-control" id="admin-id" name="admin-id" required placeholder="Ingrese el ID de un administrador" pattern="[0-9]+">
                    <br><br>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Verificar</button>
                <br><br>
            </form>
        </div>
    </div>
</body>