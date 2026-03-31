<?php
include 'seguridad.php'; // Opcional: si quieres control de sesión
//header('Location: panel.php'); // redirigimos al panel principal
//exit;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - SCD NSEL-CLNSA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .notfound-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
<div class="notfound-container">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-primary"><i class="bi bi-exclamation-triangle-fill"></i> 404</h1>
        <h2 class="fw-bold text-secondary mb-3">Página no encontrada</h2>
        <p class="lead mb-4">La página que buscas no existe o ha sido eliminada.</p>
        <a href="panel.php" class="btn btn-lg btn-primary">
            <i class="bi bi-arrow-left-circle"></i> Volver al inicio
        </a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>