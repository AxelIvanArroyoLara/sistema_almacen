<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <!-- Enlace a hojas de estilo -->
    <link rel="stylesheet" href="../../css/styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/script-adduser.js"></script>
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
                    -Registro de usuarios-
                </header>
            </div>
        </nav>
    </div>
    <br>
    <!-- Contenedor principal para el formulario -->
    <header class="encabezado-wrapper my-5">
        <a href="../../index.html" class="button-return" aria-label="Volver">
            <img src="../../resources/images/icon-return.png" alt="">
        </a>

        <h3 class="mb-1">Registro de usuarios</h3>
        <p class="mb-0">Por favor, ingrese la información solicitada:</p>
    </header>
    <div class="container-general">
        <div class="box-white">
            <form id="user-form" action="../modules/bkend-add_user.php" method="POST">
                <div class="form-group">
                    <label for="user-id">ID</label>
                    <input type="text" class="form-control" id="user-id" name="user-id" pattern="[0-9]+" required placeholder="Ingrese su ID de estudiante">
                </div>
                <div class="form-group">
                    <label for="user-name">Nombre Completo</label>
                    <input type="text" class="form-control" id="user-name" name="user-name" placeholder="Ingrese su nombre completo">
                </div>
                <div class="form-group">
                    <label for="user-level">Nivel</label>
                    <input type="text" class="form-control" id="user-level" name="user-level" placeholder="Ingrese su nivel de permisos">
                </div>
                <div class="form-group">
                    <label for="user-absences">Faltas</label>
                    <input type="text" class="form-control" id="user-absences" name="user-absences" pattern="[0-9]+">
                </div>
                <div id="additional-info" class="form-container">
                    <div class="form-group">
                        <label for="user-status">Estado</label>
                        <select class="form-control" id="user-status" name="user-status">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user-email">Email</label>
                        <input type="email" class="form-control" id="user-email" name="user-email" placeholder="Ingrese su E-Mail">
                    </div>
                    <div class="form-group">
                        <label for="user-phone">Teléfono</label>
                        <input type="text" class="form-control" id="user-phone" name="user-phone" pattern="[0-9]+" placeholder="Ingrese su número telefónico">
                    </div>
                    <div class="form-group">
                        <label for="user-password">Nuevo PIN</label>
                        <input type="password" class="form-control" id="user-password" name="user-password" placeholder="Ingrese su nuevo PIN numérico" pattern="[0-9]+">
                    </div>
                    <div class="form-group">
                        <label for="user-password-confirm">Confirmar PIN</label>
                        <input type="password" class="form-control" id="user-password-confirm" name="user-password-confirm" placeholder="Confirme su PIN numérico" pattern="[0-9]+">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" id="button-enviar">Enviar</button>
                <br><br>
            </form>
        </div>
    </div>
    </div>

    <script>
        // Mostrar la alerta automáticamente cuando se carga la página
        window.onload = function() {
            alert("El ID proporcionado no se encuentra en la base de datos. Se procederá a registrarlo como un nuevo usuario.");
        };
    </script>
</body>
</html>
