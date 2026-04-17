<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: consulta.php?error=sin_permiso');
    exit;
}
include 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Buscar rutas de los archivos para borrarlos del servidor
    $stmt_archivos = $conexion->prepare("SELECT foto_perfil, archivo_adjunto FROM trabajadores WHERE id = :id");
    $stmt_archivos->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_archivos->execute();
    $archivos = $stmt_archivos->fetch(PDO::FETCH_ASSOC);

    if ($archivos) {
        if (!empty($archivos['foto_perfil']) && file_exists($archivos['foto_perfil'])) {
            unlink($archivos['foto_perfil']);
        }
        if (!empty($archivos['archivo_adjunto']) && file_exists($archivos['archivo_adjunto'])) {
            unlink($archivos['archivo_adjunto']);
        }
    }

    $stmt_delete = $conexion->prepare("DELETE FROM trabajadores WHERE id = :id");
    $stmt_delete->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt_delete->execute()) {
        header("Location: consulta.php?res=del");
        exit();
    } else {
        echo "Error al eliminar.";
    }
}
?>