
$(document).ready(function() {
    $('#user-id').on('change', function() {
        var userId = $(this).val();
        $.ajax({
            url: 'check_user.php',
            type: 'POST',
            data: { 'user-id': userId },
            dataType: 'json',
            success: function(response) {
                if (response.exists) {
                    $('#user-name').val(response.name);
                    $('#user-level').val(response.level);
                    $('#user-absences').val(response.absences);
                    $('#user-email').val(response.email);
                    $('#user-phone').val(response.phone);
                    $('#user-status').val(response.status);
                    $('#additional-info').addClass('show');
                } else {
                    $('#user-email, #user-phone, #user-password, #user-password-confirm').val('');
                    $('#user-name, #user-level, #user-absences').val('');
                    $('#additional-info').addClass('show');
                }
            },
            error: function() {
                alert('Error al verificar el ID de usuario.');
            }
        });
    });
});