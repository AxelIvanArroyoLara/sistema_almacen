<?php
// Incluir el módulo de conexión y consultas de equipos
include_once '../modules/conn.php';
include_once '../modules/bkend-consult_equipos.php';

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
                    -Consulta y búsqueda de equipos-
                </header>
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
        <!-- Contenedor para el botón de retorno y el campo de búsqueda -->
        <div class="search-container">
            
        <br>
        <br>

        <!-- Botón para abrir el modal de agregar -->
            
            <!-- Campo de búsqueda -->
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar equipo por cualquier campo..." onkeyup="filterTable()">
        </div>

        <!-- Botón para agregar un nuevo equipo -->
        <div class="text-right mb-3 text-center">
            <button class="btn btn-success" data-toggle="modal" data-target="#addModal">Agregar Nuevo Equipo</button>
        </div>
        <br>
        <!-- Mensajes de éxito tras editar o eliminar -->
        <div id="message" style="display: none;" class="alert alert-success"></div> <!-- Barra verde de notificación -->

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
                var aparato   = row.find('td:eq(0)').text();
                var marca     = row.find('td:eq(1)').text();
                var modelo    = row.find('td:eq(2)').text();
                var encargado = row.find('td:eq(4)').text();
                var posicion  = row.find('td:eq(5)').text();
                var status    = row.find('td:eq(6)').text();

                $.ajax({
                    url: '../modules/mod-edit_equipment.php',
                    method: 'POST',
                    data: { id, aparato, marca, modelo, encargado, posicion, status },
                    success: function(resp) {
                        resp = resp.trim();
                        if (resp === 'success') {
                            $('#message').text('Cambios guardados correctamente').removeClass('alert-danger').addClass('alert-success').show();
                            row.find('.editable').attr('contenteditable', 'false');
                            row.find('.save').removeClass('save btn-success')
                                            .addClass('edit btn-primary')
                                            .text('Editar');
                            setTimeout(function(){ $('#message').fadeOut(); }, 3000);
                        } else {
                            $('#message').html('Error al editar').removeClass('alert-success').addClass('alert-danger').show();
                        }
                    },
                    error: function() {
                        $('#message').html('Error en la solicitud').removeClass('alert-success').addClass('alert-danger').show();
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
                    url: '../modules/mod-delete_equipos.php',
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

            /* ============================================================
                Filtro y paginación
            ============================================================ */
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
                var allRows  = Array.from(document.querySelectorAll('#equipmentsTable tbody tr'));
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


            /* ============================================================
            Primera carga
            ============================================================ */
            $('#addForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: '../modules/mod-add_equipment.php',
                    method: 'POST',
                    data: formData,
                    success: function(resp) {
                        if (resp.trim() === 'success') {
                            var rowData = {};
                            $('#addForm').serializeArray().forEach(function(field) {
                                rowData[field.name] = field.value;
                            });

                            var newRow = `
                                <tr id="row-${rowData.numero_ser}">
                                    <td class="editable" contenteditable="true">${rowData.aparato}</td>
                                    <td class="editable" contenteditable="true">${rowData.marca}</td>
                                    <td class="editable" contenteditable="true">${rowData.modelo}</td>
                                    <td>${rowData.numero_ser}</td>
                                    <td class="editable" contenteditable="true">${rowData.encargado}</td>
                                    <td class="editable" contenteditable="true">${rowData.posicion}</td>
                                    <td class="editable" contenteditable="true">${rowData.status}</td>
                                    <td>${rowData.fecha_inv}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm edit" data-id="${rowData.numero_ser}">Editar</button>
                                        <button class="btn btn-danger btn-sm delete" data-id="${rowData.numero_ser}">Eliminar</button>
                                    </td>
                                </tr>
                            `;

                            $('#equipmentsTable tbody').append(newRow);
                            $('#addModal').modal('hide');
                            $('#addForm')[0].reset();

                            $('#message').html('<div class="alert alert-success">Equipo agregado exitosamente</div>')
                                        .fadeIn().delay(3000).fadeOut();

                            filterTable(); // Actualiza la paginación
                        } else {
                            // Si la respuesta no es 'success', muestra el error
                            $('#message').html('<div class="alert alert-danger">' + resp + '</div>')
                                        .fadeIn().delay(5000).fadeOut();
                        }
                    },
                error: function() {
                    $('#message').html('<div class="alert alert-danger">Error al registrar el equipo</div>')
                                .fadeIn().delay(5000).fadeOut();
                    }
                });
            });
        });
    </script>
    <!-- Requisitos para el modal de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <!-- Modal para agregar nuevo equipo -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">Agregar nuevo equipo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <form id="addForm" autocomplete="off">
          <div class="form-row">
            <div class="form-group col-md-3">
              <label>Aparato</label>
              <input type="text" class="form-control" name="aparato" required>
            </div>
            <div class="form-group col-md-3">
              <label>Marca</label>
              <input type="text" class="form-control" name="marca" required>
            </div>
            <div class="form-group col-md-3">
              <label>Modelo</label>
              <input type="text" class="form-control" name="modelo" required>
            </div>
            <div class="form-group col-md-3">
              <label>Número de Serie</label>
              <input type="text" class="form-control" name="numero_ser" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-3">
              <label>Encargado</label>
              <input type="text" class="form-control" name="encargado" required>
            </div>
            <div class="form-group col-md-3">
              <label>Posición</label>
              <input type="text" class="form-control" name="posicion" required>
            </div>
            <div class="form-group col-md-3">
              <label>Status</label>
              <input type="text" class="form-control" name="status" required>
            </div>
            <div class="form-group col-md-3">
              <label>Fecha de Inventario</label>
              <input type="date" class="form-control" name="fecha_inv" required>
            </div>
          </div>
          <br>
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
