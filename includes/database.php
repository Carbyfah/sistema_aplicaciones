<?php
// Si no se ha iniciado ya la sesión, iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Para debugging solo en desarrollo
if ($_ENV['DEBUG_MODE'] ?? 0) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

try {
    // Usar variables del archivo .env
    $host = $_ENV['DB_HOST'] ?? 'host.docker.internal';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV['DB_NAME'] ?? 'apps';
    $user = $_ENV['DB_USER'] ?? 'developer';
    $pass = $_ENV['DB_PASS'] ?? 'rootpassword';

    // Conexión a la base de datos (sin mensajes de diagnóstico en producción)
    $db = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5
        ]
    );
} catch (PDOException $e) {
    if ($_ENV['DEBUG_MODE'] ?? 0) {
        echo "Error de conexión: " . $e->getMessage();
    } else {
        echo json_encode([
            "mensaje" => "Error de conexión a la base de datos",
            "codigo" => 5
        ]);
    }
    exit;
}
