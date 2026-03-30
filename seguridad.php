<?php
session_start();

// Si no existe la sesión del usuario, lo mandamos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>