<?php
include 'seguridad.php';
include 'conexion.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id']);

    // Opcional: Aquí podrías buscar las rutas de los archivos y borrarlos del servidor con unlink()
    
    $sql = "DELETE FROM trabajadores WHERE id = '$id'";

    if (mysqli_query($conexion, $sql)) {
        header("Location: consulta.php?res=del");
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
}
?>