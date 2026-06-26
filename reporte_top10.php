<?php
include 'seguridad.php';
include 'conexion.php';

$sql = "SELECT codigo_trabajador, nombre_completo, MIN(cedula) AS cedula, COUNT(*) AS total_embargos " .
       "FROM trabajadores " .
       "WHERE inhabilitado = 0 " .
       "GROUP BY codigo_trabajador, nombre_completo " .
       "HAVING total_embargos > 1 " .
       "ORDER BY total_embargos DESC, nombre_completo ASC " .
       "LIMIT 10";

$top_trabajadores = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$fecha_actual = date('d/m/Y H:i');
?>
<?php include 'includes/header.php'; ?>
<style>
    @media print {
        .no-print { display: none !important; }
        body { background-color: white !important; }
    }
    .header-reporte { border-bottom: 3px solid #0d6efd; padding-bottom: 15px; margin-bottom: 20px; }
</style>
<div class="container mt-5 mb-5">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="fw-bold text-primary mb-1">Top 10 embargos</h2>
                    <p class="mb-1">Trabajadores con más de un embargo activo.</p>
                    <p class="small text-muted mb-0">Generado el <?php echo $fecha_actual; ?></p>
                </div>
                <div class="d-flex gap-2 no-print">
                    <button type="button" class="btn btn-success" onclick="window.print();">
                        <i class="bi bi-printer"></i> Imprimir / Guardar PDF
                    </button>
                    <a href="exportar_top10.php" class="btn btn-primary">
                        <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                    </a>
                    <a href="panel.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Panel
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark text-white">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Nombre del Trabajador</th>
                            <th>Cédula</th>
                            <th class="text-center">Embargos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($top_trabajadores)): ?>
                            <?php foreach ($top_trabajadores as $index => $trabajador): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['codigo_trabajador']); ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['cedula']); ?></td>
                                    <td class="text-center fw-bold"><?php echo htmlspecialchars($trabajador['total_embargos']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay trabajadores con más de un embargo.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>