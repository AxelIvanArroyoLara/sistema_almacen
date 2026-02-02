$(document).on('click', '.save', function() {
    var row = $(this).closest('tr');
    var id = $(this).data('id');
    var aparato = row.find('td:eq(0)').text();
    var marca = row.find('td:eq(1)').text();
    var modelo = row.find('td:eq(2)').text();
    var encargado = row.find('td:eq(4)').text();
    var posicion = row.find('td:eq(5)').text();
    var status = row.find('td:eq(6)').text();

    // Verificar los datos que se están enviando
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
        url: '../php/modules/mod-edit_equipment.php',  // Asegúrate de que esta ruta sea correcta
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
            console.log(response); // Mostrar la respuesta del servidor en la consola
            if (response.trim() === 'Success') {
                $('#message').html('<div class="alert alert-success">Editado correctamente</div>');
                row.find('.editable').attr('contenteditable', 'false'); // Deshabilitar edición
                row.find('.save').removeClass('save btn-success').addClass('edit btn-primary').text('Editar'); // Cambiar a botón "Editar"
            } else {
                $('#message').html('<div class="alert alert-danger">Error al editar: ' + response + '</div>');
            }
        },
        error: function() {
            $('#message').html('<div class="alert alert-danger">Error al editar</div>');
        }
    });
    //181763
});
