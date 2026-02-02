<?php
// Validar y limpiar ID de sesión antes de iniciar
if (isset($_COOKIE['PHPSESSID']) && strlen($_COOKIE['PHPSESSID']) > 40) {
    // ID de sesión demasiado largo, forzar uno nuevo
    unset($_COOKIE['PHPSESSID']);
    setcookie('PHPSESSID', '', time() - 3600, '/');
}
session_start();
// Regenerar ID si es necesario
if (strlen(session_id()) > 40) {
    session_regenerate_id(true);
}
include_once '../modules/conn.php';
include_once '../modules/bkend-auto-consult_prestamos.php';

// Validar existencia de sesión antes de continuar
if (!isset($_SESSION['user-id'])) {
    die("Acceso no autorizado. Por favor, inicie sesión.");
}

// Obtener deuda por usuario
$deuda = getDeuda($connection, $_SESSION['user-id']);
if ($deuda === false) {
    $deuda = [];
}

// Obtener préstamos filtrados por el usuario autenticado
$prestamos = getHistorial($connection, $_SESSION['user-id']);
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
    <a href="../forms/form-auto-select_type.php" class="button-return" aria-label="Volver">
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
        <!-- Tabla de deudas -->
        <h4 class="mt-4">Devoluciones pendientes</h4>
        <br>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Tipo</th>
                        <th>Artículo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cantidad</th>
                        <th>Encargado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($deuda)): ?>
                    <?php foreach ($deuda as $p): ?>
                        <tr>
                            <td class="tipo-cell"><?= htmlspecialchars($p['TIPO']) ?></td>
                            <td><?= htmlspecialchars($p['NOMPAR']) ?></td>
                            <td><?= htmlspecialchars($p['FECHA']) ?></td>
                            <td><?= htmlspecialchars($p['HORA']) ?></td>
                            <td><?= htmlspecialchars($p['CANT0MULTA']) ?></td>
                            <td><?= htmlspecialchars($p['ENCARGADO']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-success devolver-btn" 
                                        data-tipo="<?= htmlspecialchars($p['TIPO']) ?>"
                                        data-articulo="<?= htmlspecialchars($p['NOMPAR']) ?>"
                                        data-cantidad="<?= htmlspecialchars($p['CANT0MULTA']) ?>">
                                    Devolver
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No hay equipos en préstamo actualmente.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
        
        <!-- Tabla de historial -->
        <h4 class="mt-5">Historial de movimientos</h4>
        <br>
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

                // Colorear celdas de TIPO en devoluciones pendientes
                $('.table tbody tr').each(function () {
                    const tipoCell = $(this).find('td.tipo-cell');
                    const tipo = tipoCell.text().trim().toLowerCase();

                    switch (tipo) {
                        case 'equipo':
                            tipoCell.addClass('bg-primary text-white');
                            break;
                        case 'conexión':
                            tipoCell.addClass('bg-success text-white');
                            break;
                        case 'chip':
                            tipoCell.addClass('bg-warning');
                            break;
                        default:
                            tipoCell.addClass('bg-danger text-white');
                            break;
                    }
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

                // Manejo del botón de devolver (usar delegación de eventos)
                $(document).on('click', '.devolver-btn', function() {
                    const tipo = $(this).data('tipo').toLowerCase().trim();
                    const articulo = $(this).data('articulo');
                    const cantidadMax = $(this).data('cantidad');
                    
                    $('#tipo_devolver').val(tipo);
                    $('#articulo_devolver').val(articulo);
                    $('#cantidad_devolver').attr('max', cantidadMax);
                    $('#cantidad_devolver').val('');
                    $('#cantidad_confirmar_devolver').val('');
                    $('#articulo_nombre_modal').text(articulo);
                    $('#cantidad_max_modal').text(cantidadMax);
                    
                    $('#btnConfirmarDevolucion').prop('disabled', true);
                    $('#modalDevolucion').modal('show');
                });

                // Validación de cantidades en modal de devolución
                $('#cantidad_devolver, #cantidad_confirmar_devolver').on('input', function() {
                    const val1 = parseInt($('#cantidad_devolver').val(), 10);
                    const val2 = parseInt($('#cantidad_confirmar_devolver').val(), 10);
                    const max = parseInt($('#cantidad_devolver').attr('max'), 10);
                    const valido = val1 > 0 && val1 <= max && val1 === val2;
                    $('#btnConfirmarDevolucion').prop('disabled', !valido);
                });

                // Envío del formulario de devolución
                $('#formDevolucion').on('submit', function(e) {
                    e.preventDefault();
                    
                    const tipo = $('#tipo_devolver').val();
                    const articulo = $('#articulo_devolver').val();
                    const cantidad = $('#cantidad_devolver').val();
                    const userId = <?= json_encode($_SESSION['user-id'] ?? '') ?>;
                    const adminId = <?= json_encode($_SESSION['admin-id'] ?? '') ?>;
                    
                    let url = '';
                    let data = {
                        cantidad: cantidad,
                        user_id: userId,
                        admin_id: adminId
                    };

                    // Determinar el módulo según el tipo
                    if (tipo === 'chip') {
                        url = '../modules/mod-return_chips.php';
                        data.art_no = articulo;
                        data.modo = 'devolver';
                    } else if (tipo === 'equipo') {
                        url = '../modules/mod-return_equipment.php';
                        data.numero_ser = articulo;
                    } else if (tipo === 'conexión' || tipo === 'conexion') {
                        url = '../modules/mod-return_conexion.php';
                        data.art_no = articulo;
                        data.modo = 'devolver';
                    } else {
                        alert('Tipo de artículo desconocido');
                        return;
                    }

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: data,
                        success: function(resp) {
                            if ($.trim(resp) === 'success') {
                                $('#message').removeClass().addClass('alert alert-success')
                                    .text('Devolución registrada correctamente')
                                    .fadeIn().delay(2500).fadeOut();
                                $('#modalDevolucion').modal('hide');
                                setTimeout(() => location.reload(), 2600);
                            } else {
                                $('#message').removeClass().addClass('alert alert-danger')
                                    .text(resp).fadeIn().delay(4000).fadeOut();
                            }
                        },
                        error: function() {
                            $('#message').removeClass().addClass('alert alert-danger')
                                .text('Error al procesar la devolución').fadeIn().delay(4000).fadeOut();
                        }
                    });
                });

            });
        </script>

<!-- Modal de devolución -->
<div class="modal fade" id="modalDevolucion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formDevolucion">
            <input type="hidden" id="tipo_devolver">
            <input type="hidden" id="articulo_devolver">
            
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar devolución</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <p><strong>Artículo:</strong> <span id="articulo_nombre_modal"></span></p>
                    <p><strong>Cantidad máxima:</strong> <span id="cantidad_max_modal"></span></p>
                    
                    <div class="form-group">
                        <label for="cantidad_devolver">Cantidad a devolver:</label>
                        <input type="number" min="1" class="form-control" id="cantidad_devolver" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cantidad_confirmar_devolver">Confirme cantidad:</label>
                        <input type="number" min="1" class="form-control" id="cantidad_confirmar_devolver" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="btnConfirmarDevolucion">Confirmar devolución</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>