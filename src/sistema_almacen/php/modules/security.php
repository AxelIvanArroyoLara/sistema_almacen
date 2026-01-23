<?php

/**
 * Security utilities for the application
 */

// Ensure sessions are started securely
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => false, // Set to true in production with HTTPS
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true
    ]);
}

/**
 * Generate CSRF token and store in session
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from POST request
 */
function validateCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if user is authenticated via session
 */
function isUserAuthenticated() {
    return !empty($_SESSION['user-id']);
}

/**
 * Get authenticated user ID
 */
function getAuthenticatedUserID() {
    return $_SESSION['user-id'] ?? null;
}

/**
 * Check if admin is authenticated via session
 */
function isAdminAuthenticated() {
    return !empty($_SESSION['admin-id']);
}

/**
 * Get authenticated admin ID
 */
function getAuthenticatedAdminID() {
    return $_SESSION['admin-id'] ?? null;
}

/**
 * Require authentication for a page/endpoint
 */
function requireUserAuthentication() {
    if (!isUserAuthenticated()) {
        http_response_code(403);
        die(json_encode(['error' => 'Acceso denegado']));
    }
}

/**
 * Require admin authentication
 */
function requireAdminAuthentication() {
    if (!isAdminAuthenticated()) {
        http_response_code(403);
        die(json_encode(['error' => 'Acceso denegado. Se requieren permisos de administrador']));
    }
}

/**
 * Safely compare passwords using password_verify
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Log security events
 */
function logSecurityEvent($event, $details = '') {
    $log_file = __DIR__ . '/../../security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $message = "[$timestamp] IP: $ip | Event: $event | Details: $details\n";
    error_log($message, 3, $log_file);
}

/**
 * Sanitize input for display
 */
function sanitizeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate numeric input
 */
function validateNumericInput($value) {
    return is_numeric($value) && intval($value) == $value;
}

?>
