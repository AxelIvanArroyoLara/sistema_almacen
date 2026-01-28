<?php
/**
 * Utilidad para iniciar sesiones de forma segura
 * Previene errores de nombres de archivo demasiado largos
 */

// Validar y limpiar ID de sesión antes de iniciar
if (isset($_COOKIE['PHPSESSID']) && strlen($_COOKIE['PHPSESSID']) > 40) {
    // ID de sesión demasiado largo, forzar uno nuevo
    unset($_COOKIE['PHPSESSID']);
    setcookie('PHPSESSID', '', time() - 3600, '/');
}

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerar ID si es necesario
if (strlen(session_id()) > 40) {
    session_regenerate_id(true);
}
?>
