<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "control_documental";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Configurar PDO para que lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

define('URL_BASE', 'http://172.16.31.62/control_documental/');
?>