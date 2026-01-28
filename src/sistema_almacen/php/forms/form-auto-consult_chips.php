<?php
require_once __DIR__ . '/../modules/session_helper.php';
include_once '../modules/conn.php';
include_once '../modules/bkend-consult_chips.php';


$user_id = $_SESSION['user-id'] ?? null;
$admin_id = $_SESSION['admin-id'] ?? null;

$chips = getChips($connection);
if ($chips === false) $chips = [];

function safe($val) {
    return htmlspecialchars((string)($val ?? ''));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Chips</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <!-- Navegación principal -->
    <nav id="main-nav">
        <div class="logo-container">
            <img src="../../resources/images/logo-udlap.png" alt="" class="img-fluid" id="logo-udlap">
        </div>
        <div class="header-container" id="header-container">
            <header id="nav-departamento">Departamento de Electrónica</header>
            <header id="nav-titulo">-Consulta y búsqueda de chips-</header>
        </div>
    </nav>
    <header class="encabezado-wrapper my-5">
    <a href="../forms/form-auto-select_type.php" class="button-return" aria-label="Volver">
        <img src="../../resources/images/icon-return.png" alt="">
    </a>

    <h3 class="mb-1">Consulta de equipos del laboratorio</h3>
    <p class="mb-0">Por favor, modifique la información cuidadosamente:</p>
    </header>
    <div class="container mt-4">
        <!-- Búsqueda y botón de retorno -->
        <div class="search-container">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar chip por cualquier campo..." onkeyup="filterTable()">
        </div>

        <br>

        <!-- Mensaje -->
        <div id="message" style="display: none;" class="alert alert-success"></div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="chipsTable">
                <thead class="thead-dark">
                    <tr>
                        <?php
                        $columnas = array_keys($chips[0] ?? []);
                        foreach ($columnas as $col):
                        ?>
                            <th class="<?= $col !== 'ART_NO' ? 'editable' : '' ?>"><?= $col ?></th>
                        <?php endforeach; ?>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chips as $chip): ?>
                        <tr>
                            <?php foreach ($chip as $key => $value): ?>
                                <td class="<?= $key !== 'ART_NO' ? 'editable' : '' ?>"><?= safe($value) ?></td>
                            <?php endforeach; ?>
                            <td>
                                <?php if ((int)$chip['EXISTENCIA'] > 0): ?>
                                    <button class="btn btn-success btn-sm request" data-id="<?= safe($chip['ART_NO']) ?>">Solicitar</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Sin stock</button>
                                <?php endif; ?>
                                <button class="btn btn-warning btn-sm return" data-id="<?= safe($chip['ART_NO']) ?>">Devolver</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="pagination-buttons">
        <button id="prevBtn" class="btn btn-primary" onclick="prevPage()">Anterior</button>
        <span id="pageIndicator" class="page-indicator"></span>
        <button id="nextBtn" class="btn btn-primary" onclick="nextPage()">Siguiente</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    $(document).ready(function () {
        var currentPage = 1;
        var rowsPerPage = 10;

        const user_id = <?= json_encode($_SESSION['user-id'] ?? '') ?>;
        const admin_id = <?= json_encode($_SESSION['admin-id'] ?? '') ?>;

        // Abrir modal de solicitud
        $(document).on('click', '.request', function () {
            const art_no = $(this).data('id');

            $('#art_no_p').val(art_no);
            $('#cantidad_p').val('');
            $('#cantidad_p1').val('');
            $('#user_id_p').val(user_id);
            $('#admin_id_p').val(admin_id);
            $('#btnConfirmarPrestamo').prop('disabled', true);
            $('#modalPrestamo').modal('show');
        });

        // Abrir modal de devolución
        $(document).on('click', '.return', function () {
            const art_no = $(this).data('id');

            $('#art_no_d').val(art_no);
            $('#cantidad_d').val('');
            $('#cantidad_d1').val('');
            $('#user_id_d').val(user_id);
            $('#admin_id_d').val(admin_id);
            $('#btnConfirmarDevolucion').prop('disabled', true);
            $('#modalDevolucion').modal('show');
        });

        // Validación de cantidad para préstamo
        $('#cantidad_p, #cantidad_p1').on('input', function () {
            validarCantidad('#modalPrestamo', '#cantidad_p', '#cantidad_p1', '#btnConfirmarPrestamo');
        });

        // Validación de cantidad para devolución
        $('#cantidad_d, #cantidad_d1').on('input', function () {
            validarCantidad('#modalDevolucion', '#cantidad_d', '#cantidad_d1', '#btnConfirmarDevolucion');
        });

        function validarCantidad(modal, input1, input2, boton) {
            const val1 = parseInt($(input1).val(), 10);
            const val2 = parseInt($(input2).val(), 10);
            const valido = val1 > 0 && val1 === val2;
            $(boton).prop('disabled', !valido);
        }

        // Envío del formulario de préstamo
        $('#formPrestamo').on('submit', function (e) {
            e.preventDefault();
            // Excluir el campo de confirmación cantidad1 del envío
            var formData = $(this).serializeArray().filter(function(field) {
                return field.name !== 'cantidad1';
            });
            $.post('../modules/mod-request_chips.php', $.param(formData), handleResp);
        });

        // Envío del formulario de devolución
        $('#formDevolucion').on('submit', function (e) {
            e.preventDefault();
            // Excluir el campo de confirmación cantidad1 del envío
            var formData = $(this).serializeArray().filter(function(field) {
                return field.name !== 'cantidad1';
            });
            $.post('../modules/mod-return_chips.php', $.param(formData), handleResp);
        });

        // Respuesta común
        function handleResp(resp) {
            if ($.trim(resp) === 'success') {
                $('#message').removeClass().addClass('alert alert-success')
                    .text('Operación realizada correctamente')
                    .fadeIn().delay(2500).fadeOut();
                $('.modal').modal('hide');
                setTimeout(() => location.reload(), 2600);
            } else {
                $('#message').removeClass().addClass('alert alert-danger')
                    .text(resp).fadeIn().delay(4000).fadeOut();
            }
        }

        function paginateTable(rows) {
            var totalPages = Math.max(1, Math.ceil(rows.length / rowsPerPage));
            currentPage = Math.min(currentPage, totalPages);
            var start = (currentPage - 1) * rowsPerPage;
            var end = start + rowsPerPage;

            rows.forEach(function (row, idx) {
                row.style.display = (idx >= start && idx < end) ? '' : 'none';
            });

            document.getElementById('pageIndicator').textContent = 'Página ' + currentPage + ' de ' + totalPages;
            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = currentPage === totalPages;
        }

        function filterTable() {
            var filter = document.getElementById('searchInput').value.toLowerCase();
            var allRows = Array.from(document.querySelectorAll('#chipsTable tbody tr'));
            var visibles = [];

            allRows.forEach(function (tr) {
                var texto = tr.textContent.toLowerCase();
                var coincide = texto.indexOf(filter) !== -1;
                tr.style.display = coincide ? '' : 'none';
                if (coincide) visibles.push(tr);
            });

            paginateTable(visibles);
        }

        function nextPage() {
            currentPage++;
            filterTable();
        }

        function prevPage() {
            if (currentPage > 1) currentPage--;
            filterTable();
        }

        window.nextPage = nextPage;
        window.prevPage = prevPage;
        window.filterTable = filterTable;

        filterTable();

        // Formulario para agregar chip (sin cambios)
        $('#addChipForm').on('submit', function (e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: '../modules/mod-add_chips.php',
                method: 'POST',
                data: formData,
                success: function (resp) {
                    if (resp.trim() === 'success') {
                        // Lógica para agregar visualmente la nueva fila...
                        $('#addChipModal').modal('hide');
                        $('#addChipForm')[0].reset();
                        $('#message').html('<div class="alert alert-success">Chip agregado exitosamente</div>')
                            .fadeIn().delay(3000).fadeOut();
                        filterTable();
                    } else {
                        $('#message').html('<div class="alert alert-danger">' + resp + '</div>')
                            .fadeIn().delay(3000).fadeOut();
                    }
                },
                error: function () {
                    $('#message').html('<div class="alert alert-danger">Error al registrar el chip</div>')
                        .fadeIn().delay(3000).fadeOut();
                }
            });
        });
    });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="modal fade text-center" id="modalPrestamo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formPrestamo">
      <input type="hidden" name="user_id" id="user_id_p">
      <input type="hidden" name="admin_id" id="admin_id_p">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Solicitar préstamo</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="art_no" id="art_no_p">
          <div class="form-group">
            <label for="cantidad_p">Cantidad a solicitar:</label>
            <input type="number" min="1" class="form-control" name="cantidad" id="cantidad_p" required>
            <label for="cantidad_p1">Confirme cantidad:</label>
            <input type="number" min="1" class="form-control" name="cantidad1" id="cantidad_p1" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btnConfirmarPrestamo">Confirmar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>

      <input type="hidden" name="modo" value="prestamo">
    </form>
  </div>
</div>

<div class="modal fade text-center" id="modalDevolucion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formDevolucion">
      <input type="hidden" name="user_id" id="user_id_d">
      <input type="hidden" name="admin_id" id="admin_id_d">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Registrar devolución</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="art_no" id="art_no_d">
          <div class="form-group">
            <label for="cantidad_d">Cantidad a devolver:</label>
            <input type="number" min="1" class="form-control" name="cantidad" id="cantidad_d" required>
            <label for="cantidad_d1">Confirme cantidad:</label>
            <input type="number" min="1" class="form-control" name="cantidad1" id="cantidad_d1" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btnConfirmarDevolucion">Confirmar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>

      <input type="hidden" name="modo" value="devolver">
    </form>
  </div>
</div>


</body>
</html>
