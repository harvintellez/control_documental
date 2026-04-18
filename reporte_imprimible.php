<?php
include 'seguridad.php';
include 'conexion.php';

// 1. Capturar filtros (con valores por defecto: desde el 1ero del mes actual hasta hoy)
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date("Y-m-01");
$fecha_fin    = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date("Y-m-d");
$tipo_filtro  = isset($_GET['tipo_documento']) ? $_GET['tipo_documento'] : 'Todos';
$estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : 'activos';

// 2. Construir la consulta SQL dinámica
$sql = "SELECT * FROM trabajadores WHERE DATE(fecha_registro) BETWEEN :fecha_inicio AND :fecha_fin";

if ($tipo_filtro != 'Todos') {
    $sql .= " AND tipo_documento = :tipo_filtro";
}
if ($estado_filtro === 'activos') {
    $sql .= " AND inhabilitado = 0";
} elseif ($estado_filtro === 'inhabilitados') {
    $sql .= " AND inhabilitado = 1";
}
$sql .= " ORDER BY fecha_registro DESC";

$stmt = $conexion->prepare($sql);
$stmt->bindParam(':fecha_inicio', $fecha_inicio);
$stmt->bindParam(':fecha_fin', $fecha_fin);
if ($tipo_filtro != 'Todos') {
    $stmt->bindParam(':tipo_filtro', $tipo_filtro);
}
$stmt->execute();

$fecha_actual = date("d/m/Y H:i");
?>
<?php
include 'includes/header.php';
?>

<style>
    /* Estilos adicionales para Reporte e Impresión */
    @media print {
        .no-print, nav.navbar { display: none !important; }
        body { background-color: white !important; padding: 0 !important; }
        .container { max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .header-reporte { border-bottom: 2px solid #000 !important; }
        .mt-4, .mb-5 { margin-top: 0 !important; margin-bottom: 0 !important; }
    }

    .header-reporte { border-bottom: 3px solid #0d6efd; padding-bottom: 15px; margin-bottom: 20px; }
    .table thead { background-color: #212529 !important; color: white !important; }
    .firma-espacio { border-top: 1px solid #000; width: 200px; margin: 50px auto 10px auto; }
</style>

<div class="mt-4 mb-5">
    
    <div class="card shadow-sm mb-4 no-print border-0">
        <div class="card-header bg-dark text-white fw-bold">
            <i class="bi bi-funnel me-2"></i> Filtros de Reporte
        </div>
        <div class="card-body bg-white">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Desde:</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($fecha_inicio); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Hasta:</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($fecha_fin); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tipo de Oficio:</label>
                    <select name="tipo_documento" class="form-select">
                        <option value="Todos" <?php echo ($tipo_filtro == 'Todos') ? 'selected' : ''; ?>>-- Todos los tipos --</option>
                        <option value="Embargo Judicial" <?php echo ($tipo_filtro == 'Embargo Judicial') ? 'selected' : ''; ?>>Embargo Judicial</option>
                        <option value="Pensión Alimenticia" <?php echo ($tipo_filtro == 'Pensión Alimenticia') ? 'selected' : ''; ?>>Pensión Alimenticia</option>
                        <option value="Otro" <?php echo ($tipo_filtro == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="todos"         <?php echo ($estado_filtro == 'todos')         ? 'selected' : ''; ?>>Todos</option>
                        <option value="activos"       <?php echo ($estado_filtro == 'activos')       ? 'selected' : ''; ?>>Solo Activos</option>
                        <option value="inhabilitados" <?php echo ($estado_filtro == 'inhabilitados') ? 'selected' : ''; ?>>Solo Inhabilitados</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1" title="Filtrar">
                        <i class="bi bi-search"></i>
                    </button>
                    <button type="button" onclick="window.print();" class="btn btn-success flex-grow-1" title="Imprimir PDF">
                        <i class="bi bi-printer"></i>
                    </button>
                    <a href="exportar_excel.php?fecha_inicio=<?php echo urlencode($fecha_inicio); ?>&fecha_fin=<?php echo urlencode($fecha_fin); ?>&tipo_documento=<?php echo urlencode($tipo_filtro); ?>&estado=<?php echo urlencode($estado_filtro); ?>" 
                       class="btn btn-warning flex-grow-1 text-white" title="Exportar Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>
                    <a href="panel.php" class="btn btn-secondary flex-grow-1" title="Volver al Inicio">
                        <i class="bi bi-house"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card p-5 shadow-sm border-0">
        <div class="header-reporte d-flex justify-content-between align-items-start">
            <div>
                <h1 class="fw-bold text-primary mb-0"><i class="bi bi-briefcase-fill me-2"></i>SCD NSEL-CLNSA</h1>
                <h4 class="text-secondary mb-1">Reporte de Control Documental</h4>
                <p class="mb-0 small">
                    <strong>Filtro aplicado:</strong> <?php echo htmlspecialchars(($tipo_filtro == 'Todos') ? 'Todos los registros' : $tipo_filtro); ?>
                </p>
                <p class="mb-0 small">
                    <strong>Periodo:</strong> <?php echo date("d/m/Y", strtotime($fecha_inicio)); ?> al <?php echo date("d/m/Y", strtotime($fecha_fin)); ?>
                </p>
            </div>
            <div class="text-end small">
                <p class="mb-0"><strong>Fecha de emisión:</strong> <?php echo $fecha_actual; ?></p>
                <p class="mb-0"><strong>Generado por:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
            </div>
        </div>

        <table class="table table-bordered table-striped align-middle mt-4">
            <thead class="table-dark text-center small">
                <tr>
                    <th>Fecha Registro</th>
                    <th>Código</th>
                    <th>Nombre del Trabajador</th>
                    <th>Cédula</th>
                    <th>Tipo de Oficio</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if($stmt->rowCount() > 0): ?>
                    <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr class="small" style="<?php echo $row['inhabilitado'] ? 'background-color:#f8d7da;color:#842029;' : ''; ?>">
                        <td class="text-center"><?php echo date("d/m/Y", strtotime($row['fecha_registro'])); ?></td>
                        <td class="text-center fw-bold"><?php echo htmlspecialchars($row['codigo_trabajador']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_documento']); ?></td>
                        <td class="text-center"><?php echo $row['inhabilitado'] ? 'Inhabilitado' : 'Activo'; ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            No se encontraron registros en el rango y tipo seleccionado.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-5 pt-5">
            <div class="row text-center">
                <div class="col-4">
                    <div class="firma-espacio"></div>
                    <p class="small fw-bold mb-0">Elaborado por</p>
                    <p class="small text-muted"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
                </div>
                <div class="col-4">
                    </div>
                <div class="col-4">
                    <div class="firma-espacio"></div>
                    <p class="small fw-bold mb-0">Revisado por</p>
                    <p class="small text-muted">Capital Humano / Legal</p>
                </div>
            </div>
        </div>
    <?php include 'includes/footer.php'; ?>