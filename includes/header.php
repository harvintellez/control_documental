<?php
// includes/header.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSEL-CLNSA - Sistema de Control Documental</title>
    <!-- Referencias locales para modo Offline -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/isa-colors.css" rel="stylesheet">
    <link rel="stylesheet" href="font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(180deg, #f4f7fb 0%, #eef2f7 40%, #f8f9fa 100%);
            min-height: 100vh;
        }
        .navbar {
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .navbar-brand img {
            max-height: 34px;
        }
        .card {
            border: none;
            border-radius: 1.2rem;
            box-shadow: 0 1.25rem 2.5rem rgba(41, 51, 63, 0.08);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1.75rem 3.5rem rgba(41, 51, 63, 0.16);
        }
        .card-header {
            border-bottom: none;
            background: transparent;
        }
        .stat-card {
            min-height: 180px;
        }
        .stat-card .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 1rem;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,.18);
        }
        .table thead th {
            border-bottom: 2px solid #dee2e6;
            background: rgba(13,110,253,.08);
            color: #374151;
            font-weight: 600;
        }
        .table tbody tr {
            transition: background-color .15s ease;
        }
        .table-hover tbody tr:hover {
            background-color: #f3f6fb;
        }
        .table-responsive {
            background-color: #ffffff;
            border-radius: 1rem;
            overflow: hidden;
        }
        .table-responsive-scroll {
            max-height: 62vh;
            overflow: auto;
        }
        .table-responsive-scroll table {
            min-width: 1100px;
        }
        .btn, .btn-outline-dark {
            transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .btn-outline-dark:hover {
            background-color: #343a40;
            color: #fff;
        }
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }
        .badge.bg-secondary {
            background-color: #6c757d !important;
        }
        .card.bg-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #4f9bff 100%);
        }
        .card.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e86478 100%);
        }
        .card.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #8b95a2 100%);
        }
        .card.bg-success {
            background: linear-gradient(135deg, #198754 0%, #4bbf73 100%);
        }
        .dark-mode body {
            background: #0b1220 !important;
            color: #e2e8f0;
        }
        .dark-mode .navbar {
            background-color: #07111e !important;
            border-color: rgba(255,255,255,.08);
        }
        .dark-mode .card,
        .dark-mode .table-responsive {
            background-color: #0f172a !important;
            color: #e2e8f0;
            box-shadow: 0 1.25rem 2.5rem rgba(0, 0, 0, 0.4);
        }
        .dark-mode .card-header {
            background: transparent;
            border-bottom-color: rgba(226,232,240,.08);
        }
        .dark-mode .table thead th {
            background: rgba(30, 64, 175, .14);
            color: #e2e8f0;
        }
        .dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255,255,255,.08);
        }
        .dark-mode .text-muted,
        .dark-mode .small,
        .dark-mode .badge.bg-secondary {
            color: #94a3b8 !important;
        }
        .dark-mode .btn-outline-dark {
            color: #e2e8f0;
            border-color: rgba(226,232,240,.35);
        }
        .dark-mode .btn-outline-dark:hover {
            background-color: rgba(226,232,240,.12);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="panel.php">
            <img src="img/isa-logo.png" alt="ISA" style="max-height: 35px;" class="me-2">
            <span>Control Documental</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'panel.php') ? 'active' : '' ?>" href="panel.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'registro.php') ? 'active' : '' ?>" href="registro.php">Nuevo Registro</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'consulta.php') ? 'active' : '' ?>" href="consulta.php">Consultas</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'buscar_trabajadores.php') ? 'active' : '' ?>" href="buscar_trabajadores.php">Búsquedas</a></li>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= ($current_page == 'usuarios.php' || $current_page == 'carga_masiva.php') ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear-fill me-1"></i>Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item <?= ($current_page == 'usuarios.php') ? 'active' : '' ?>" href="usuarios.php"><i class="bi bi-people me-2"></i>Usuarios</a></li>
                        <li><a class="dropdown-item <?= ($current_page == 'carga_masiva.php') ? 'active' : '' ?>" href="carga_masiva.php"><i class="bi bi-file-earmark-arrow-up me-2"></i>Carga Masiva</a></li>
                        <li><a class="dropdown-item <?= ($current_page == 'configuracion_bancos.php') ? 'active' : '' ?>" href="configuracion_bancos.php"><i class="bi bi-bank2 me-2"></i>Configuración de Bancos</a></li>
                        <li><a class="dropdown-item <?= ($current_page == 'auditoria_trabajadores.php') ? 'active' : '' ?>" href="auditoria_trabajadores.php"><i class="bi bi-journal-text me-2"></i>Log de Auditoría</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?= ($current_page == 'mantenimiento.php') ? 'active' : '' ?>" href="mantenimiento.php"><i class="bi bi-shield-lock me-2 text-danger"></i>Mantenimiento del Sistema</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'usuarios.php') ? 'active' : '' ?>" href="usuarios.php">Usuarios</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
