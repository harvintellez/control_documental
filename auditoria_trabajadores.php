<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: consulta.php?error=sin_permiso');
    exit;
}
include 'conexion.php';
include 'includes/auditoria.php';

$trabajador_id = isset($_GET['trabajador_id']) ? intval($_GET['trabajador_id']) : 0;
$fecha_inicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : '';
$fecha_fin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : '';
$accion = isset($_GET['accion']) ? trim($_GET['accion']) : '';
$usuario = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';
$where = '';
$params = [];

if ($trabajador_id > 0) {
    $where = 'WHERE trabajador_id = :trabajador_id';
    $params[':trabajador_id'] = $trabajador_id;
}

$acciones = $conexion->query("SELECT DISTINCT accion FROM trabajadores_auditoria ORDER BY accion")->fetchAll(PDO::FETCH_COLUMN);
$usuarios = $conexion->query("SELECT DISTINCT usuario FROM trabajadores_auditoria ORDER BY usuario")->fetchAll(PDO::FETCH_COLUMN);

if ($fecha_inicio !== '') {
    $where = $where ? "$where AND fecha_hora >= :fecha_inicio" : "WHERE fecha_hora >= :fecha_inicio";
    $params[':fecha_inicio'] = $fecha_inicio . ' 00:00:00';
}
if ($fecha_fin !== '') {
    $where = $where ? "$where AND fecha_hora <= :fecha_fin" : "WHERE fecha_hora <= :fecha_fin";
    $params[':fecha_fin'] = $fecha_fin . ' 23:59:59';
}
if ($accion !== '') {
    $where = $where ? "$where AND accion = :accion" : "WHERE accion = :accion";
    $params[':accion'] = $accion;
}
if ($usuario !== '') {
    $where = $where ? "$where AND usuario = :usuario" : "WHERE usuario = :usuario";
    $params[':usuario'] = $usuario;
}

$valid_per_page = [5, 10, 20, 50, 100];
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
if (!in_array($per_page, $valid_per_page, true)) {
    $per_page = 10;
}
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$countSql = "SELECT COUNT(*) FROM trabajadores_auditoria $where";
$countStmt = $conexion->prepare($countSql);
$countStmt->execute($params);
$total_records = (int)$countStmt->fetchColumn();
$total_pages = max(1, (int)ceil($total_records / $per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $per_page;
$max_visible_pages = 7;
$start_page = max(1, $page - intval($max_visible_pages / 2));
$end_page = min($total_pages, $start_page + $max_visible_pages - 1);
if ($end_page - $start_page + 1 < $max_visible_pages) {
    $start_page = max(1, $end_page - $max_visible_pages + 1);
}

$sql = "SELECT * FROM trabajadores_auditoria $where ORDER BY fecha_hora DESC, id DESC LIMIT :limit OFFSET :offset";
$stmt = $conexion->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_INT);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$auditoria = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query_params = $_GET;
unset($query_params['page']);
$base_filter_query = http_build_query($query_params);
$base_url = 'auditoria_trabajadores.php' . ($base_filter_query ? '?' . $base_filter_query : '');

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
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Filtrar por trabajador</label>
                        <input type="number" name="trabajador_id" class="form-control" value="<?= $trabajador_id ?>" placeholder="ID del trabajador">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Acción</label>
                        <select name="accion" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($acciones as $option): ?>
                                <option value="<?= htmlspecialchars($option) ?>" <?= $accion === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Usuario</label>
                        <select name="usuario" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($usuarios as $option): ?>
                                <option value="<?= htmlspecialchars($option) ?>" <?= $usuario === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Registros por página</label>
                        <select name="per_page" class="form-select">
                            <?php foreach ($valid_per_page as $option): ?>
                                <option value="<?= $option ?>" <?= $per_page === $option ? 'selected' : '' ?>><?= $option ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fecha_inicio) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fecha_fin) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filtrar</button>
                    </div>
                    <div class="col-md-3">
                        <a href="auditoria_trabajadores.php?<?= $base_filter_query ? $base_filter_query . '&' : '' ?>exportar_excel=1" class="btn btn-success w-100"><i class="bi bi-file-earmark-excel"></i> Exportar Excel</a>
                    </div>
                </form>

                <?php if (empty($auditoria)): ?>
                    <div class="alert alert-info">No hay registros de auditoría disponibles.</div>
                <?php else: ?>
                    <div class="table-responsive table-responsive-scroll">
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
                <?php if (!empty($auditoria)): ?>
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mt-3 mb-3 gap-3">
                        <div class="text-muted small">
                            Mostrando <strong><?= min($total_records, $offset + 1) ?></strong> a <strong><?= min($total_records, $offset + $per_page) ?></strong> de <strong><?= $total_records ?></strong> registros.
                        </div>
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Paginación auditoría">
                                <ul class="pagination mb-0">
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $base_url . ($base_filter_query ? '&' : '?') . 'page=' . ($page - 1) ?>" aria-label="Anterior">&laquo;</a>
                                    </li>
                                    <?php if ($start_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= $base_url . ($base_filter_query ? '&' : '?') . 'page=1' ?>">1</a>
                                        </li>
                                        <?php if ($start_page > 2): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php for ($p = $start_page; $p <= $end_page; $p++): ?>
                                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= $base_url . ($base_filter_query ? '&' : '?') . 'page=' . $p ?>"><?= $p ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php if ($end_page < $total_pages): ?>
                                        <?php if ($end_page < $total_pages - 1): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= $base_url . ($base_filter_query ? '&' : '?') . 'page=' . $total_pages ?>"><?= $total_pages ?></a>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= $base_url . ($base_filter_query ? '&' : '?') . 'page=' . ($page + 1) ?>" aria-label="Siguiente">&raquo;</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
