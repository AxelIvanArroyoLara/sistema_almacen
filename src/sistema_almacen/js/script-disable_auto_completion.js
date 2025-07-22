window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.setAttribute('autocomplete', 'off');
    });
    document.querySelectorAll('input').forEach(input => {
    input.setAttribute('autocomplete', 'off');
    });
});