<?php
include 'conexion.php';
include 'seguridad.php';

// 1. Obtener el ID del trabajador desde la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conexion->prepare("SELECT * FROM trabajadores WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre = htmlspecialchars($row['nombre_completo']);
        $cedula = htmlspecialchars($row['cedula']);
        $codigo = htmlspecialchars($row['codigo_trabajador']);
        $descripcion = htmlspecialchars($row['descripcion_oficio']);
        $tipo = htmlspecialchars($row['tipo_documento']);
        $valor_inicial = htmlspecialchars($row['valor_inicial']);
        $valor_final = htmlspecialchars($row['valor_final']);
        $foto_actual = $row['foto_perfil'];
        $doc_actual = $row['archivo_adjunto'];
    } else {
        header("Location: consulta.php?msg=No+encontrado");
        exit();
    }
}

// 2. Lógica para procesar la actualización
if (isset($_POST['actualizar'])) {
    $id = $_GET['id'];
    $nombre_nuevo = trim($_POST['nombre']);
    $cedula_nueva = trim($_POST['cedula']);
    $desc_nueva = trim($_POST['descripcion']);
    $tipo_nuevo = trim($_POST['tipo']);
    $valor_inicial_nuevo = !empty($_POST['valor_inicial']) ? floatval($_POST['valor_inicial']) : null;
    $valor_final_nuevo = !empty($_POST['valor_final']) ? floatval($_POST['valor_final']) : null;

    $max_size = 5 * 1024 * 1024;
    $dir_fotos = "uploads/fotos/";
    $dir_docs  = "uploads/documentos/";
    
    if (!file_exists($dir_fotos)) { mkdir($dir_fotos, 0755, true); }
    if (!file_exists($dir_docs))  { mkdir($dir_docs,  0755, true); }

    function validar_mime_seguro($tmp_name, $tipos_permitidos) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $tmp_name);
        finfo_close($finfo);
        return in_array($mime_type, $tipos_permitidos);
    }

    $ruta_foto_bd = $foto_actual;
    $ruta_doc_bd = $doc_actual;

    // Procesar nueva foto
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK) {
        if ($_FILES['foto_perfil']['size'] <= $max_size && validar_mime_seguro($_FILES['foto_perfil']['tmp_name'], ['image/jpeg', 'image/png'])) {
            $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
            $nombre_foto = "foto_" . time() . "_" . uniqid() . "." . $ext;
            $ruta_nueva = $dir_fotos . $nombre_foto;
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_nueva)) {
                if (!empty($foto_actual) && file_exists($foto_actual)) { @unlink($foto_actual); }
                $ruta_foto_bd = $ruta_nueva;
            }
        }
    }

    // Procesar nuevo documento
    if (isset($_FILES['archivo_oficio']) && $_FILES['archivo_oficio']['error'] == UPLOAD_ERR_OK) {
        if ($_FILES['archivo_oficio']['size'] <= $max_size && validar_mime_seguro($_FILES['archivo_oficio']['tmp_name'], ['application/pdf', 'image/jpeg', 'image/png'])) {
            $ext = strtolower(pathinfo($_FILES['archivo_oficio']['name'], PATHINFO_EXTENSION));
            $nombre_doc = "doc_" . time() . "_" . uniqid() . "." . $ext;
            $ruta_nueva = $dir_docs . $nombre_doc;
            if (move_uploaded_file($_FILES['archivo_oficio']['tmp_name'], $ruta_nueva)) {
                if (!empty($doc_actual) && file_exists($doc_actual)) { @unlink($doc_actual); }
                $ruta_doc_bd = $ruta_nueva;
            }
        }
    }

    $update_query = "UPDATE trabajadores SET 
        nombre_completo = :nombre, 
        cedula = :cedula, 
        descripcion_oficio = :descripcion,
        tipo_documento = :tipo,
        valor_inicial = :valor_inicial,
        valor_final = :valor_final,
        foto_perfil = :foto,
        archivo_adjunto = :doc
        WHERE id = :id";
        
    $stmt_update = $conexion->prepare($update_query);
    $stmt_update->bindParam(':nombre', $nombre_nuevo);
    $stmt_update->bindParam(':cedula', $cedula_nueva);
    $stmt_update->bindParam(':descripcion', $desc_nueva);
    $stmt_update->bindParam(':tipo', $tipo_nuevo);
    $stmt_update->bindParam(':valor_inicial', $valor_inicial_nuevo);
    $stmt_update->bindParam(':valor_final', $valor_final_nuevo);
    $stmt_update->bindParam(':foto', $ruta_foto_bd);
    $stmt_update->bindParam(':doc', $ruta_doc_bd);
    $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        header("Location: consulta.php?status=success");
        exit();
    } else {
        $error_msg = "Error al actualizar.";
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-8">
        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        <?php if(isset($_GET['res']) && $_GET['res'] === 'archivo_eliminado'): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>Archivo eliminado correctamente.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <div class="card shadow border-0">
            <div class="card-header bg-warning py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Editar Registro: <?php echo $codigo; ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="editar.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $nombre; ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cédula de Identidad</label>
                            <input type="text" name="cedula" class="form-control" value="<?php echo $cedula; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Oficio</label>
                            <select name="tipo" class="form-select">
                                <option value="Embargo Judicial" <?php if($tipo == 'Embargo Judicial') echo 'selected'; ?>>Embargo Judicial</option>
                                <option value="Pensión Alimenticia" <?php if($tipo == 'Pensión Alimenticia') echo 'selected'; ?>>Pensión Alimenticia</option>
                                <option value="Otro" <?php if($tipo == 'Otro') echo 'selected'; ?>>Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Valor Inicial</label>
                            <div class="input-group">
                                <span class="input-group-text">VI</span>
                                <input type="number" step="0.01" name="valor_inicial" class="form-control" value="<?php echo $valor_inicial; ?>" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Valor Final</label>
                            <div class="input-group">
                                <span class="input-group-text">VF</span>
                                <input type="number" step="0.01" name="valor_final" class="form-control" value="<?php echo $valor_final; ?>" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción del Oficio</label>
                        <textarea name="descripcion" class="form-control" rows="4"><?php echo $descripcion; ?></textarea>
                    </div>

                    <div class="row mb-4 mt-4 bg-light p-3 rounded">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Actualizar Archivos Adjuntos</h6>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fotografía del Trabajador</label>
                            <input type="file" name="foto_perfil" class="form-control" accept=".jpg,.jpeg,.png">
                            <div class="form-text small">Dejar en blanco para mantener la fotografía actual.</div>
                            <?php if(!empty($foto_actual)): ?>
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <div class="text-success small"><i class="bi bi-check-circle me-1"></i>Tiene foto adjunta</div>
                                    <a href="eliminar_archivo.php?id=<?php echo $id; ?>&tipo=foto" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar la fotografía?');"><i class="bi bi-trash"></i> Eliminar</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Documento del Oficio (PDF o Imagen)</label>
                            <input type="file" name="archivo_oficio" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text small">Dejar en blanco para mantener el documento actual.</div>
                            <?php if(!empty($doc_actual)): ?>
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <div class="text-success small"><i class="bi bi-check-circle me-1"></i>Tiene documento adjunto</div>
                                    <a href="eliminar_archivo.php?id=<?php echo $id; ?>&tipo=documento" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar el documento?');"><i class="bi bi-trash"></i> Eliminar</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="consulta.php" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" name="actualizar" class="btn btn-warning fw-bold">Actualizar Información</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>