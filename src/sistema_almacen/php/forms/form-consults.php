<?php
// Incluir el módulo de conexión y consultas de equipos
include_once '../modules/conn.php';
include_once '../modules/bkend-consults.php';

// Procesar solicitudes de edición o eliminación
processRequest($connection);

// Obtener todos los equipos para mostrarlos
$equipos = getEquipments($connection);

// Si no se obtuvieron equipos, asegurarse de que $equipos sea un array vacío
if ($equipos === false) {
    $equipos = [];
}

// Verificar si se seleccionó un equipo para editar
$equipoEditar = null; // Initialize the variable to avoid undefined variable warning
if (isset($_GET['numero_ser'])) {
    $equipoEditar = getEquipmentBySerial($connection, $_GET['numero_ser']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Metadatos de la página -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Equipos</title>
    
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles-consults.css">
</head>

<body>
    <!-- Inicio del contenido visible de la página -->
    <div>
        <!-- Sección de navegación principal -->
        <nav id="main-nav">
            <!-- Contenedor para el logotipo -->
            <div class="logo-container">
                <!-- Imagen del logotipo de la UDLAP con un diseño adaptativo (responsive) -->
                <img src="../../resources/images/logo-udlap.png" alt="" class="img-fluid" id="logo-udlap">
            </div>
            <!-- Contenedor para los encabezados -->
            <div class="header-container" id="header-container">
                <!-- Encabezado que muestra el nombre del departamento -->
                <header id="nav-departamento">
                    Departamento de Electrónica
                </header>
                <!-- Encabezado que muestra el título del sistema -->
                <header id="nav-titulo">
                    -Consulta y búsqueda de equipos-
                </header>
            </div>
        </nav>
    </div>

    <div class="container mt-4">
        <!-- Contenedor para el botón de retorno y el campo de búsqueda -->
        <div class="search-container">
            <!-- Botón de retorno -->
            <div class="button-return mr-3">
                <a href="../../index.html">
                    <img src="../../resources/images/icon-return.png" alt="Volver">
                </a>
            </div>
            <!-- Campo de búsqueda -->
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar equipo por cualquier campo..." onkeyup="filterTable()">
        </div>

        <!-- Mensajes de éxito tras editar o eliminar -->
        <div id="message"></div>

        <!-- Tabla de equipos -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="equipmentsTable">
                <thead class="thead-dark">
                    <tr>
                        <th>APARATO</th>
                        <th>MARCA</th>
                        <th>MODELO</th>
                        <th>NUMERO_SER</th>
                        <th>ENCARGADO</th>
                        <th>POSICION</th>
                        <th>STATUS</th>
                        <th>FECHA_INV</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($equipos)): ?>
                        <?php foreach ($equipos as $equipo): ?>
                            <tr id="row-<?= htmlspecialchars($equipo['numero_ser'] ?? '') ?>">
                                <td class="editable"><?= htmlspecialchars($equipo['aparato'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($equipo['marca'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($equipo['modelo'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($equipo['numero_ser'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($equipo['encargado'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($equipo['posicion'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($equipo['status'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($equipo['fecha_inv'] ?? 'N/A') ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit" data-id="<?= htmlspecialchars($equipo['numero_ser'] ?? '') ?>">Editar</button>
                                    <button class="btn btn-danger btn-sm delete" data-id="<?= htmlspecialchars($equipo['numero_ser'] ?? '') ?>">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay equipos disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Controles de paginación -->
    <div class="pagination-buttons">
        <button id="prevBtn" class="btn btn-primary" onclick="prevPage()">Anterior</button>
        <span id="pageIndicator" class="page-indicator"></span>
        <button id="nextBtn" class="btn btn-primary" onclick="nextPage()">Siguiente</button>
    </div>

    <!-- Script de Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Habilitar la edición de los campos al presionar el botón "Editar"
            $(document).on('click', '.edit', function() {
                var row = $(this).closest('tr'); // Obtener la fila correspondiente
                row.find('.editable').attr('contenteditable', 'true').focus(); // Hacer los campos editables

                // Cambiar el botón "Editar" a "Guardar"
                $(this).removeClass('edit btn-primary').addClass('save btn-success').text('Guardar');
            });

            // Guardar los cambios al presionar "Guardar"
            $(document).on('click', '.save', function() {
                var row = $(this).closest('tr');
                var id = $(this).data('id');
                var aparato = row.find('td:eq(0)').text();
                var marca = row.find('td:eq(1)').text();
                var modelo = row.find('td:eq(2)').text();
                var encargado = row.find('td:eq(4)').text();
                var posicion = row.find('td:eq(5)').text();
                var status = row.find('td:eq(6)').text();

                // Mostrar datos en consola para depuración
                console.log({
                    id: id,
                    aparato: aparato,
                    marca: marca,
                    modelo: modelo,
                    encargado: encargado,
                    posicion: posicion,
                    status: status
                });

                // Enviar los datos mediante AJAX
                $.ajax({
                    url: '../modules/mod-edit_equipment.php',
                    method: 'POST',
                    data: {
                        id: id,
                        aparato: aparato,
                        marca: marca,
                        modelo: modelo,
                        encargado: encargado,
                        posicion: posicion,
                        status: status
                    },
                    success: function(response) {
            console.log(response);  // Verificar la respuesta del servidor
            if (response === 'success') {
                $('#message').html('<div class="alert alert-success">Editado correctamente</div>');
            } else {
                $('#message').html('<div class="alert alert-danger">' + response + '</div>');
            }
        },
        error: function() {
            $('#message').html('<div class="alert alert-danger">Error al editar</div>');
        }
    });
}); 

            // Eliminar el equipo al presionar "Eliminar"
            $(document).on('click', '.delete', function() {
                var id = $(this).data('id');
                var row = $(this).closest('tr');

                if (confirm('¿Estás seguro de eliminar este equipo?')) {
                    $.ajax({
                        url: 'delete_equipment.php',
                        method: 'POST',
                        data: { id: id },
                        success: function(response) {
                            row.remove();
                            $('#message').html('<div class="alert alert-success">Eliminado correctamente</div>');
                        },
                        error: function() {
                            $('#message').html('<div class="alert alert-danger">Error al eliminar</div>');
                        }
                    });
                }
            });

            // Funciones de paginación
            var currentPage = 1;
            var rowsPerPage = 10;

            function paginateTable() {
                var table, tr, i, start, end;
                table = document.getElementById("equipmentsTable");
                tr = table.getElementsByTagName("tr");
                start = (currentPage - 1) * rowsPerPage;
                end = start + rowsPerPage;

                for (i = 1; i < tr.length; i++) {
                    if (i >= start && i < end) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
                document.getElementById("pageIndicator").textContent = "Página " + currentPage;
            }

            function nextPage() {
                currentPage++;
                paginateTable();
            }

            function prevPage() {
                if (currentPage > 1) {
                    currentPage--;
                    paginateTable();
                }
            }

            window.onload = function() {
                paginateTable();
            };
        });
    </script>
</body>
</html>


