<?php 
include 'seguridad.php'; // Solo usuarios logueados pueden registrar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCD - Nuevo Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .card { border-radius: 15px; border: none; }
        .card-header { border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5 shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="panel.php">SCD LEGAL</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="panel.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="consulta.php">Consultas</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Documento</h4>
                </div>
                <div class="card-body p-4">
                    <form action="procesar_registro.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Código del Trabajador</label>
                                <input type="text" name="codigo" class="form-control" placeholder="Ej: TR-502" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cédula de Identidad</label>
                                <input type="text" name="cedula" class="form-control" placeholder="001-000000-0000X" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre Completo del Trabajador</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Fotografía de Perfil (Opcional)</label>
                            <input type="file" name="foto_perfil" class="form-control" accept="image/*">
                            <div class="form-text">Formatos permitidos: JPG, PNG.</div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tipo de Documento</label>
                                <select name="tipo_documento" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una opción...</option>
                                    <option value="Embargo Judicial">Embargo Judicial</option>
                                    <option value="Pensión Alimenticia">Pensión Alimenticia</option>
                                    <option value="Otro">Otro Oficio Legal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Documento Original (PDF/Imagen)</label>
                                <input type="file" name="archivo_oficio" class="form-control" accept=".pdf, image/*" required>
                                <div class="form-text text-danger">Este archivo es obligatorio.</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Breve descripción del oficio</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Ej: Monto del embargo, juzgado emisor, etc."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i>Guardar en Base de Datos
                            </button>
                            <a href="panel.php" class="btn btn-light">Cancelar y Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>