<?php
include 'seguridad.php';
include 'conexion.php';

if (!isset($_GET['id']) || !isset($_GET['tipo'])) {
    header('Location: editar.php?id=' . $_GET['id'] . '&error=param');
    exit;
}

$id = intval($_GET['id']);
$tipo = $_GET['tipo'];

if ($tipo === 'foto') {
    $campo = 'foto_perfil';
} elseif ($tipo === 'documento') {
    $campo = 'archivo_adjunto';
} else {
    header('Location: editar.php?id=' . $id . '&error=tipo');
    exit;
}

$stmt = $conexion->prepare("SELECT $campo FROM trabajadores WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && !empty($row[$campo])) {
    $archivo = $row[$campo];
    if (file_exists($archivo)) {
        unlink($archivo);
    }
    $stmt_update = $conexion->prepare("UPDATE trabajadores SET $campo = NULL WHERE id = :id");
    $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_update->execute();
}

header('Location: editar.php?id=' . $id . '&res=archivo_eliminado');
exit;