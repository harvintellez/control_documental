<?php
include 'seguridad.php'; // Protegemos el acceso
include 'conexion.php';

// Consultas para las estadísticas con PDO
$total_trabajadores = $conexion->query("SELECT COUNT(*) as total FROM trabajadores WHERE  inhabilitado = 0")->fetch(PDO::FETCH_ASSOC)['total'];
$total_embargos = $conexion->query("SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Embargo Judicial' AND inhabilitado = 0")->fetch(PDO::FETCH_ASSOC)['total'];
$total_otros = $conexion->query("SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Otro'AND inhabilitado = 0")->fetch(PDO::FETCH_ASSOC)['total'];
$total_pensiones = $conexion->query("SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Pensión Alimenticia'AND inhabilitado = 0")->fetch(PDO::FETCH_ASSOC)['total'];

include 'includes/header.php';
?>

<div class="row mb-4 mt-5">
    <div class="col">
        <h2 class="fw-bold text-secondary">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h2>
        <p class="text-muted">Resumen general de documentos legales registrados.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Total Trabajadores</h6>
                        <h2 class="display-4 fw-bold"><?php echo $total_trabajadores; ?></h2>
                    </div>
                    <i class="bi bi-people-fill opacity-50" style="font-size: 3.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Embargos Judiciales</h6>
                        <h2 class="display-4 fw-bold"><?php echo $total_embargos; ?></h2>
                    </div>
                    <i class="bi bi-gavel opacity-50" style="font-size: 3.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm bg-secondary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Otros Embargos</h6>
                        <h2 class="display-4 fw-bold"><?php echo $total_otros; ?></h2>
                    </div>
                    <i class="bi bi-gavel opacity-50" style="font-size: 3.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Pensiones Alimenticias</h6>
                        <h2 class="display-4 fw-bold"><?php echo $total_pensiones; ?></h2>
                    </div>
                    <i class="bi bi-heart-pulse-fill opacity-50" style="font-size: 3.5rem;"></i>
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
                <div class="d-flex justify-content-center gap-3">
                    <a href="registro.php" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-plus-circle me-2"></i> Registrar Oficio
                    </a>
                    <a href="consulta.php" class="btn btn-outline-dark btn-lg px-4">
                        <i class="bi bi-search me-2"></i> Consultar Documentos
                    </a>
                    <a href="reporte_imprimible.php" class="btn btn-outline-danger btn-lg px-4">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i> Generar Reporte PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>