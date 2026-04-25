<?php
include 'seguridad.php';
include 'conexion.php';
include 'includes/header.php';

if (!isset($_SESSION['registro_duplicado'])) {
    header('Location: registro.php');
    exit();
}

$datos = $_SESSION['registro_duplicado'];
$archivos_temp = $_SESSION['archivos_temp'] ?? ['foto' => null, 'doc' => null];
$existente = $datos['existente'];

function getFileName($ruta) {
    return $ruta ? basename($ruta) : null;
}
?>
<div class="row justify-content-center mt-5">
    <div class="col-lg-8">
        <div class="alert alert-warning">
            <h4><i class="bi bi-exclamation-triangle-fill me-2"></i>Advertencia: Registro Duplicado</h4>
            <p class="mb-2">Ya existe un registro en el sistema con datos similares:</p>
            <table class="table table-bordered table-warning mb-3">
                <thead class="table-light">
                    <tr><th>Campo</th><th>Registro Existente</th><th>Nuevo Intento</th></tr>
                </thead>
                <tbody>
                    <tr><td><strong>Código</strong></td><td><?php echo htmlspecialchars($existente['codigo']); ?></td><td><?php echo htmlspecialchars($datos['codigo']); ?></td></tr>
                    <tr><td><strong>Cédula</strong></td><td><?php echo htmlspecialchars($existente['cedula']); ?></td><td><?php echo htmlspecialchars($datos['cedula']); ?></td></tr>
                    <tr><td><strong>Nombre</strong></td><td><?php echo htmlspecialchars($existente['nombre']); ?></td><td><?php echo htmlspecialchars($datos['nombre']); ?></td></tr>
                </tbody>
            </table>
            <p class="text-danger fw-bold">¿Desea guardar el nuevo registro de todas formas?</p>
        </div>

        <div class="card shadow">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Confirmar Registro</h5>
            </div>
            <div class="card-body p-4">
                <form action="procesar_registro_confirmado.php" method="POST">
                    <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($datos['codigo']); ?>">
                    <input type="hidden" name="cedula" value="<?php echo htmlspecialchars($datos['cedula']); ?>">
                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($datos['nombre']); ?>">
                    <input type="hidden" name="tipo_documento" value="<?php echo htmlspecialchars($datos['tipo']); ?>">
                    <input type="hidden" name="valor_inicial" value="<?php echo htmlspecialchars($datos['valor_inicial']); ?>">
                    <input type="hidden" name="valor_final" value="<?php echo htmlspecialchars($datos['valor_final']); ?>">
                    <input type="hidden" name="descripcion" value="<?php echo htmlspecialchars($datos['descripcion']); ?>">
                    <input type="hidden" name="temp_foto" value="<?php echo htmlspecialchars($archivos_temp['foto'] ?? ''); ?>">
                    <input type="hidden" name="temp_doc" value="<?php echo htmlspecialchars($archivos_temp['doc'] ?? ''); ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Código del Trabajador</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['codigo']); ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cédula de Identidad</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['cedula']); ?>" disabled>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['nombre']); ?>" disabled>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipo de Documento</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['tipo']); ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Valores</label>
                            <div class="input-group">
                                <span class="input-group-text">VI</span>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['valor_inicial']); ?>" disabled>
                                <span class="input-group-text">VF</span>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($datos['valor_final']); ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" rows="2" disabled><?php echo htmlspecialchars($datos['descripcion']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Archivos Adjuntos</label>
                        <?php if (!empty($archivos_temp['foto'])): ?>
                            <div class="text-success mb-1"><i class="bi bi-check-circle"></i> Foto: <?php echo htmlspecialchars(getFileName($archivos_temp['foto'])); ?></div>
                        <?php else: ?>
                            <div class="text-muted mb-1"><em>Sin foto adjunta</em></div>
                        <?php endif; ?>
                        <?php if (!empty($archivos_temp['doc'])): ?>
                            <div class="text-success"><i class="bi bi-check-circle"></i> Documento: <?php echo htmlspecialchars(getFileName($archivos_temp['doc'])); ?></div>
                        <?php else: ?>
                            <div class="text-muted"><em>Sin documento adjunto</em></div>
                        <?php endif; ?>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>Los archivos fueron cargados previamente y se guardarán con el registro.
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold">
                            <i class="bi bi-exclamation-octagon me-2"></i>Sí, Guardar de Todas Formas
                        </button>
                        <a href="cancelar_registro.php" class="btn btn-light">
                            <i class="bi bi-x-circle me-2"></i>Cancelar y Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>