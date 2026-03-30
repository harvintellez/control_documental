<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}
include 'conexion.php';

if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$id = intval($_GET['id']);
if ($id > 0) {
    mysqli_query($conexion, "DELETE FROM usuarios WHERE id = $id");
}

header('Location: usuarios.php');
exit;
?>