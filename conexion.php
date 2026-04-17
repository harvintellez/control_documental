<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "control_documental";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    // Configurar PDO para que lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Emulación de sentencias preparadas desactivada para mayor seguridad
    $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    error_log("Error de conexión BD: " . $e->getMessage());
    die("Error crítico: no se pudo conectar al servidor. Contacte al administrador.");
}

define('URL_BASE', 'http://172.16.31.62/control_documental/');
?>