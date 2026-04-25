<?php
include 'seguridad.php';
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo       = trim($_POST['codigo']);
    $cedula       = trim($_POST['cedula']);
    $nombre       = trim($_POST['nombre']);
    $tipo         = trim($_POST['tipo_documento']);
    $descripcion  = trim($_POST['descripcion']);
    $valor_inicial = !empty($_POST['valor_inicial']) ? floatval($_POST['valor_inicial']) : null;
    $valor_final   = !empty($_POST['valor_final'])   ? floatval($_POST['valor_final'])   : null;
    $temp_foto    = !empty($_POST['temp_foto']) ? $_POST['temp_foto'] : null;
    $temp_doc     = !empty($_POST['temp_doc']) ? $_POST['temp_doc'] : null;

    $dir_fotos = "uploads/fotos/";
    $dir_docs  = "uploads/documentos/";
    if (!file_exists($dir_fotos)) { mkdir($dir_fotos, 0755, true); }
    if (!file_exists($dir_docs))  { mkdir($dir_docs,  0755, true); }

    $ruta_foto_bd = null;
    if ($temp_foto && file_exists($temp_foto)) {
        $ext = strtolower(pathinfo($temp_foto, PATHINFO_EXTENSION));
        $nombre_foto = "foto_" . time() . "_" . uniqid() . "." . $ext;
        $ruta_foto_final = $dir_fotos . $nombre_foto;
        if (rename($temp_foto, $ruta_foto_final)) {
            $ruta_foto_bd = $ruta_foto_final;
        }
    }

    $ruta_doc_bd = null;
    if ($temp_doc && file_exists($temp_doc)) {
        $ext = strtolower(pathinfo($temp_doc, PATHINFO_EXTENSION));
        $nombre_doc = "doc_" . time() . "_" . uniqid() . "." . $ext;
        $ruta_doc_final = $dir_docs . $nombre_doc;
        if (rename($temp_doc, $ruta_doc_final)) {
            $ruta_doc_bd = $ruta_doc_final;
        }
    }

    try {
        $sql = "INSERT INTO trabajadores (codigo_trabajador, nombre_completo, cedula, foto_perfil, descripcion_oficio, archivo_adjunto, tipo_documento, valor_inicial, valor_final) 
                VALUES (:codigo, :nombre, :cedula, :foto, :descripcion, :archivo, :tipo, :valor_inicial, :valor_final)";
        $stmt = $conexion->prepare($sql);
        
        $stmt->bindParam(':codigo',       $codigo);
        $stmt->bindParam(':nombre',       $nombre);
        $stmt->bindParam(':cedula',       $cedula);
        $stmt->bindParam(':foto',         $ruta_foto_bd);
        $stmt->bindParam(':descripcion',  $descripcion);
        $stmt->bindParam(':archivo',      $ruta_doc_bd);
        $stmt->bindParam(':tipo',         $tipo);
        $stmt->bindParam(':valor_inicial',$valor_inicial);
        $stmt->bindParam(':valor_final',  $valor_final);

        if ($stmt->execute()) {
            unset($_SESSION['registro_duplicado'], $_SESSION['archivos_temp']);
            header("Location: consulta.php?res=ok");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Error BD procesar_registro_confirmado: " . $e->getMessage());
        header("Location: registro.php?error=db");
        exit();
    }
} else {
    header("Location: registro.php");
    exit();
}
?>