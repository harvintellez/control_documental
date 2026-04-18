<?php
include 'seguridad.php';
include 'conexion.php';

$resultados = [];
$mensaje = '';

// Procesamiento de búsqueda y exportación a Excel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigos_raw = $_POST['codigos'];
    $codigos = array_filter(array_map('trim', explode(',', $codigos_raw)), function($c) { return $c !== ''; });
    if (count($codigos) > 0) {
        // Crear placeholders para PDO (?, ?, ?)
        $placeholders = implode(',', array_fill(0, count($codigos), '?'));
        
        $sql = "SELECT codigo_trabajador, nombre_completo, cedula, descripcion_oficio, inhabilitado FROM trabajadores WHERE codigo_trabajador IN ($placeholders)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(array_values($codigos));
        
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = $fila;
        }

        // Exportar a Excel
        if (isset($_POST['exportar']) && count($resultados) > 0) {
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=busqueda_trabajadores_" . date('Ymd_His') . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<table border="1">';
            echo '<thead>';
            echo '<tr style="background-color: #0d6efd; color: white;">';
            echo '<th colspan="5" style="font-size: 16px;">SISTEMA DE CONTROL DOCUMENTAL - BÚSQUEDA DE TRABAJADORES</th>';
            echo '</tr>';
            echo '<tr style="background-color: #333; color: white;">';
            echo '<th>Código Trabajador</th>';
            echo '<th>Nombre Completo</th>';
            echo '<th>Cédula</th>';
            echo '<th>Descripción Oficio</th>';
            echo '<th>Estado</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($resultados as $trabajador) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($trabajador['codigo_trabajador']) . '</td>';
                echo '<td>' . htmlspecialchars($trabajador['nombre_completo']) . '</td>';
                echo '<td>' . htmlspecialchars($trabajador['cedula']) . '</td>';
                echo '<td>' . htmlspecialchars($trabajador['descripcion_oficio']) . '</td>';
                $estado_texto = $trabajador['inhabilitado'] ? 'Inhabilitado' : 'Activo';
                echo '<td>' . htmlspecialchars($estado_texto) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            exit;
        }
    } else {
        $mensaje = "Debe ingresar al menos un código.";
    }
}


include 'includes/header.php';
?>

<div class="row mb-4 mt-5">
    <div class="col">
        <h2 class="fw-bold text-secondary"><i class="bi bi-search"></i> Buscar varios trabajadores</h2>
        <p class="text-muted">Ingrese uno o más <b>códigos de trabajador</b> separados por comas para obtener los datos correspondientes.</p>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje); ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="codigos" class="form-label">Códigos de trabajador (separados por coma)</label>
                        <textarea name="codigos" id="codigos" class="form-control" rows="3" placeholder="Ej: 123, 456, 789"><?php if (isset($_POST['codigos'])) echo htmlspecialchars($_POST['codigos']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <?php if (!empty($resultados)): ?>
                    <button type="submit" name="exportar" class="btn btn-success w-100">
                        <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                    </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($resultados)): ?>
<div class="row mb-5">
    <div class="col">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Resultados encontrados</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Código Trabajador</th>
                                <th>Nombre Completo</th>
                                <th>Cédula</th>
                                <th>Descripción Oficio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados as $trabajador): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($trabajador['codigo_trabajador']); ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['cedula']); ?></td>
                                    <td><?php echo htmlspecialchars($trabajador['descripcion_oficio']); ?></td>
                                    <td>
                                        <?php if ($trabajador['inhabilitado']): ?>
                                            <span class="badge bg-danger">Inhabilitado</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="buscar_trabajadores.php" class="btn btn-outline-dark mt-3"><i class="bi bi-arrow-left"></i> Nueva búsqueda</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>