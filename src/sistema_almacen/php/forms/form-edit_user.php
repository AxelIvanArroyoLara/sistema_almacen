<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        input[type="text"], input[type="email"], input[type="password"] {
            text-align: center;
        }
    </style>
</head>
<body>
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
                    -Editar Usuario-
                </header>
            </div>
        </nav>
    </div>
    <br>
    <header class="encabezado-wrapper my-5">
        <a href="../../index.html" class="button-return" aria-label="Volver">
            <img src="../../resources/images/icon-return.png" alt="">
        </a>

        <h3 class="mb-1">Edición de datos</h3>
        <p class="mb-0">Por favor, manipule la información cuidadosamente:</p>
    </header>
    <div class="container-general">
        <div class="box-white">
            <!-- El formulario envía los datos al backend en bkend-edit_user.php -->
            <form id="user-form" action="../modules/bkend-edit_user.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="user-id">ID</label>
                    <input type="text" class="form-control" id="user-id" name="user-id" readonly>
                </div>
                <div class="form-group">
                    <label for="user-name">Nombre Completo</label>
                    <input type="text" class="form-control" id="user-name" name="user-name" required>
                </div>
                <div class="form-group">
                    <label for="user-level">Nivel</label>
                    <input type="text" class="form-control" id="user-level" name="user-level">
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
                        <input type="email" class="form-control" id="user-email" name="user-email">
                    </div>
                    <div class="form-group">
                        <label for="user-phone">Teléfono</label>
                        <input type="text" class="form-control" id="user-phone" name="user-phone" pattern="[0-9]+">
                    </div>
                    <div class="form-group">
                        <label for="user-password">Nuevo PIN (Déjelo en blanco si no desea cambiarlo)</label>
                        <input type="password" class="form-control" id="user-password" name="user-password" pattern="[0-9]+">
                    </div>
                    <div class="form-group">
                        <label for="user-password-confirm">Confirmar PIN</label>
                        <input type="password" class="form-control" id="user-password-confirm" name="user-password-confirm" pattern="[0-9]+">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-lg" id="button-enviar">Actualizar</button>
                <br><br>
            </form>
        </div>
    </div>

    <!-- Script para seleccionar el contenido del campo al hacer click y para precargar los datos -->
    <script>
        // Añadir el evento focus a todos los campos de texto, email y password para seleccionar su contenido
        document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.select();
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Obtener el parámetro 'user_id' de la URL
            var userId = new URLSearchParams(window.location.search).get('user_id');

            // Verificar si el parámetro 'user_id' existe
            if (userId) {
                // Llamar al módulo PHP para obtener los datos del usuario
                fetch(`../modules/mod_fetch-user-data.php?user_id=${userId}`)
                    .then(response => response.json())  // Parsear la respuesta como JSON
                    .then(data => {
                        console.log("Datos del usuario recibidos:", data);

                        // Verificar si no hubo error
                        if (!data.error) {
                            // Función para manejar valores nulos y reemplazarlos por 0
                            function checkNull(value) {
                                return value === null ? "0" : value;
                            }

                            // Llenar los campos del formulario con los datos del usuario
                            document.getElementById('user-id').value = checkNull(data.NUMERO); 
                            document.getElementById('user-name').value = checkNull(data.NOMBRE); 
                            document.getElementById('user-level').value = checkNull(data.NIVEL); 
                            document.getElementById('user-absences').value = checkNull(data.FALTAS); 
                            document.getElementById('user-status').value = checkNull(data.STATUS); 
                            document.getElementById('user-email').value = checkNull(data.E_MAIL); 
                            document.getElementById('user-phone').value = checkNull(data.TELEFONO);
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos del usuario:', error);
                    });
            } else {
                alert("ID de usuario no proporcionado.");
                window.location.href = "../../index.html";
            }
        });
    </script>

</body>
</html>
