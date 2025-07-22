<!DOCTYPE html>
<!-- Declaración del tipo de documento, especificando que es HTML5 -->
<html lang="en">
<!-- Define el idioma del contenido como inglés -->

<head>
    <!-- Metadatos del documento -->
    <meta charset="UTF-8">
    <!-- Especifica la codificación de caracteres como UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Configura la escala y dimensiones para que sea adaptable en dispositivos móviles -->
    <title>preview-almacen</title>
    <!-- Define el título de la página que se mostrará en la pestaña del navegador -->

    <!-- Enlace a la hoja de estilos personalizada para la página de control automático -->
    <link rel="stylesheet" href="../css/control-automatico.css">
    <!-- Enlace a la hoja de estilos general para la página -->
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Enlace al archivo de estilos CSS de Bootstrap desde un CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Contenido principal de la página -->
    <nav id="main-nav">
        <!-- Inicio de la barra de navegación principal -->
        <div class="logo-container">
            <!-- Contenedor para el logotipo -->
            <img src="../resources/images/logo-udlap.png" alt="" class="img-fluid" id="logo-udlap">
            <!-- Imagen del logotipo de la UDLAP, con diseño adaptable a diferentes tamaños de pantalla -->
        </div>
        <div class="header-container" id="header-container">
            <!-- Contenedor para los encabezados de la barra de navegación -->
            <header id="nav-departamento">
                Departamento de Electrónica
                <!-- Primer encabezado que muestra el nombre del departamento -->
            </header>
            <header id="nav-titulo">
                -Control automático de equipo, componentes y manuales-
                <!-- Segundo encabezado que describe la función principal de la página -->
            </header>
        </div>
    </nav>
</div>
<br><br>

<!-- Contenedor principal de la página -->
<div class="container-general">
    <div class="button-return">
        <a href="../index.html">
            <img src="..\resources\images\icon-return.png" alt="">
        </a>
    </div>
    <div class="box-white">
        <!-- Formulario para el control automático -->
        <div class="container mt-5">
            <form id="user-form" action="php/modules/bkend-auto-check_admin_id.php" method="POST">
                <!-- Grupo de campos para la entrada del ID de usuario -->
                <div class="form-group">
                    <label for="user-id">ID</label>
                    <input type="text" class="form-control" id="user-id" name="user-id" required placeholder="Ingrese su ID">
                </div>
                <!-- Grupo de campos para la entrada del nombre del encargado del almacén -->
                <div class="form-group">
                    <label for="user-id">Encargado del almacen</label>
                    <input type="text" class="form-control" id="user-id" name="user-id" required placeholder="Ingrese al encargado del almacén">
                </div>
                <!-- Grupo de campos para la entrada de la contraseña -->
                <div class="form-group">
                    <label for="user-id">Contraseña</label>
                    <input type="text" class="form-control" id="user-id" name="user-id" required placeholder="Ingrese su contraseña">
                </div>
        </div>
        <!-- Botón para enviar el formulario -->
        <button type="" class="btn btn-primary btn-lg" id="button-enviar">Enviar</button>
        <br><br>
    </div>
</div>

<!-- Sección de bienvenida oculta inicialmente -->
<div id="welcome-section" class="hidden">
    <h3 id="welcome-message"></h3>
    <!-- Mensaje de bienvenida dinámico basado en el nombre del usuario -->
    <button class="btn btn-success btn-lg">Botón 1</button>
    <!-- Primer botón de acción -->
    <button class="btn btn-warning btn-lg">Botón 2</button>
    <!-- Segundo botón de acción -->
</div>
</body>
</html>
