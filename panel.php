<?php
include 'seguridad.php'; // Protegemos el acceso
include 'conexion.php';

// Consultas para las estadísticas con PDO
$total_trabajadores = $conexion->query("SELECT COUNT(*) as total FROM trabajadores WHERE  inhabilitado = 0")->fetch(PDO::FETCH_ASSOC)['total'];
$total_embargos = $conexion->query("SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Embargo Judicial' AND inhabilitado = 0")->fetch(PDO::FETCH_ASSOC)['total'];
$total_otros = $conexion->query("SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Otro'AND inhabilitado = 0")->fetch(PDO::FETCH_ASSOC)['total'];
$total_pensiones = $conexion->query("SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Pensión Alimenticia'AND inhabilitado = 0")->fetch(PDO::FETCH_ASSOC)['total'];

$top_trabajadores = $conexion->query(
    "SELECT codigo_trabajador, nombre_completo, MIN(cedula) AS cedula, COUNT(*) AS total_embargos " .
    "FROM trabajadores " .
    "WHERE inhabilitado = 0 " .
    "GROUP BY codigo_trabajador, nombre_completo " .
    "HAVING total_embargos > 1 " .
    "ORDER BY total_embargos DESC, nombre_completo ASC " .
    "LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);

$documentos_por_tipo = $conexion->query(
    "SELECT tipo_documento, COUNT(*) AS total FROM trabajadores WHERE inhabilitado = 0 GROUP BY tipo_documento"
)->fetchAll(PDO::FETCH_ASSOC);

$judicial_por_banco = $conexion->query(
    "SELECT banco_institucion, COUNT(*) AS total 
     FROM trabajadores 
     WHERE tipo_documento = 'Embargo Judicial' AND inhabilitado = 0 AND TRIM(COALESCE(banco_institucion, '')) <> ''
     GROUP BY banco_institucion
     ORDER BY total DESC, banco_institucion ASC
     LIMIT 8"
)->fetchAll(PDO::FETCH_ASSOC);

$ultimos_registros = $conexion->query(
    "SELECT codigo_trabajador, nombre_completo, tipo_documento, fecha_registro FROM trabajadores WHERE inhabilitado = 0 ORDER BY fecha_registro DESC LIMIT 3"
)->fetchAll(PDO::FETCH_ASSOC);

$timezone = new DateTimeZone('America/Managua');
$now = new DateTime('now', $timezone);
$current_hour = (int)$now->format('H');
$local_time = $now->format('d/m/Y H:i');

if ($current_hour >= 6 && $current_hour < 12) {
    $saludo = 'Buenos días';
    $sub_message = '¡Feliz mañana! El sol está saliendo y tu panel está listo para empezar.';
    $icon_svg = <<<'SVG'
<svg width="68" height="68" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
  <circle cx="32" cy="30" r="10" fill="#FFD54F"/>
  <path d="M32 4V14" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M32 46V56" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M10 30H20" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M44 30H54" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M16 14L22 20" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M42 14L36 20" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M16 46L22 40" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M42 46L36 40" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M4 54H60" stroke="#FFB300" stroke-width="4" stroke-linecap="round"/>
</svg>
SVG;
} elseif ($current_hour >= 12 && $current_hour < 18) {
    $saludo = 'Buenas tardes';
    $sub_message = 'El sol está cerca del ocaso y tu panel sigue funcionando con fuerza.';
    $icon_svg = <<<'SVG'
<svg width="68" height="68" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
  <circle cx="32" cy="34" r="12" fill="#FFB300"/>
  <path d="M4 52C10 44 18 40 32 40C46 40 54 44 60 52" stroke="#FF8F00" stroke-width="4" stroke-linecap="round"/>
  <path d="M32 8V18" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M20 12L26 18" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
  <path d="M44 12L38 18" stroke="#FFB300" stroke-width="3" stroke-linecap="round"/>
</svg>
SVG;
} else {
    $saludo = 'Buenas noches';
    $sub_message = 'La luna y las estrellas te acompañan mientras revisas tu panel.';
    $icon_svg = <<<'SVG'
<svg width="68" height="68" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M40 14C36 14 32 16 30 20C24 24 24 32 26 38C20 40 14 44 14 52C20 52 26 54 32 54C46 54 56 44 56 30C56 22 50 16 42 14C42 14 41 14 40 14Z" fill="#90CAF9"/>
  <circle cx="18" cy="18" r="3" fill="#FFF59D"/>
  <circle cx="46" cy="12" r="2" fill="#FFF59D"/>
  <circle cx="52" cy="26" r="2.5" fill="#FFF59D"/>
  <circle cx="34" cy="8" r="2" fill="#FFF59D"/>
  <circle cx="44" cy="40" r="1.8" fill="#FFF59D"/>
</svg>
SVG;
}

include 'includes/header.php';
?>

<div class="row mb-4 mt-5">
    <div class="col">
        <h2 class="fw-bold text-secondary">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h2>
        <p class="text-muted">Resumen general de documentos legales registrados.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3 flex-column flex-sm-row">
                    <div class="d-flex align-items-center justify-content-center bg-light rounded-4" style="width:100px;height:100px;">
                        <?= $icon_svg ?>
                    </div>
                    <div>
                        <p class="text-muted small mb-1">Hora local GMT-6 (Nicaragua)</p>
                        <h5 class="mb-1 fw-bold"><?= $saludo ?>, </h5>
                        <p class="mb-1 text-muted"><?= $sub_message ?></p>
                        <span class="badge bg-primary bg-opacity-10 text-primary py-2 px-3"><?= $local_time ?></span>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <span class="badge bg-info text-dark py-2 px-3">Diseño responsivo</span>
                    <button id="themeToggle" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-moon-stars me-1"></i>Modo oscuro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card stat-card overflow-hidden border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase opacity-75 mb-2">Total Trabajadores</h6>
                        <h2 class="display-5 fw-bold mb-0"><?php echo $total_trabajadores; ?></h2>
                    </div>
                    <div class="icon-circle">
                        <i class="bi bi-people-fill fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card stat-card overflow-hidden border-0 shadow-sm bg-danger text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase opacity-75 mb-2">Embargos Judiciales</h6>
                        <h2 class="display-5 fw-bold mb-0"><?php echo $total_embargos; ?></h2>
                    </div>
                    <div class="icon-circle">
                        <i class="bi bi-bank fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card stat-card overflow-hidden border-0 shadow-sm bg-secondary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase opacity-75 mb-2">Otros Embargos</h6>
                        <h2 class="display-5 fw-bold mb-0"><?php echo $total_otros; ?></h2>
                    </div>
                    <div class="icon-circle">
                        <i class="bi bi-cash-coin fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card stat-card overflow-hidden border-0 shadow-sm bg-success text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase opacity-75 mb-2">Pensiones Alimenticias</h6>
                        <h2 class="display-5 fw-bold mb-0"><?php echo $total_pensiones; ?></h2>
                    </div>
                    <div class="icon-circle">
                        <i class="bi bi-heart-pulse-fill fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <h4 class="mb-4">¿Qué deseas hacer hoy?</h4>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="registro.php" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-plus-circle me-2"></i> Registrar Oficio
                    </a>
                    <a href="consulta.php" class="btn btn-outline-dark btn-lg px-4">
                        <i class="bi bi-search me-2"></i> Consultar Documentos
                    </a>
                    <a href="reporte_imprimible.php" class="btn btn-outline-danger btn-lg px-4">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i> Generar Reporte PDF
                    </a>
                    <a href="reporte_fechas_especiales_hoy.php" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-lg px-4">
                        <i class="bi bi-calendar2-day me-2"></i>Reporte Fechas Especiales (Hoy)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<hr class="my-5 border-2 border-primary border-opacity-10">
<div class="row g-4 mb-5">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-bar-chart-line me-2"></i>Distribución por tipo de documento</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary py-2 px-3">Total ordenado</span>
                </div>
            </div>
            <div class="card-body p-4">
                <canvas id="documentTypeChart" height="240"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-success"><i class="bi bi-clock-history me-2"></i>Últimos registros</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary py-2 px-3">Recientes</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($ultimos_registros)): ?>
                        <?php foreach ($ultimos_registros as $registro): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div>
                                    <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($registro['nombre_completo']); ?></h6>
                                    <p class="mb-1 text-muted small"><?php echo htmlspecialchars($registro['tipo_documento']); ?></p>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($registro['fecha_registro'])); ?></small>
                                </div>
                                <span class="badge bg-secondary align-self-start"><?php echo htmlspecialchars($registro['codigo_trabajador']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">No hay registros recientes disponibles.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-danger"><i class="bi bi-bank me-2"></i>Embargos Judiciales por banco</h5>
                    <span class="badge bg-danger bg-opacity-10 text-danger py-2 px-3">Top 8 instituciones</span>
                </div>
            </div>
            <div class="card-body p-4">
                <canvas id="judicialBankChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-award me-2"></i>Top 10 trabajadores con más de 1 embargo</h5>
                    <div class="d-flex gap-2">
                        <a href="exportar_top10.php" class="btn btn-sm btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                        </a>
                        <a href="reporte_top10.php" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-danger">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Generar PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Trabajador</th>
                                <th>Cédula</th>
                                <th class="text-center">Embargos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($top_trabajadores)): ?>
                                <?php foreach ($top_trabajadores as $index => $trabajador): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($trabajador['codigo_trabajador']); ?></span></td>
                                        <td><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></td>
                                        <td><?php echo htmlspecialchars($trabajador['cedula']); ?></td>
                                        <td class="text-center"><strong><?php echo htmlspecialchars($trabajador['total_embargos']); ?></strong></td>
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
</div>

<?php include 'includes/footer.php'; ?>

<script src="js/chart.min.js"></script>
<script>
    const labels = [
        <?php foreach ($documentos_por_tipo as $item) {
            echo '"' . htmlspecialchars($item['tipo_documento']) . '",';
        } ?>
    ];
    const dataValues = [
        <?php foreach ($documentos_por_tipo as $item) {
            echo (int)$item['total'] . ',';
        } ?>
    ];

    const ctx = document.getElementById('documentTypeChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: [
                        '#0d6efd',
                        '#dc3545',
                        '#6c757d',
                        '#198754',
                        '#ffc107',
                        '#0dcaf0'
                    ],
                    borderWidth: 1,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: window.getComputedStyle(document.body).color
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw || 0;
                                return context.label + ': ' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    const judicialBankLabels = [
        <?php foreach ($judicial_por_banco as $item) {
            echo '"' . htmlspecialchars($item['banco_institucion']) . '",';
        } ?>
    ];
    const judicialBankData = [
        <?php foreach ($judicial_por_banco as $item) {
            echo (int)$item['total'] . ',';
        } ?>
    ];

    const bankCtx = document.getElementById('judicialBankChart');
    if (bankCtx && judicialBankData.length > 0) {
        new Chart(bankCtx, {
            type: 'bar',
            data: {
                labels: judicialBankLabels,
                datasets: [{
                    label: 'Embargos judiciales',
                    data: judicialBankData,
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#dc3545',
                        '#fd7e14',
                        '#6f42c1',
                        '#20c997',
                        '#ffc107',
                        '#6c757d'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw || 0;
                                return 'Embargos: ' + value;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: window.getComputedStyle(document.body).color,
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: false
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: window.getComputedStyle(document.body).color
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.08)'
                        }
                    }
                }
            }
        });
    }

    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                themeToggle.innerHTML = '<i class="bi bi-sun-fill me-1"></i>Modo claro';
            } else {
                themeToggle.innerHTML = '<i class="bi bi-moon-stars me-1"></i>Modo oscuro';
            }
        });
    }
</script>