<?php
// Incluir el módulo de conexión
include_once '../modules/conn.php';

// Obtener todas las conexiones
function getConexiones($connection) {
    $sql = "SELECT * FROM conexion"; // Ajusta al nombre exacto de la tabla
    $stmt = $connection->prepare($sql);
    if (!$stmt->execute()) {
        return []; // Si hay un error, devolver un array vacío
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Procesar las conexiones
$conexiones = getConexiones($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Conexiones</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <div>
        <nav id="main-nav">
            <div class="logo-container">
                <img src="../../resources/images/logo-udlap.png" alt="" class="img-fluid" id="logo-udlap">
            </div>
            <div class="header-container">
                <header id="nav-departamento">Departamento de Electrónica</header>
                <header id="nav-titulo">-Consulta y búsqueda de conexiones-</header>
            </div>
        </nav>
    </div>
    <br>
    <header class="encabezado-wrapper my-5">
    <a href="../forms/form-select_type.php" class="button-return" aria-label="Volver">
        <img src="../../resources/images/icon-return.png" alt="">
    </a>

    <h3 class="mb-1">Consulta de equipos del laboratorio</h3>
    <p class="mb-0">Por favor, modifique la información cuidadosamente:</p>
    </header>
    <div class="container mt-4">
        <div class="search-container">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar conexión por cualquier campo..." onkeyup="filterTable()">
        </div>

        <!-- Botón para abrir el modal de agregar nueva conexión -->
        <div class="text-center my-3">
            <button class="btn btn-success" data-toggle="modal" data-target="#addModal">Agregar Nueva Conexión</button>
        </div>

        <br>

        <div id="message" style="display: none;" class="alert alert-success"></div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="conexionTable">
                <thead class="thead-dark">
                    <tr>
                        <th>ART_NO</th>
                        <th>POSICIONX</th>
                        <th>ETIQUETA</th>
                        <th>CONECTOR</th>
                        <th>DESCRIP1</th>
                        <th>DESCRIP2</th>
                        <th>EXISTENCIA</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($conexiones)): ?>
                        <?php foreach ($conexiones as $conexion): ?>
                            <tr id="row-<?= htmlspecialchars($conexion['ART_NO'] ?? '') ?>">
                                <td><?= htmlspecialchars($conexion['ART_NO'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($conexion['POSICIONX'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($conexion['ETIQUETA'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($conexion['CONECTOR'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($conexion['DESCRIP1'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($conexion['DESCRIP2'] ?? 'N/A') ?></td>
                                <td class="editable"><?= htmlspecialchars($conexion['EXISTENCIA'] ?? 'N/A') ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit" data-id="<?= htmlspecialchars($conexion['ART_NO'] ?? '') ?>">Editar</button>
                                    <button class="btn btn-danger btn-sm delete" data-id="<?= htmlspecialchars($conexion['ART_NO'] ?? '') ?>">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay conexiones disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-buttons">
        <button id="prevBtn" class="btn btn-primary" onclick="prevPage()">Anterior</button>
        <span id="pageIndicator" class="page-indicator"></span>
        <button id="nextBtn" class="btn btn-primary" onclick="nextPage()">Siguiente</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            /* ============================================================
               Variables de paginación
            ============================================================ */
            var currentPage = 1;
            var rowsPerPage = 10;
            
    // Habilitar la edición de los campos al presionar el botón "Editar"
    $(document).on('click', '.edit', function() {
        var row = $(this).closest('tr');
        row.find('.editable').attr('contenteditable', 'true').focus();
        $(this).removeClass('edit btn-primary').addClass('save btn-success').text('Guardar');
    });

    // Guardar los cambios al presionar "Guardar"
    $(document).on('click', '.save', function() {
        var row = $(this).closest('tr');
        var id = $(this).data('id'); // ID de la fila (ART_NO)
        var posicionx = row.find('td:eq(1)').text().trim();
        var etiqueta = row.find('td:eq(2)').text().trim();
        var conector = row.find('td:eq(3)').text().trim();
        var descripcion1 = row.find('td:eq(4)').text().trim();
        var descripcion2 = row.find('td:eq(5)').text().trim();
        var existencia = row.find('td:eq(6)').text().trim();

        // AJAX para guardar cambios
        $.ajax({
            url: '../modules/mod-edit_conexiones.php', // Archivo PHP para procesar la edición
            method: 'POST',
            data: { 
                art_no: id, 
                posicionx: posicionx, 
                etiqueta: etiqueta, 
                conector: conector, 
                descripcion1: descripcion1, 
                descripcion2: descripcion2, 
                existencia: existencia 
            },
            success: function(response) {
                if (response.trim() === 'success') {
                    // Mostrar mensaje de éxito
                    $('#message').html('<div class="alert alert-success">Cambios guardados correctamente</div>')
                        .fadeIn().delay(3000).fadeOut();

                    // Desactivar los campos editables
                    row.find('.editable').attr('contenteditable', 'false');

                    // Cambiar el botón "Guardar" a "Editar"
                    row.find('.save').removeClass('save btn-success').addClass('edit btn-primary').text('Editar');
                } else {
                    // Mostrar mensaje de error
                    $('#message').html('<div class="alert alert-danger">Error: ' + response + '</div>')
                        .fadeIn().delay(3000).fadeOut();
                }
            },
            error: function() {
                // Manejar error de AJAX
                $('#message').html('<div class="alert alert-danger">Error al guardar los cambios</div>')
                    .fadeIn().delay(3000).fadeOut();
            }
        });
    });

    // Eliminar la conexión al presionar "Eliminar"
    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');

        if (confirm('¿Estás seguro de eliminar esta conexión?')) {
            $.ajax({
                url: '../modules/mod-delete_conexiones.php',
                method: 'POST',
                data: { art_no: id },
                success: function(response) {
                    if (response.trim() === 'success') {
                        row.remove();
                        $('#message').html('<div class="alert alert-success">Conexión eliminada correctamente</div>')
                            .fadeIn().delay(3000).fadeOut();
                    } else {
                        $('#message').html('<div class="alert alert-danger">Error: ' + response + '</div>')
                            .fadeIn().delay(3000).fadeOut();
                    }
                },
                error: function() {
                    $('#message').html('<div class="alert alert-danger">Error al eliminar la conexión</div>')
                        .fadeIn().delay(3000).fadeOut();
                }
            });
        }
    });

    function paginateTable(rows) {
                var totalPages = Math.max(1, Math.ceil(rows.length / rowsPerPage));
                currentPage    = Math.min(currentPage, totalPages);

                var start = (currentPage - 1) * rowsPerPage;
                var end   = start + rowsPerPage;

                rows.forEach(function(row, idx) {
                    row.style.display = (idx >= start && idx < end) ? '' : 'none';
                });

                document.getElementById('pageIndicator').textContent =
                    'Página ' + currentPage + ' de ' + totalPages;

                document.getElementById('prevBtn').disabled = currentPage === 1;
                document.getElementById('nextBtn').disabled = currentPage === totalPages;
            }

            function filterTable() {
                var filter   = document.getElementById('searchInput').value.toLowerCase();
                var allRows  = Array.from(document.querySelectorAll('#conexionTable tbody tr'));
                var visibles = [];

                allRows.forEach(function(tr) {
                    var texto = tr.textContent.toLowerCase();
                    var coincide = texto.indexOf(filter) !== -1;
                    tr.style.display = coincide ? '' : 'none';
                    if (coincide) visibles.push(tr);
                });

                paginateTable(visibles);
            }

            /* ============================================================
               Botones de navegación
            ============================================================ */
            function nextPage() {
                ++currentPage;
                filterTable();
            }

            function prevPage() {
                if (currentPage > 1) --currentPage;
                filterTable();
            }

            /* --  Exponer funciones al ámbito global  -- */
            window.nextPage    = nextPage;
            window.prevPage    = prevPage;
            window.filterTable = filterTable;

            /* ============================================================
            Primera carga
            ============================================================ */
            filterTable();
            $('#addForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: '../modules/mod-add_conexiones.php',
                    method: 'POST',
                    data: formData,
                    success: function(resp) {
                        if (resp.trim() === 'success') {
                            var rowData = {};
                            $('#addForm').serializeArray().forEach(function(field) {
                                rowData[field.name] = field.value;
                            });

                            var newRow = `
                                <tr id="row-${rowData.ART_NO}">
                                    <td>${rowData.ART_NO}</td>
                                    <td class="editable" contenteditable="true">${rowData.POSICIONX}</td>
                                    <td class="editable" contenteditable="true">${rowData.ETIQUETA}</td>
                                    <td class="editable" contenteditable="true">${rowData.CONECTOR}</td>
                                    <td class="editable" contenteditable="true">${rowData.DESCRIP1}</td>
                                    <td class="editable" contenteditable="true">${rowData.DESCRIP2}</td>
                                    <td class="editable" contenteditable="true">${rowData.EXISTENCIA}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm edit" data-id="${rowData.ART_NO}">Editar</button>
                                        <button class="btn btn-danger btn-sm delete" data-id="${rowData.ART_NO}">Eliminar</button>
                                    </td>
                                </tr>
                            `;

                            $('#conexionTable tbody').append(newRow);
                            $('#addModal').modal('hide');
                            $('#addForm')[0].reset();

                            $('#message').html('<div class="alert alert-success">Conexión agregada exitosamente</div>')
                                .fadeIn().delay(3000).fadeOut();

                            filterTable(); // Actualizar paginación
                        } else {
                            $('#message').html('<div class="alert alert-danger">' + resp + '</div>')
                                .fadeIn().delay(5000).fadeOut();
                        }
                    },
                    error: function() {
                        $('#message').html('<div class="alert alert-danger">Error al registrar la conexión</div>')
                            .fadeIn().delay(5000).fadeOut();
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Modal para agregar nueva conexión -->
<div class="modal fade text-center" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">Agregar nueva conexión</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addForm">
          <div class="form-row">
            <div class="form-group col-md-3"><label>ART_NO</label><input type="text" class="form-control" name="ART_NO" required></div>
            <div class="form-group col-md-3"><label>POSICIONX</label><input type="text" class="form-control" name="POSICIONX" required></div>
            <div class="form-group col-md-3"><label>ETIQUETA</label><input type="text" class="form-control" name="ETIQUETA" required></div>
            <div class="form-group col-md-3"><label>CONECTOR</label><input type="text" class="form-control" name="CONECTOR" required></div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-4"><label>DESCRIP1</label><input type="text" class="form-control" name="DESCRIP1" required></div>
            <div class="form-group col-md-4"><label>DESCRIP2</label><input type="text" class="form-control" name="DESCRIP2" required></div>
            <div class="form-group col-md-4"><label>EXISTENCIA</label><input type="number" class="form-control" name="EXISTENCIA" required></div>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-primary">Agregar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
