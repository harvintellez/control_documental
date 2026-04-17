<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: consulta.php?error=sin_permiso');
    exit;
}
include 'conexion.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: consulta.php');
    exit;
}

try {
    $stmt = $conexion->prepare(
        "UPDATE trabajadores SET
            inhabilitado          = 0,
            fecha_inhabilitacion  = NULL,
            motivo_inhabilitacion = NULL,
            doc_inhabilitacion    = NULL,
            usuario_inhabilito    = NULL
         WHERE id = :id"
    );
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header('Location: consulta.php?res=reactivado');
    exit;
} catch (PDOException $e) {
    error_log('Error reactivar: ' . $e->getMessage());
    header('Location: consulta.php?error=db');
    exit;
}
?>
