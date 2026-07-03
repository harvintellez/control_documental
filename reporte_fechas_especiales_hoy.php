<?php
include 'seguridad.php';
include 'conexion.php';

date_default_timezone_set('America/Managua');

$hoy   = date('Y-m-d');
$hoy1  = date('Y-m-d', strtotime($hoy . ' +1 day'));
$hoy15 = date('Y-m-d', strtotime($hoy . ' +15 day'));

$hoy_mmdd = date('m-d', strtotime($hoy));
$hoy1_mmdd = date('m-d', strtotime($hoy1));
$hoy15_mmdd = date('m-d', strtotime($hoy15));

$sql = "
SELECT
  id,
  codigo_trabajador,
  nombre_completo,
  cedula,
  tipo_documento,
  fecha_registro,
  inhabilitado,
  archivo_adjunto,
  fecha_especial_1,
  fecha_especial_2,
  condicion_especial_1,
  condicion_especial_2
FROM trabajadores
WHERE fecha_especial_1 IS NOT NULL OR fecha_especial_2 IS NOT NULL
ORDER BY fecha_registro DESC
";

// Ejecutar
$stmt = $conexion->prepare($sql);
$stmt->execute();
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

$zona = new DateTimeZone('America/Managua');
$fechaHoy = new DateTime('now', $zona);
$fecha_actual = date('d/m/Y H:i');

function calcularDistanciaAniversario($fechaEspecial, DateTime $fechaHoy)
{
    if (empty($fechaEspecial)) {
        return [false, null, null];
    }

    $anioHoy = (int) $fechaHoy->format('Y');
    $mesDia = date('m-d', strtotime($fechaEspecial));
    $candidatos = [
        DateTime::createFromFormat('Y-m-d', "$anioHoy-$mesDia", $fechaHoy->getTimezone()),
        DateTime::createFromFormat('Y-m-d', ($anioHoy - 1) . "-$mesDia", $fechaHoy->getTimezone()),
        DateTime::createFromFormat('Y-m-d', ($anioHoy + 1) . "-$mesDia", $fechaHoy->getTimezone()),
    ];

    $distanciaMin = null;
    $fechaCoincidencia = null;

    foreach ($candidatos as $candidato) {
        if (!($candidato instanceof DateTime)) {
            continue;
        }

        $diff = (int) $fechaHoy->diff($candidato)->format('%r%a');
        $absDiff = abs($diff);

        if ($distanciaMin === null || $absDiff < $distanciaMin) {
            $distanciaMin = $absDiff;
            $fechaCoincidencia = $candidato;
        }
    }

    if ($distanciaMin === null) {
        return [false, null, null];
    }

    return [$distanciaMin <= 15, $distanciaMin, $fechaCoincidencia];
}

$registrosFiltrados = [];
foreach ($registros as $registro) {
    list($match1, $diff1) = calcularDistanciaAniversario($registro['fecha_especial_1'], $fechaHoy);
    list($match2, $diff2) = calcularDistanciaAniversario($registro['fecha_especial_2'], $fechaHoy);

    if ($match1 || $match2) {
        $registro['__match1'] = $match1;
        $registro['__match2'] = $match2;
        $registro['__diff1'] = $diff1;
        $registro['__diff2'] = $diff2;
        $registro['__mmdd1'] = $registro['fecha_especial_1'] ? date('m-d', strtotime($registro['fecha_especial_1'])) : null;
        $registro['__mmdd2'] = $registro['fecha_especial_2'] ? date('m-d', strtotime($registro['fecha_especial_2'])) : null;
        $registrosFiltrados[] = $registro;
    }
}

$registros = $registrosFiltrados;

include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-calendar2-day me-2"></i>Reporte automático por fechas especiales (Aniversario ±15 días)
        </div>
        <div class="card-body bg-white">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="text-muted small">Fecha de hoy (validación)</div>
                    <div class="fs-4 fw-bold"><?php echo htmlspecialchars(date('d/m/Y', strtotime($hoy))); ?></div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="text-muted small">Generado el</div>
                    <div class="fw-semibold"><?php echo htmlspecialchars($fecha_actual); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 no-print mb-3">
        <button type="button" class="btn btn-success" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
        <a href="panel.php" class="btn btn-outline-secondary">
            <i class="bi bi-house me-1"></i>Volver al Panel
        </a>
    </div>

    <div class="alert alert-info no-print" role="alert">
        Coincidencia por aniversario MM-DD dentro de ±15 días respecto a hoy (<?php echo htmlspecialchars($hoy_mmdd); ?>).
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Cédula</th>
                <th>Documento</th>
                <th>Fecha Especial 1</th>
                <th>Condición 1</th>
                <th>Fecha Especial 2</th>
                <th>Condición 2</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($registros) > 0): ?>
            <?php foreach ($registros as $r): ?>
                <?php
                    $inh = !empty($r['inhabilitado']);
                    $f1 = $r['fecha_especial_1'] ?? null;
                    $f2 = $r['fecha_especial_2'] ?? null;

                    $mmdd1 = $f1 ? date('m-d', strtotime($f1)) : null;
                    $mmdd2 = $f2 ? date('m-d', strtotime($f2)) : null;

                    $match1 = $r['__match1'] ?? false;
                    $match2 = $r['__match2'] ?? false;
                    $diff1 = $r['__diff1'] ?? null;
                    $diff2 = $r['__diff2'] ?? null;
                ?>
                <tr style="<?php echo $inh ? 'background-color:#f8d7da;color:#842029;' : ''; ?>">
                    <td class="text-center fw-bold"><?php echo htmlspecialchars($r['codigo_trabajador']); ?></td>
                    <td><?php echo htmlspecialchars($r['nombre_completo']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($r['cedula']); ?></td>
                    <td class="text-center">
                        <?php if (!empty($r['archivo_adjunto'])): ?>
                            <a href="<?php echo htmlspecialchars($r['archivo_adjunto']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf"></i> Ver
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>

                    <td class="text-center">
                        <?php if (!empty($f1)): ?>
                            <div><?php echo date('d/m/Y', strtotime($f1)); ?></div>
                            <div class="small text-muted">MM-DD: <?php echo htmlspecialchars($mmdd1); ?> | Distancia: <?php echo htmlspecialchars($diff1 !== null ? $diff1 . ' día(s)' : '—'); ?> <?php echo $match1 ? '✅' : '❌'; ?></div>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($r['condicion_especial_1'] ?? ''); ?></td>

                    <td class="text-center">
                        <?php if (!empty($f2)): ?>
                            <div><?php echo date('d/m/Y', strtotime($f2)); ?></div>
                            <div class="small text-muted">MM-DD: <?php echo htmlspecialchars($mmdd2); ?> | Distancia: <?php echo htmlspecialchars($diff2 !== null ? $diff2 . ' día(s)' : '—'); ?> <?php echo $match2 ? '✅' : '❌'; ?></div>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($r['condicion_especial_2'] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">No hay trabajadores con fechas especiales dentro de ±15 días de hoy (MM-DD).</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
@media print {
    .no-print { display: none !important; }
}
</style>

<?php include 'includes/footer.php'; ?>

