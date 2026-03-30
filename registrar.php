<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    
    // Manejo de la Foto de Perfil
    $foto_nombre = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $ruta_foto = "uploads/fotos/" . $foto_nombre;
    move_uploaded_file($foto_tmp, $ruta_foto);

    // Manejo del PDF del Oficio
    $pdf_nombre = $_FILES['oficio']['name'];
    $pdf_tmp = $_FILES['oficio']['tmp_name'];
    $ruta_pdf = "uploads/documentos/" . $pdf_nombre;
    move_uploaded_file($pdf_tmp, $ruta_pdf);

    $sql = "INSERT INTO trabajadores (codigo_trabajador, nombre_completo, foto_perfil, archivo_adjunto) 
            VALUES ('$codigo', '$nombre', '$ruta_foto', '$ruta_pdf')";

    if (mysqli_query($conexion, $sql)) {
        echo "Registro guardado con éxito.";
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>