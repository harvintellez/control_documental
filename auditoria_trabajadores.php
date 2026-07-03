<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: consulta.php?error=sin_permiso');
    exit;
}
include 'conexion.php';
include 'includes/auditoria.php';

$trabajador_id = isset($_GET['trabajador_id']) ? intval($_GET['trabajador_id']) : 0;
$where = '';
$params = [];

if ($trabajador_id > 0) {
    $where = 'WHERE trabajador_id = :trabajador_id';
    $params[':trabajador_id'] = $trabajador_id;
}

$sql = "SELECT * FROM trabajadores_auditoria $where ORDER BY fecha_hora DESC, id DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$auditoria = $stmt->fetchAll(PDO::FETCH_ASSOC);

$exportar_excel = isset($_GET['exportar_excel']) && $_GET['exportar_excel'] == '1';
if ($exportar_excel) {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=auditoria_trabajadores_' . date('Ymd_His') . '.xls');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Trabajador', 'Acción', 'Campo', 'Valor anterior', 'Valor nuevo', 'Usuario', 'Fecha/Hora', 'IP', 'Detalles'], "\t");

    foreach ($auditoria as $item) {
        fputcsv($output, [
            $item['id'],
            $item['trabajador_id'],
            $item['accion'],
            $item['campo'] ?? '-',
            $item['valor_anterior'] ?? '-',
            $item['valor_nuevo'] ?? '-',
            $item['usuario'] ?? '-',
            $item['fecha_hora'],
            $item['ip'] ?? '-',
            $item['detalles'] ?? '-'
        ], "\t");
    }

    fclose($output);
    exit;
}

include 'includes/header.php';
?>

<div class="row mt-4 mb-5">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold text-primary mb-1"><i class="bi bi-journal-text me-2"></i>Log de auditoría de trabajadores</h3>
                <p class="text-muted mb-0">Consulta los cambios registrados en los trabajadores, incluyendo usuario, fecha, valores anteriores y nuevos.</p>
            </div>
            <a href="consulta.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Filtrar por trabajador</label>
                        <input type="number" name="trabajador_id" class="form-control" value="<?= $trabajador_id ?>" placeholder="ID del trabajador">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filtrar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="auditoria_trabajadores.php?exportar_excel=1<?= $trabajador_id > 0 ? '&trabajador_id=' . $trabajador_id : '' ?>" class="btn btn-success w-100"><i class="bi bi-file-earmark-excel"></i> Exportar Excel</a>
                    </div>
                </form>

                <?php if (empty($auditoria)): ?>
                    <div class="alert alert-info">No hay registros de auditoría disponibles.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Trabajador</th>
                                    <th>Acción</th>
                                    <th>Campo</th>
                                    <th>Antes</th>
                                    <th>Después</th>
                                    <th>Usuario</th>
                                    <th>Fecha/Hora</th>
                                    <th>IP</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($auditoria as $item): ?>
                                    <tr>
                                        <td><?= (int)$item['id'] ?></td>
                                        <td><?= (int)$item['trabajador_id'] ?></td>
                                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($item['accion']) ?></span></td>
                                        <td><?= htmlspecialchars($item['campo'] ?? '-') ?></td>
                                        <td><?= nl2br(htmlspecialchars($item['valor_anterior'] ?? '-')) ?></td>
                                        <td><?= nl2br(htmlspecialchars($item['valor_nuevo'] ?? '-')) ?></td>
                                        <td><?= htmlspecialchars($item['usuario'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($item['fecha_hora']) ?></td>
                                        <td><?= htmlspecialchars($item['ip'] ?? '-') ?></td>
                                        <td><?= nl2br(htmlspecialchars($item['detalles'] ?? '-')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
