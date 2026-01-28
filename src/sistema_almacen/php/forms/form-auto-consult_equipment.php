<?php
require_once __DIR__ . '/../modules/session_helper.php';
// Incluir el módulo de conexión y consultas de equipos
include_once '../modules/conn.php';
include_once '../modules/bkend-consult_equipos.php';



$user_id = $_SESSION['user-id'] ?? null;
$admin_id = $_SESSION['admin-id'] ?? null;

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
    <a href="../forms/form-auto-select_type.php" class="button-return" aria-label="Volver">
        <img src="../../resources/images/icon-return.png" alt="">
    </a>

    <h3 class="mb-1">Consulta de equipos del laboratorio</h3>
    <p class="mb-0">Por favor, modifique la información cuidadosamente:</p>
    </header>
    <div class="container mt-4">
        <!-- Contenedor para el botón de retorno y el campo de búsqueda -->
        <div class="search-container">
            <!-- Campo de búsqueda -->
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar equipo por cualquier campo..." onkeyup="filterTable()">
        </div>

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
                                    <button class="btn btn-primary btn-sm request"
                                        data-id="<?= htmlspecialchars($equipo['numero_ser'] ?? '') ?>"
                                        <?= (isset($equipo['status']) && strtolower($equipo['status']) === 'prestado') ? 'disabled' : '' ?>>
                                        Solicitar
                                    </button>
                                    <button class="btn btn-danger btn-sm return"
                                        data-id="<?= htmlspecialchars($equipo['numero_ser'] ?? '') ?>">
                                        Devolver
                                    </button>
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
                Solicitud
            ============================================================ */
            $(document).on('click', '.request', function() {
                const user_id = <?= json_encode($user_id) ?>;
                const admin_id = <?= json_encode($admin_id) ?>;
                const numero_ser = $(this).data('id');

                $.ajax({
                    url: '../modules/mod-request_equipment.php',
                    method: 'POST',
                    data: {
                        numero_ser: numero_ser,
                        user_id: user_id,
                        admin_id: admin_id
                    },
                    success: function(resp) {
                        if (resp.trim() === 'success') {
                            $('#message').text('Solicitud registrada correctamente al usuario ' + user_id + '.' ).fadeIn();
                        
                            // Actualizar status en la tabla
                            const fila = $('#row-' + numero_ser);
                            fila.find('td').eq(6).text('PRESTADO'); // Cambia el texto de la columna STATUS
                            fila.find('.request').prop('disabled', true); // Deshabilita el botón
                        
                            setTimeout(() => { $('#message').fadeOut(); }, 3000);
                        } else {
                            $('#message').html('<div class="alert alert-danger">' + resp + '</div>').fadeIn();
                        }
                    },
                    error: function() {
                        $('#message').html('<div class="alert alert-danger">Error al procesar la solicitud.</div>').fadeIn();
                    }
                });
            });


            /* ============================================================
                Devolución
            ============================================================ */
            $(document).on('click', '.return', function() {
                const numero_ser = $(this).data('id');
                const admin_id = <?= json_encode($admin_id) ?>;
                const user_id = <?= json_encode($user_id) ?>;

                $.ajax({
                    url: '../modules/mod-return_equipment.php',
                    method: 'POST',
                    data: {
                        numero_ser: numero_ser,
                        admin_id: admin_id,
                        user_id: user_id
                    },
                    success: function(resp) {
                        if (resp.trim() === 'success') {
                            $('#message').text('Equipo devuelto exitosamente.').fadeIn();
                        
                            const fila = $('#row-' + numero_ser);
                            fila.find('td').eq(6).text('DISPONIBLE'); // Actualiza el campo "Status"
                            fila.find('.request').prop('disabled', false); // Reactiva botón "Solicitar"
                        
                            setTimeout(() => { $('#message').fadeOut(); }, 3000);
                        } else {
                            $('#message').html('<div class="alert alert-danger">' + resp + '</div>').fadeIn();
                        }
                    },
                    error: function() {
                        $('#message').html('<div class="alert alert-danger">Error al devolver el equipo.</div>').fadeIn();
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
        });
    </script>
</body>
</html>
