<?php
include 'seguridad.php';
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibir datos de texto y limpiar para evitar inyecciones SQL básicas
    $codigo      = mysqli_real_escape_string($conexion, $_POST['codigo']);
    $cedula      = mysqli_real_escape_string($conexion, $_POST['cedula']);
    $nombre      = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $tipo        = mysqli_real_escape_string($conexion, $_POST['tipo_documento']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

    // 2. Definir carpetas de destino
    $dir_fotos = "uploads/fotos/";
    $dir_docs  = "uploads/documentos/";

    // Crear carpetas si no existen
    if (!file_exists($dir_fotos)) { mkdir($dir_fotos, 0777, true); }
    if (!file_exists($dir_docs)) { mkdir($dir_docs, 0777, true); }

    // 3. Procesar Fotografía de Perfil
    $ruta_foto_bd = ""; // Valor por defecto si no suben foto
    if (!empty($_FILES['foto_perfil']['name'])) {
        $nombre_foto = time() . "_" . $_FILES['foto_perfil']['name'];
        $ruta_foto_final = $dir_fotos . $nombre_foto;
        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_foto_final)) {
            $ruta_foto_bd = $ruta_foto_final;
        }
    }

    // 4. Procesar Archivo del Oficio (PDF o Imagen)
    $ruta_doc_bd = "";
    if (!empty($_FILES['archivo_oficio']['name'])) {
        $nombre_doc = time() . "_" . $_FILES['archivo_oficio']['name'];
        $ruta_doc_final = $dir_docs . $nombre_doc;
        if (move_uploaded_file($_FILES['archivo_oficio']['tmp_name'], $ruta_doc_final)) {
            $ruta_doc_bd = $ruta_doc_final;
        }
    }

    // 5. Insertar en la Base de Datos
    $sql = "INSERT INTO trabajadores (codigo_trabajador, nombre_completo, cedula, foto_perfil, descripcion_oficio, archivo_adjunto, tipo_documento) 
            VALUES ('$codigo', '$nombre', '$cedula', '$ruta_foto_bd', '$descripcion', '$ruta_doc_bd', '$tipo')";

    if (mysqli_query($conexion, $sql)) {
        // Redirigir a la consulta con un mensaje de éxito
        header("Location: consulta.php?res=ok");
    } else {
        echo "Error al guardar el registro: " . mysqli_error($conexion);
    }
} else {
    header("Location: registro.php");
}
?>