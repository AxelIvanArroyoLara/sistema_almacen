<?php
include_once '../modules/conn.php';
include_once '../modules/bkend-consult_chips.php';

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
    <a href="../forms/form-select_type.php" class="button-return" aria-label="Volver">
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

        <div class="text-center my-3">
            <button class="btn btn-success" data-toggle="modal" data-target="#addChipModal">Agregar Nuevo Chip</button>
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
                                <button class="btn btn-primary btn-sm edit" data-id="<?= safe($chip['ART_NO']) ?>">Editar</button>
                                <button class="btn btn-danger btn-sm delete" data-id="<?= safe($chip['ART_NO']) ?>">Eliminar</button>
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
    $(document).ready(function() {
        var currentPage = 1;
        var rowsPerPage = 10;

        $(document).on('click', '.edit', function () {
            const $btn = $(this);
            const $row = $btn.closest('tr');

            // Hacer editables todas las celdas con la clase .editable
            $row.find('.editable').each(function () {
                $(this).attr('contenteditable', 'true');
            });
        
            // Cambiar botón a modo guardar
            $btn.removeClass('edit btn-primary')
                .addClass('save btn-success')
                .text('Guardar');
        });

        $(document).on('click', '.save', function() {
            var row = $(this).closest('tr');
            var id = $(this).data('id');

            var data = {
                art_no: id,
                posicionx: row.find('td:eq(1)').text().trim(),
                etiqueta: row.find('td:eq(2)').text().trim(),
                conector: row.find('td:eq(3)').text().trim(),
                descrip1: row.find('td:eq(4)').text().trim(),
                descrip2: row.find('td:eq(5)').text().trim(),
                minimo: row.find('td:eq(6)').text().trim(),
                existencia: row.find('td:eq(7)').text().trim(),
                pedidos: row.find('td:eq(8)').text().trim(),
                conector_2: row.find('td:eq(9)').text().trim(),
                pedido: row.find('td:eq(10)').text().trim(),
                precio: row.find('td:eq(11)').text().trim(),
                fecha_adq: row.find('td:eq(12)').text().trim(),
                proveedor: row.find('td:eq(13)').text().trim(),
                chkx: row.find('td:eq(14)').text().trim(),
                cont_1: row.find('td:eq(15)').text().trim(),
                cont_2: row.find('td:eq(16)').text().trim(),
                no_provee: row.find('td:eq(17)').text().trim(),
                stock: row.find('td:eq(18)').text().trim()
            };
        
            $.post('../modules/mod-edit_chips.php', data, function(resp) {
                if (resp.trim() === 'success') {
                    $('#message').text('Cambios guardados correctamente').fadeIn().delay(3000).fadeOut();
                    row.find('.editable').attr('contenteditable', 'false');
                    row.find('.save').removeClass('save btn-success').addClass('edit btn-primary').text('Editar');
                } else {
                    $('#message').html('<div class="alert alert-danger">Error: ' + resp + '</div>').fadeIn().delay(3000).fadeOut();
                }
            });
        });


        $(document).on('click', '.delete', function() {
            if (!confirm('¿Estás seguro de eliminar este chip?')) return;
            var row = $(this).closest('tr');
            var id = $(this).data('id');

            $.post('../modules/bkend-consult_chips.php', { delete: true, art_no: id }, function(resp) {
                if (resp.trim() === 'success') {
                    row.remove();
                    $('#message').text('Chip eliminado correctamente').fadeIn().delay(3000).fadeOut();
                    filterTable();
                } else {
                    $('#message').html('<div class="alert alert-danger">Error: ' + resp + '</div>').fadeIn().delay(3000).fadeOut();
                }
            });
        });

        function paginateTable(rows) {
            var totalPages = Math.max(1, Math.ceil(rows.length / rowsPerPage));
            currentPage = Math.min(currentPage, totalPages);
            var start = (currentPage - 1) * rowsPerPage;
            var end = start + rowsPerPage;

            rows.forEach(function(row, idx) {
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

            allRows.forEach(function(tr) {
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

        $('#addChipForm').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: '../modules/mod-add_chips.php',
                method: 'POST',
                data: formData,
                success: function(resp) {
                    if (resp.trim() === 'success') {
                        var rowData = {};
                        $('#addChipForm').serializeArray().forEach(function(field) {
                            rowData[field.name] = field.value;
                        });

                        var newRow = '<tr>';
                        newRow += `<td>${rowData.ART_NO}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.POSICIONX}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.ETIQUETA}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.CONECTOR}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.DESCRIP1}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.DESCRIP2}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.MINIMO}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.EXISTENCIA}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.PEDIDOS}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.CONECTOR_2}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.PEDIDO}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.PRECIO}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.FECHA_ADQ}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.PROVEEDOR}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.CHKX}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.CONT_1}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.CONT_2}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.NO_PROVEE}</td>`;
                        newRow += `<td class="editable" contenteditable="true">${rowData.STOCK}</td>`;
                        newRow += `<td>
                            <button class="btn btn-primary btn-sm edit" data-id="${rowData.ART_NO}">Editar</button>
                            <button class="btn btn-danger btn-sm delete" data-id="${rowData.ART_NO}">Eliminar</button>
                        </td>`;
                        newRow += '</tr>';

                        $('#chipsTable tbody').append(newRow);
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
                error: function() {
                    $('#message').html('<div class="alert alert-danger">Error al registrar el chip</div>')
                                .fadeIn().delay(3000).fadeOut();
                }
            });
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Modal para agregar nuevo chip -->
    <div class="modal fade text-center" id="addChipModal" tabindex="-1" role="dialog" aria-labelledby="addChipModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form id="addChipForm" autocomplete="off">
        <div class="modal-header">
          <h5 class="modal-title" id="addChipModalLabel">Agregar nuevo chip</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-3"><label>ART_NO</label><input type="text" name="ART_NO" class="form-control" required></div>
            <div class="form-group col-md-3"><label>POSICIONX</label><input type="text" name="POSICIONX" class="form-control" required></div>
            <div class="form-group col-md-3"><label>ETIQUETA</label><input type="text" name="ETIQUETA" class="form-control" required></div>
            <div class="form-group col-md-3"><label>CONECTOR</label><input type="text" name="CONECTOR" class="form-control" required></div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-3"><label>DESCRIP1</label><input type="text" name="DESCRIP1" class="form-control" required></div>
            <div class="form-group col-md-3"><label>DESCRIP2</label><input type="text" name="DESCRIP2" class="form-control" required></div>
            <div class="form-group col-md-2"><label>MINIMO</label><input type="number" step="any" name="MINIMO" class="form-control" required></div>
            <div class="form-group col-md-2"><label>EXISTENCIA</label><input type="number" step="any" name="EXISTENCIA" class="form-control" required></div>
            <div class="form-group col-md-2"><label>PEDIDOS</label><input type="number" step="any" name="PEDIDOS" class="form-control" required></div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-3"><label>CONECTOR_2</label><input type="text" name="CONECTOR_2" class="form-control" required></div>
            <div class="form-group col-md-3"><label>PEDIDO</label><input type="text" name="PEDIDO" class="form-control" required></div>
            <div class="form-group col-md-2"><label>PRECIO</label><input type="number" step="any" name="PRECIO" class="form-control" required></div>
            <div class="form-group col-md-2"><label>FECHA_ADQ</label><input type="date" name="FECHA_ADQ" class="form-control" placeholder="YYYY-MM-DD" required></div>
            <div class="form-group col-md-2"><label>PROVEEDOR</label><input type="text" name="PROVEEDOR" class="form-control" required></div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-3"><label>CHKX</label><input type="text" name="CHKX" class="form-control" required></div>
            <div class="form-group col-md-2"><label>CONT_1</label><input type="number" step="any" name="CONT_1" class="form-control" required></div>
            <div class="form-group col-md-2"><label>CONT_2</label><input type="number" step="any" name="CONT_2" class="form-control" required></div>
            <div class="form-group col-md-3"><label>NO_PROVEE</label><input type="text" name="NO_PROVEE" class="form-control" required></div>
            <div class="form-group col-md-2"><label>STOCK</label><input type="text" name="STOCK" class="form-control" required></div>
          </div>
        </div>

        <div class="modal-footer justify-content-center">
          <button type="submit" class="btn btn-primary">Agregar</button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
