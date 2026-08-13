<?php
include 'seguridad.php';
include 'conexion.php';
include 'includes/auditoria.php';

// Solo administradores pueden acceder
if ($_SESSION['rol'] !== 'admin') {
    header("Location: panel.php?error=sin_permiso");
    exit();
}

$mensaje = "";
$error = "";

if (isset($_POST['guardar_banco'])) {
    $nombreBanco = trim($_POST['nombre_banco'] ?? '');
    if ($nombreBanco === '') {
        $error = 'El nombre del banco o institución es obligatorio.';
    } else {
        try {
            $stmt = $conexion->prepare("INSERT INTO configuracion_bancos (nombre, activo) VALUES (:nombre, 1) ON DUPLICATE KEY UPDATE activo = 1");
            $stmt->execute([':nombre' => $nombreBanco]);
            registrar_auditoria_configuracion($conexion, 'CONFIG_BANCO_AGREGAR', $_SESSION['usuario_nombre'] ?? $_SESSION['usuario'], 'configuracion_bancos', null, $nombreBanco, 'Se agregó el banco/institución al catálogo.' );
            $mensaje = 'Banco o institución agregada correctamente.';
        } catch (Exception $e) {
            $error = 'No se pudo guardar el banco: ' . $e->getMessage();
        }
    }
}

if (isset($_POST['eliminar_banco'])) {
    $idBanco = isset($_POST['id_banco']) ? (int) $_POST['id_banco'] : 0;
    if ($idBanco <= 0) {
        $error = 'No se pudo identificar el banco a eliminar.';
    } else {
        try {
            $stmt = $conexion->prepare("SELECT nombre FROM configuracion_bancos WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $idBanco]);
            $banco = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($banco) {
                $stmtDelete = $conexion->prepare("DELETE FROM configuracion_bancos WHERE id = :id");
                $stmtDelete->execute([':id' => $idBanco]);
                registrar_auditoria_configuracion($conexion, 'CONFIG_BANCO_ELIMINAR', $_SESSION['usuario_nombre'] ?? $_SESSION['usuario'], 'configuracion_bancos', $banco['nombre'], null, 'Se eliminó el banco/institución del catálogo.');
                $mensaje = 'Banco o institución eliminada correctamente.';
            }
        } catch (Exception $e) {
            $error = 'No se pudo eliminar el banco: ' . $e->getMessage();
        }
    }
}

if (isset($_POST['guardar_edicion_banco'])) {
    $idBanco = isset($_POST['id_banco_editar']) ? (int) $_POST['id_banco_editar'] : 0;
    $nombreBanco = trim($_POST['nombre_banco_editar'] ?? '');

    if ($idBanco <= 0 || $nombreBanco === '') {
        $error = 'La edición del banco requiere un nombre válido.';
    } else {
        try {
            $stmtActual = $conexion->prepare("SELECT nombre FROM configuracion_bancos WHERE id = :id LIMIT 1");
            $stmtActual->execute([':id' => $idBanco]);
            $bancoActual = $stmtActual->fetch(PDO::FETCH_ASSOC);

            if ($bancoActual) {
                $stmtUpdate = $conexion->prepare("UPDATE configuracion_bancos SET nombre = :nombre WHERE id = :id");
                $stmtUpdate->execute([
                    ':nombre' => $nombreBanco,
                    ':id' => $idBanco,
                ]);

                registrar_auditoria_configuracion(
                    $conexion,
                    'CONFIG_BANCO_EDITAR',
                    $_SESSION['usuario_nombre'] ?? $_SESSION['usuario'],
                    'configuracion_bancos',
                    $bancoActual['nombre'],
                    $nombreBanco,
                    'Se editó el nombre del banco/institución del catálogo.'
                );

                $mensaje = 'Banco o institución actualizada correctamente.';
            }
        } catch (Exception $e) {
            $error = 'No se pudo editar el banco: ' . $e->getMessage();
        }
    }
}

$bancos_config = $conexion->query("SELECT id, nombre FROM configuracion_bancos WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="row mt-5 mb-5">
    <div class="col-md-8 mx-auto">
        <div class="card shadow border-0">
            <div class="card-header bg-info text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-bank2 me-2"></i>Configuración de Bancos / Instituciones</h5>
            </div>
            <div class="card-body p-4">
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?php echo $mensaje; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <h6 class="fw-bold"><i class="bi bi-info-circle-fill me-2"></i>Información:</h6>
                    <p class="mb-0">Los bancos e instituciones configurados aquí se utilizan cuando el tipo de documento es "Embargo Judicial". Solo los administradores pueden agregar, editar o eliminar instituciones.</p>
                </div>

                <div class="card border-info shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i>Agregar Nueva Institución</h6>

                        <form method="POST" class="row g-2 align-items-end">
                            <div class="col-md-9">
                                <label class="form-label fw-bold">Nombre del banco o institución</label>
                                <input type="text" name="nombre_banco" class="form-control" placeholder="Ej: Lafise Bancentro, BAC, BANPRO" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="guardar_banco" class="btn btn-info w-100 text-white">
                                    <i class="bi bi-plus-circle me-2"></i>Agregar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (!empty($bancos_config)): ?>
                    <div class="card border-light shadow-sm mt-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="bi bi-list-ul me-2"></i>Instituciones Configuradas</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="fw-bold">Nombre del Banco / Institución</th>
                                            <th class="text-end fw-bold" style="width: 100px;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bancos_config as $banco): ?>
                                            <tr>
                                                <td class="pe-3">
                                                    <div class="fw-semibold"><i class="bi bi-bank me-2 text-info"></i><?= htmlspecialchars($banco['nombre']) ?></div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <button type="button" class="btn btn-link btn-sm p-0 text-primary" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarBanco<?= (int)$banco['id'] ?>">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="id_banco" value="<?= (int)$banco['id'] ?>">
                                                            <button type="submit" name="eliminar_banco" class="btn btn-link btn-sm p-0 text-danger" title="Eliminar" onclick="return confirm('¿Desea eliminar este banco o institución?');">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php else: ?>
                    <div class="alert alert-light border mt-4 text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-3 mb-0">No hay bancos o instituciones configurados aún.<br><small>Agregue una nueva institución utilizando el formulario anterior.</small></p>
                    </div>
                <?php endif; ?>

                <div class="mt-4 text-center">
                    <a href="panel.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales de Edición -->
<?php foreach ($bancos_config as $banco): ?>
    <div class="modal fade" id="modalEditarBanco<?= (int)$banco['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Institución</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_banco_editar" value="<?= (int)$banco['id'] ?>">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="nombre_banco_editar" value="<?= htmlspecialchars($banco['nombre']) ?>" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="guardar_edicion_banco" class="btn btn-primary btn-sm">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include 'includes/footer.php'; ?>
