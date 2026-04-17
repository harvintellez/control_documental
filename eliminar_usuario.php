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
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

header('Location: usuarios.php');
exit;
?>