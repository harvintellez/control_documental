<?php
include 'seguridad.php'; // Protege la página
include 'conexion.php'; // Conecta a la BD

// Consulta para obtener todos los trabajadores
$sql = "SELECT * FROM trabajadores ORDER BY fecha_registro DESC";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSEL-CLNSA - Listado de Documentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
                <li class="nav-item"><a class="nav-link active" href="panel.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="registro.php">Nuevo Registro</a></li>
                <li class="nav-item"><a class="nav-link " href="consulta.php">Consultas</a></li>
                <li class="nav-item"><a class="nav-link " href="buscar_trabajadores.php">Busquedas</a></li>
                <li class="nav-item"><a class="nav-link " href="usuarios.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    
    <?php if (isset($_GET['res'])): ?>
        <?php if ($_GET['res'] == 'ok'): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Registro guardado con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['res'] == 'del'): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-trash-fill me-2"></i> Registro eliminado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> Información actualizada con éxito.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <h5 class="mb-0 fw-bold text-primary">Listado de Trabajadores</h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white border-primary">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="inputBusqueda" class="form-control border-primary" placeholder="Filtrar por nombre, cédula o código...">
                        <button class="btn btn-outline-secondary" type="button" id="btnLimpiar">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaTrabajadores">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Perfil</th>
                            <th>Código</th>
                            <th>Nombre Completo</th>
                            <th>Cédula</th>
                            <th>Tipo Oficio</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($resultado) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td class="ps-3">
                                    <?php if(!empty($row['foto_perfil'])): ?>
                                        <img src="<?php echo $row['foto_perfil']; ?>" class="rounded-circle" width="40" height="40">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle text-secondary" style="font-size: 40px;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo $row['codigo_trabajador']; ?></span></td>
                                <td><div class="fw-bold"><?php echo $row['nombre_completo']; ?></div></td>
                                <td><?php echo $row['cedula']; ?></td>
                                <td><small class="text-muted"><?php echo $row['tipo_documento']; ?></small></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo $row['archivo_adjunto']; ?>" target="_blank" class="btn btn-sm btn-info text-white" title="Ver Documento">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="eliminar.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('¿Seguro que deseas eliminar este registro?');" 
                                           title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No se encontraron registros en la base de datos.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-muted small">
            Mostrando todos los registros disponibles en el sistema.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Lógica del Buscador en tiempo real
    const inputBusqueda = document.getElementById('inputBusqueda');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const filas = document.querySelectorAll('#tablaTrabajadores tbody tr');

    inputBusqueda.addEventListener('keyup', function() {
        const filtro = this.value.toLowerCase();
        
        filas.forEach(fila => {
            const contenido = fila.innerText.toLowerCase();
            fila.style.display = contenido.includes(filtro) ? '' : 'none';
        });
    });

    // Lógica del botón Limpiar
    btnLimpiar.addEventListener('click', function() {
        inputBusqueda.value = '';
        filas.forEach(fila => fila.style.display = '');
        inputBusqueda.focus();
    });
</script>

</body>
</html>