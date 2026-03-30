<?php
include 'seguridad.php'; // Protegemos el acceso
include 'conexion.php';

// Consultas para las estadísticas
$total_trabajadores = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM trabajadores"))['total'];
$total_embargos = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Embargo Judicial'"))['total'];
$total_otros = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Otro'"))['total'];
$total_pensiones = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM trabajadores WHERE tipo_documento = 'Pensión Alimenticia'"))['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Sistema Control Documental de NSEL-CLNSA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="panel.php"><i class="bi bi-briefcase-fill me-2"></i>Sistema de Control Documental NSEL-CLNSA</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="registro.php">Nuevo Registro</a></li>
                <li class="nav-item"><a class="nav-link" href="consulta.php">Consultas</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-secondary">Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?></h2>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>