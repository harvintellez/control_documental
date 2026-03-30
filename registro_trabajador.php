<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Documentos Legales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-header { background-color: #0d6efd; color: white; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0 text-center">Registro de Trabajador y Oficio</h4>
                </div>
                <div class="card-body">
                    <form action="procesar_registro.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Código del Trabajador</label>
                                <input type="text" name="codigo" class="form-control" placeholder="Ej: TR-001" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cédula de Identidad</label>
                                <input type="text" name="cedula" class="form-control" placeholder="001-000000-0000X" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fotografía del Trabajador</label>
                            <input type="file" name="foto_perfil" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción del Oficio</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles sobre el embargo o pensión..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Documento del Oficio (PDF o Imagen)</label>
                            <input type="file" name="archivo_oficio" class="form-control" accept=".pdf,image/*" required>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Guardar Registro</button>
                            <a href="panel.php" class="btn btn-outline-secondary">Volver al Panel</a>
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