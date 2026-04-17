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
    <link rel="stylesheet" href="font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .table img { object-fit: cover; border: 1px solid #dee2e6; }
        .btn-group .btn { margin: 0 2px; }
        .card-header { border-bottom: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="panel.php"><i class="bi bi-briefcase-fill me-2"></i>Sistema de Control Documental NSEL-CLNSA</a>
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
