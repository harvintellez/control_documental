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

    $update_query = "UPDATE trabajadores SET 
        nombre_completo = :nombre, 
        cedula = :cedula, 
        descripcion_oficio = :descripcion,
        tipo_documento = :tipo,
        valor_inicial = :valor_inicial,
        valor_final = :valor_final
        WHERE id = :id";
        
    $stmt_update = $conexion->prepare($update_query);
    $stmt_update->bindParam(':nombre', $nombre_nuevo);
    $stmt_update->bindParam(':cedula', $cedula_nueva);
    $stmt_update->bindParam(':descripcion', $desc_nueva);
    $stmt_update->bindParam(':tipo', $tipo_nuevo);
    $stmt_update->bindParam(':valor_inicial', $valor_inicial_nuevo);
    $stmt_update->bindParam(':valor_final', $valor_final_nuevo);
    $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        header("Location: consulta.php?status=success");
        exit();
    } else {
        echo "Error al actualizar.";
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-8">
        <div class="card shadow border-0">
            <div class="card-header bg-warning py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Editar Registro: <?php echo $codigo; ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="editar.php?id=<?php echo $id; ?>" method="POST">
                    
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

                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-2"></i>Nota: Para cambiar la fotografía o el PDF, favor de realizar un nuevo registro o contactar al administrador del sistema.
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