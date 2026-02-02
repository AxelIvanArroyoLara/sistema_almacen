<?php

// Load environment variables from .env file
// En Docker: /var/www/html/ es el root
// Localmente: src/sistema_almacen/ es el root
$possible_paths = [
    __DIR__ . '/../../.env',           // Local: src/sistema_almacen/php/modules/../../.env
    __DIR__ . '/../../../.env',        // Root: .env
    '/var/www/html/.env'               // Docker mount point
];

foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $env_file = file_get_contents($path);
        $lines = explode("\n", $env_file);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '#') !== 0 && strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
        break;
    }
}

$server = $_ENV['MYSQL_HOST'] ?? 'db';
$user = $_ENV['MYSQL_USER'] ?? 'root';
$password = $_ENV['MYSQL_ROOT_PASSWORD'] ?? '';
$database = $_ENV['MYSQL_DATABASE'] ?? 'almacen';

if (empty($password)) {
    error_log("WARNING: Database password not set in .env file");
}

try {
    $connection = new PDO("mysql:host=$server;dbname=$database;charset=utf8", $user, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Don't expose database error details to users
    error_log("Database connection error: " . $e->getMessage());
    die("Error de conexión. Por favor contacte al administrador.");
}

?>
