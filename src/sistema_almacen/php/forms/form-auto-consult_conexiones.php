<?php
/* ---------------------------------------------------------
 *  Vista: lista de conexiones
 *  – Préstamo  → mod-request_conexion.php
 *  – Devolución→ mod-return_conexion.php
 * ---------------------------------------------------------*/
require_once __DIR__ . '/../modules/session_helper.php';
include_once '../modules/conn.php';


$user_id  = $_SESSION['user-id']  ?? null;
$admin_id = $_SESSION['admin-id'] ?? null;

/* --- Datos ------------------------------------------------ */
function getConexiones($connection) {
    $stmt = $connection->prepare("SELECT * FROM conexion");
    return $stmt->execute() ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}
$conexiones = getConexiones($connection);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Conexiones</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
<!-- ······································· NAV ································ -->
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
    <a href="../forms/form-auto-select_type.php" class="button-return" aria-label="Volver">
        <img src="../../resources/images/icon-return.png" alt="">
    </a>
    <h3 class="mb-1">Consulta de equipos del laboratorio</h3>
    <p class="mb-0">Por favor, modifique la información cuidadosamente:</p>
</header>

<!-- ·································· TABLA ·································· -->
<div class="container mt-4">
    <div class="search-container">
        <input type="text" id="searchInput" class="form-control"
               placeholder="Buscar conexión por cualquier campo..." onkeyup="filterTable()">
    </div><br>

    <div id="message" style="display:none" class="alert"></div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="conexionTable">
            <thead class="thead-dark">
            <tr>
                <th>ART_NO</th><th>POSICIONX</th><th>ETIQUETA</th><th>CONECTOR</th>
                <th>DESCRIP1</th><th>DESCRIP2</th><th>EXISTENCIA</th><th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($conexiones): foreach ($conexiones as $c): ?>
                <tr id="row-<?= htmlspecialchars((string)$c['ART_NO']) ?>">
                    <td><?= htmlspecialchars((string)$c['ART_NO']) ?></td>
                    <td class="editable"><?= htmlspecialchars((string)$c['POSICIONX']) ?></td>
                    <td class="editable"><?= htmlspecialchars((string)$c['ETIQUETA']) ?></td>
                    <td class="editable"><?= htmlspecialchars((string)$c['CONECTOR']) ?></td>
                    <td class="editable"><?= htmlspecialchars((string)$c['DESCRIP1']) ?></td>
                    <td class="editable"><?= htmlspecialchars((string)$c['DESCRIP2']) ?></td>
                    <td class="editable"><?= htmlspecialchars((string)$c['EXISTENCIA']) ?></td>
                    <td>
                        <button class="btn btn-success btn-sm solicitar"
                                data-id="<?= htmlspecialchars((string)$c['ART_NO']) ?>"
                                data-existencia="<?= (int)$c['EXISTENCIA'] ?>">
                            Solicitar
                        </button>
                        <button class="btn btn-warning btn-sm devolver"
                                data-id="<?= htmlspecialchars((string)$c['ART_NO']) ?>">
                            Devolver
                        </button>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="8" class="text-center">No hay conexiones disponibles.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ·································· PAGINACIÓN ···························· -->
<div class="pagination-buttons">
    <button id="prevBtn" class="btn btn-primary" onclick="prevPage()">Anterior</button>
    <span id="pageIndicator" class="page-indicator"></span>
    <button id="nextBtn" class="btn btn-primary" onclick="nextPage()">Siguiente</button>
</div>

<!-- ································· SCRIPTS ································ -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(function () {
    /* ----------  variables globales  ---------- */
    const user_id  = <?= json_encode($user_id) ?>;
    const admin_id = <?= json_encode($admin_id) ?>;
    let currentPage = 1, rowsPerPage = 10;

    function validarCantidad(modal, input1, input2, boton) {
      const val1 = parseInt($(input1).val(), 10);
      const val2 = parseInt($(input2).val(), 10);
      const valido = val1 > 0 && val1 === val2;
      $(boton).prop('disabled', !valido);
    }

    // Validación dinámica para préstamo
    $('#cantidad_p, #cantidad_p1').on('input', function () {
        validarCantidad('#modalPrestamo', '#cantidad_p', '#cantidad_p1', '#btnConfirmarPrestamo');
    });

    // Validación dinámica para devolución
    $('#cantidad_d, #cantidad_d1').on('input', function () {
        validarCantidad('#modalDevolucion', '#cantidad_d', '#cantidad_d1', '#btnConfirmarDevolucion');
    });

    // Inicializar los botones deshabilitados al abrir los modales
    $('#modalPrestamo').on('shown.bs.modal', function () {
        $('#btnConfirmarPrestamo').prop('disabled', true);
    });
    $('#modalDevolucion').on('shown.bs.modal', function () {
        $('#btnConfirmarDevolucion').prop('disabled', true);
    });

    /* ----------  abrir modal de PRÉSTAMO  ---------- */
    $(document).on('click', '.solicitar', function () {
        const art   = $(this).data('id');
        const exist = parseInt($(this).data('existencia'), 10);
        if (exist <= 0) { alert("No hay existencias disponibles."); return; }

        $('#art_no_p').val(art);
        $('#cantidad_p').attr('max', exist).val(1);
        $('#user_id_p').val(user_id);
        $('#admin_id_p').val(admin_id);
        $('#modalPrestamo').modal('show');
    });

    /* ----------  abrir modal de DEVOLUCIÓN  ---------- */
    $(document).on('click', '.devolver', function () {
        const art = $(this).data('id');
        $('#art_no_d').val(art);
        $('#cantidad_d').val('');
        $('#user_id_d').val(user_id);
        $('#admin_id_d').val(admin_id);
        $('#modalDevolucion').modal('show');
    });

    /* ----------  submit PRÉSTAMO  ---------- */
    $('#formPrestamo').on('submit', function (e) {
        e.preventDefault();
        // Excluir el campo de confirmación cantidad1 del envío
        var formData = $(this).serializeArray().filter(function(field) {
            return field.name !== 'cantidad1';
        });
        $.post('../modules/mod-request_conexion.php', $.param(formData), handleResp);
    });

    /* ----------  submit DEVOLUCIÓN  ---------- */
    $('#formDevolucion').on('submit', function (e) {
        e.preventDefault();
        // Excluir el campo de confirmación cantidad1 del envío
        var formData = $(this).serializeArray().filter(function(field) {
            return field.name !== 'cantidad1';
        });
        $.post('../modules/mod-return_conexion.php', $.param(formData), handleResp);
    });

    /* ----------  respuesta común  ---------- */
    function handleResp(resp) {
        if ($.trim(resp) === 'success') {
            $('#message').removeClass().addClass('alert alert-success')
                         .text('Operación realizada correctamente')
                         .fadeIn().delay(2500).fadeOut();
            $('.modal').modal('hide');
            setTimeout(()=>location.reload(), 2600);
        } else {
            $('#message').removeClass().addClass('alert alert-danger')
                         .text(resp).fadeIn().delay(4000).fadeOut();
        }
    }

    /* ----------  paginación y filtro  ---------- */
    function paginate(rows){
        const total = Math.max(1, Math.ceil(rows.length/rowsPerPage));
        currentPage = Math.min(currentPage, total);
        const start = (currentPage-1)*rowsPerPage, end = start+rowsPerPage;

        rows.each(function(i){ this.style.display = (i>=start && i<end)?'' : 'none'; });
        $('#pageIndicator').text(`Página ${currentPage} de ${total}`);
        $('#prevBtn').prop('disabled', currentPage===1);
        $('#nextBtn').prop('disabled', currentPage===total);
    }
    window.nextPage = () => { currentPage++; filterTable(); };
    window.prevPage = () => { if(currentPage>1) currentPage--; filterTable(); };

    window.filterTable = function () {
        const f = $('#searchInput').val().toLowerCase();
        const rows = $('#conexionTable tbody tr').filter(function () {
            const match = $(this).text().toLowerCase().includes(f);
            $(this).toggle(match);
            return match;
        });
        paginate(rows);
    };
    filterTable();   // primera carga
});
</script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- ······························· MODAL PRÉSTAMO ··························· -->
<div class="modal fade text-center" id="modalPrestamo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formPrestamo">
      <input type="hidden" name="user_id"  id="user_id_p">
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
            <input type="number" min="1" class="form-control"
                  name="cantidad" id="cantidad_p" required>
            <label for="cantidad_p1">Confirme cantidad:</label>
            <input type="number" min="1" class="form-control"
                  name="cantidad1" id="cantidad_p1" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btnConfirmarPrestamo">Confirmar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>

      <!-- Campo de control -->
      <input type="hidden" name="modo" value="prestamo">
    </form>
  </div>
</div>

<!-- ─────────────────── MODAL DEVOLUCIÓN ─────────────────── -->
<div class="modal fade text-center" id="modalDevolucion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formDevolucion">
      <input type="hidden" name="user_id"  id="user_id_d">
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
            <input type="number" min="1" class="form-control"
                  name="cantidad" id="cantidad_d" required>
                  <label for="cantidad_d1">Confirme cantidad:</label>
            <input type="number" min="1" class="form-control"
                  name="cantidad1" id="cantidad_d1" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="btnConfirmarDevolucion">Confirmar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </div>

      <!-- Campo de control -->
      <input type="hidden" name="modo" value="devolver">
    </form>
  </div>
</div>

</body>
</html>
