<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: consulta.php?error=sin_permiso');
    exit;
}
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: consulta.php');
    exit;
}

$id     = intval($_POST['id'] ?? 0);
$fecha  = trim($_POST['fecha_inhabilitacion'] ?? '');
$motivo = trim($_POST['motivo_inhabilitacion'] ?? '');
$usuario = $_SESSION['usuario_nombre'];

if (!$id || empty($fecha) || empty($motivo)) {
    header('Location: consulta.php?error=campos_vacios_inh');
    exit;
}

// Validar formato de fecha
$fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha) {
    header('Location: consulta.php?error=fecha_invalida');
    exit;
}

// Procesar documento adjunto
$doc_inhabilitacion = null;
$dir_docs = 'uploads/inhabilitaciones/';
if (!file_exists($dir_docs)) { mkdir($dir_docs, 0755, true); }

if (isset($_FILES['doc_inhabilitacion']) && $_FILES['doc_inhabilitacion']['error'] == UPLOAD_ERR_OK) {
    $max_size = 5 * 1024 * 1024;
    if ($_FILES['doc_inhabilitacion']['size'] > $max_size) {
        header('Location: consulta.php?error=doc_grande_inh');
        exit;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES['doc_inhabilitacion']['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'])) {
        header('Location: consulta.php?error=tipo_doc_inh');
        exit;
    }
    $ext      = strtolower(pathinfo($_FILES['doc_inhabilitacion']['name'], PATHINFO_EXTENSION));
    $nombre   = 'inh_' . $id . '_' . time() . '_' . uniqid() . '.' . $ext;
    $ruta     = $dir_docs . $nombre;
    if (move_uploaded_file($_FILES['doc_inhabilitacion']['tmp_name'], $ruta)) {
        $doc_inhabilitacion = $ruta;
    }
}

try {
    $stmt = $conexion->prepare(
        "UPDATE trabajadores SET
            inhabilitado          = 1,
            fecha_inhabilitacion  = :fecha,
            motivo_inhabilitacion = :motivo,
            doc_inhabilitacion    = :doc,
            usuario_inhabilito    = :usuario
         WHERE id = :id"
    );
    $stmt->bindParam(':fecha',   $fecha);
    $stmt->bindParam(':motivo',  $motivo);
    $stmt->bindParam(':doc',     $doc_inhabilitacion);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':id',      $id, PDO::PARAM_INT);
    $stmt->execute();
    header('Location: consulta.php?res=inhabilitado');
    exit;
} catch (PDOException $e) {
    error_log('Error inhabilitar: ' . $e->getMessage());
    header('Location: consulta.php?error=db');
    exit;
}
?>
