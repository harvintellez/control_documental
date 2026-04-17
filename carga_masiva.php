<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}
include 'conexion.php';
include 'includes/header.php';
?>

<div class="row mt-5">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0"><i class="bi bi-file-earmark-arrow-up me-2"></i>Carga Masiva de Embargos</h4>
            </div>
            <div class="card-body p-4">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['res']) && $_GET['res'] === 'ok'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Carga masiva completada. <?php echo intval($_GET['c']); ?> registros insertados.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info">
                    <h5>Instrucciones:</h5>
                    <ol class="mb-0">
                        <li>Descargue la plantilla CSV.</li>
                        <li>Llene los datos de los embargos respetando las columnas. No modifique los encabezados.</li>
                        <li>Guarde el archivo como <strong>CSV (delimitado por comas)</strong>.</li>
                        <li>Suba el archivo aquí para previsualizar los datos.</li>
                    </ol>
                    <div class="mt-3">
                        <a href="plantilla_carga.csv" class="btn btn-outline-info bg-white" download>
                            <i class="bi bi-download me-1"></i> Descargar Plantilla CSV
                        </a>
                    </div>
                </div>

                <form action="previa_carga_masiva.php" method="POST" enctype="multipart/form-data" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Seleccionar archivo CSV <span class="text-danger">*</span></label>
                        <input type="file" name="archivo_csv" class="form-control" accept=".csv" required>
                        <div class="form-text">Tamaño máximo permitido: 5MB. Solo formato .csv</div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye me-1"></i> Previsualizar Carga
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
