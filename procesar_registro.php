<?php
include 'seguridad.php';
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibir y limpiar datos - NO aplicar htmlspecialchars aquí (se aplica al mostrar, no al guardar)
    $codigo      = trim($_POST['codigo']);
    $cedula      = trim($_POST['cedula']);
    $nombre      = trim($_POST['nombre']);
    $tipo        = trim($_POST['tipo_documento']);
    $descripcion = trim($_POST['descripcion']);
    
    // Validar campos obligatorios
    if (empty($codigo) || empty($cedula) || empty($nombre)) {
        header("Location: registro.php?error=campos_vacios");
        exit();
    }

    // Validar valores numéricos si están presentes
    $valor_inicial = !empty($_POST['valor_inicial']) ? floatval($_POST['valor_inicial']) : null;
    $valor_final   = !empty($_POST['valor_final'])   ? floatval($_POST['valor_final'])   : null;

    // Tamaño máximo permitido: 5 MB
    $max_size = 5 * 1024 * 1024;

    // 2. Definir carpetas de destino
    $dir_fotos = "uploads/fotos/";
    $dir_docs  = "uploads/documentos/";

    // Crear carpetas si no existen
    if (!file_exists($dir_fotos)) { mkdir($dir_fotos, 0755, true); }
    if (!file_exists($dir_docs))  { mkdir($dir_docs,  0755, true); }

    // Función para validar tipo MIME seguro
    function validar_mime_seguro($tmp_name, $tipos_permitidos) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $tmp_name);
        finfo_close($finfo);
        return in_array($mime_type, $tipos_permitidos);
    }

    // 3. Procesar Fotografía de Perfil (Validación estricta)
    $ruta_foto_bd = null;
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
        if ($_FILES['foto_perfil']['size'] > $max_size) {
            header("Location: registro.php?error=foto_grande");
            exit();
        }
        $tipos_foto = ['image/jpeg', 'image/png'];
        if (validar_mime_seguro($_FILES['foto_perfil']['tmp_name'], $tipos_foto)) {
            $extension   = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
            $nombre_foto = "foto_" . time() . "_" . uniqid() . "." . $extension;
            $ruta_foto_final = $dir_fotos . $nombre_foto;
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_foto_final)) {
                $ruta_foto_bd = $ruta_foto_final;
            }
        } else {
            header("Location: registro.php?error=tipo_foto_invalido");
            exit();
        }
    }

    // 4. Procesar Archivo del Oficio (Validación estricta PDF/Imagen)
    $ruta_doc_bd = null;
    if (isset($_FILES['archivo_oficio']) && $_FILES['archivo_oficio']['error'] == UPLOAD_ERR_OK) {
        if ($_FILES['archivo_oficio']['size'] > $max_size) {
            header("Location: registro.php?error=doc_grande");
            exit();
        }
        $tipos_doc = ['application/pdf', 'image/jpeg', 'image/png'];
        if (validar_mime_seguro($_FILES['archivo_oficio']['tmp_name'], $tipos_doc)) {
            $extension  = strtolower(pathinfo($_FILES['archivo_oficio']['name'], PATHINFO_EXTENSION));
            $nombre_doc = "doc_" . time() . "_" . uniqid() . "." . $extension;
            $ruta_doc_final = $dir_docs . $nombre_doc;
            if (move_uploaded_file($_FILES['archivo_oficio']['tmp_name'], $ruta_doc_final)) {
                $ruta_doc_bd = $ruta_doc_final;
            }
        } else {
            header("Location: registro.php?error=tipo_doc_invalido");
            exit();
        }
    }

    // 5. Insertar en la Base de Datos con PDO (Sentencia Preparada)
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
            header("Location: consulta.php?res=ok");
            exit();
        }
    } catch (PDOException $e) {
        // Registrar el error sin exponerlo al usuario
        error_log("Error BD procesar_registro: " . $e->getMessage());
        header("Location: registro.php?error=db");
        exit();
    }
} else {
    header("Location: registro.php");
    exit();
}
?>