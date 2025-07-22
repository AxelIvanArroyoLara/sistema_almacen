<?php
// Incluir el módulo de conexión y consultas de prestamos
include_once '../modules/conn.php';
include_once '../modules/bkend-consult_prestamos.php';

// Obtener todos los registros de préstamos
$prestamos = getPrestamos($connection);

if ($prestamos === false) {
    $prestamos = [];
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Metadatos de la página -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de prestamos</title>
    
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    
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
                    -Consulta y búsqueda de prestamos-
                </header>
            </div>
        </nav>
    </div>
    <br>
    <header class="encabezado-wrapper my-5">
    <a href="../forms/form-select_type.php" class="button-return" aria-label="Volver">
        <img src="../../resources/images/icon-return.png" alt="">
    </a>

    <h3 class="mb-1">Consulta de préstamos del laboratorio</h3>
    <p class="mb-0">Por favor, modifique la información cuidadosamente:</p>

    </header>
    <div class="container mt-4">
        <!-- Contenedor para el botón de retorno y el campo de búsqueda -->
        <div class="search-container">
            
        <br>
        <br>

        <!-- Botón para abrir el modal de agregar -->
            
            <!-- Campo de búsqueda -->
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar prestamo por cualquier campo..." onkeyup="filterTable()">
        </div>
        <br>
        <!-- Mensajes de éxito tras editar o eliminar -->
        <div id="message" style="display: none;" class="alert alert-success"></div> <!-- Barra verde de notificación -->
        
        <!-- Tabla de préstamos -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="prestamosTable">
                <thead class="thead-dark">
                    <tr>
                        <th>NUMERO</th>
                        <th>NOMBRE</th>
                        <th>NOMPAR</th>
                        <th>TIPMOV</th>
                        <th>FECHA</th>
                        <th>ENCARGADO</th>
                        <th>HORA</th>
                        <th>CANT</th>
                        <th>REAL_VAL</th>
                        <th>DEUDOR</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prestamos)): ?>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr id="row-<?= htmlspecialchars($prestamo['NUMERO'] ?? '') ?>">
                                <td class="editable"><?= htmlspecialchars($prestamo['NUMERO'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['NOMBRE'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['NOMPAR'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['TIPMOV'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['FECHA'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['ENCARGADO'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['HORA'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['CANT0MULTA'] ?? '0') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['REAL_VAL'] ?? '0') ?></td>
                                <td class="editable"><?= htmlspecialchars($prestamo['DEUDOR'] ?? '0') ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit" data-id="<?= htmlspecialchars($prestamo['NUMERO'] ?? '') ?>">Editar</button>
                                    <button class="btn btn-danger btn-sm delete" data-id="<?= htmlspecialchars($prestamo['NUMERO'] ?? '') ?>">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay préstamos disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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

                /* ============================================================
                    Variables de paginación
                ============================================================ */
                var currentPage = 1;
                var rowsPerPage = 10;

                /* ============================================================
                    Edición (sin cambios)
                ============================================================ */
                $(document).on('click', '.edit', function() {
                    var row = $(this).closest('tr');
                    row.find('.editable').attr('contenteditable', 'true').focus();
                    $(this).removeClass('edit btn-primary')
                        .addClass('save btn-success')
                        .text('Guardar');
                });

                $(document).on('click', '.save', function() {
                    var row = $(this).closest('tr');
                    var id = $(this).data('id');
                    var numero    = row.find('td:eq(0)').text();
                    var nombre = row.find('td:eq(1)').text();
                    var nompar   = row.find('td:eq(2)').text();
                    var tipmov     = row.find('td:eq(3)').text();
                    var fecha    = row.find('td:eq(4)').text();
                    var encargado = row.find('td:eq(5)').text();
                    var hora  = row.find('td:eq(6)').text();
                    var cant0multa    = row.find('td:eq(7)').text();
                    var real_val = row.find('td:eq(8)').text();
                    var deudor = row.find('td:eq(9)').text();

                    $.ajax({
                        url: '../modules/mod-edit_prestamos.php',
                        method: 'POST',
                        data: {id, numero, nombre, nompar, tipmov, fecha, encargado,
                            hora, cant0multa, real_val, deudor
                        },
                        success: function(resp) {
                            console.log("Respuesta AJAX:", resp);
                            if (resp === 'success') {
                                $('#message').text('Cambios guardados correctamente').fadeIn();
                                row.find('.editable').attr('contenteditable', 'false');
                                row.find('.save').removeClass('save btn-success')
                                                .addClass('edit btn-primary')
                                                .text('Editar');
                                setTimeout(function(){ $('#message').fadeOut(); }, 3000);
                            } else {
                                $('#message').html('<div class="alert alert-danger">' + resp + '</div>');
                            }
                        },
                        error: function() {
                            $('#message').html('<div class="alert alert-danger">Error al editar</div>');
                        }
                    });
                });

                /* ============================================================
                    Eliminación (sin cambios)
                ============================================================ */
                $(document).on('click', '.delete', function() {
                    var id  = $(this).data('id');
                    var row = $(this).closest('tr');
                    if (!confirm('¿Estás seguro de eliminar este equipo?')) return;

                    $.ajax({
                        url: '../modules/mod-delete_prestamos.php',
                        method: 'POST',
                        data: { id: id },
                        success: function(resp) {
                            if (resp === 'success') {
                                row.remove();
                                $('#message').text('Eliminado correctamente').fadeIn();
                                setTimeout(function(){ $('#message').fadeOut(); }, 3000);
                                filterTable();           // Recalcular paginación
                            } else {
                                $('#message').html('<div class="alert alert-danger">' + resp + '</div>');
                            }
                        },
                        error: function() {
                            $('#message').html('<div class="alert alert-danger">Error al eliminar</div>');
                        }
                    });
                });

                // Filtros y paginación
                function paginateTable(rows){
                    console.log('Paginate is active!');
                    var totalPages = Math.max(1, Math.ceil(rows.length / rowsPerPage));
                    currentPage = Math.min(currentPage, totalPages);

                    var start = (currentPage - 1) * rowsPerPage;
                    var end = start + rowsPerPage;

                    rows.forEach(function(row, index) {
                        row.style.display = (index >= start && index < end) ? '' : 'none';
                    });

                    document.getElementById('pageIndicator').textContent =
                    'Página ' + currentPage + ' de ' + totalPages;

                    document.getElementById('prevBtn').disabled = currentPage === 1;
                    document.getElementById('nextBtn').disabled = currentPage === totalPages;
                }

                function filterTable() {
                    var filter = document.getElementById('searchInput').value.toLowerCase();
                    var allRows = Array.from(document.querySelectorAll('#prestamosTable tbody tr'));
                    var visibles = [];

                    allRows.forEach(function(tr) {
                        var texto = tr.textContent.toLowerCase();
                        var coincide = texto.indexOf(filter) !== -1;
                        tr.style.display = coincide ? '' : 'none';
                        if (coincide) visibles.push(tr);
                    });

                    paginateTable(visibles);
                }

                // Botones de navegación
                function nextPage(){
                    ++currentPage;
                    filterTable();
                }

                function prevPage() {
                    if (currentPage > 1) --currentPage;
                    filterTable();
                }

                window.nextPage = nextPage;
                window.prevPage = prevPage;
                window.filterTable = filterTable;

                // Primera carga
                filterTable();

            });
        </script>