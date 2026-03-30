<?php
include 'seguridad.php';
include 'conexion.php';

$resultados = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigos_raw = $_POST['codigos'];
    // Limpiar y separar los códigos
    $codigos = array_filter(array_map('trim', explode(',', $codigos_raw)), function($c) { return $c !== ''; });
    if (count($codigos) > 0) {
        // Generamos la lista segura para SQL
        $codigos_sql = implode(',', array_map(function($c) use ($conexion) { 
            return "'" . mysqli_real_escape_string($conexion, $c) . "'";
        }, $codigos));
        // Consulta la tabla
        $sql = "SELECT codigo_trabajador, nombre_completo, cedula, descripcion_oficio FROM trabajadores WHERE codigo_trabajador IN ($codigos_sql)";
        $result = mysqli_query($conexion, $sql);
        while ($fila = mysqli_fetch_assoc($result)) {
            $resultados[] = $fila;
        }
    } else {
        $mensaje = "Debe ingresar al menos un código.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda de Trabajadores - Sistema Control Documental NSEL-CLNSA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="panel.php"><i class="bi bi-briefcase-fill me-2"></i>Sistema de Control Documental NSEL-CLNSA</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link " href="panel.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="registro.php">Nuevo Registro</a></li>
                <li class="nav-item"><a class="nav-link " href="consulta.php">Consultas</a></li>
                <li class="nav-item"><a class="nav-link active" href="buscar_trabajadores.php">Busquedas</a></li>
                <li class="nav-item"><a class="nav-link " href="usuarios.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-secondary"><i class="bi bi-search"></i> Buscar varios trabajadores</h2>
            <p class="text-muted">Ingrese uno o más <b>códigos de trabajador</b> separados por comas para obtener los datos correspondientes.</p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-8 col-lg-6 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-danger"><?php echo $mensaje; ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="codigos" class="form-label">Códigos de trabajador (separados por coma)</label>
                            <textarea name="codigos" id="codigos" class="form-control" rows="3" placeholder="Ej: 123, 456, 789"><?php if (isset($_POST['codigos'])) echo htmlspecialchars($_POST['codigos']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if (!empty($resultados)): ?>
    <div class="row">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Resultados encontrados</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código Trabajador</th>
                                    <th>Nombre Completo</th>
                                    <th>Cédula</th>
                                    <th>Descripción Oficio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultados as $trabajador): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($trabajador['codigo_trabajador']); ?></td>
                                        <td><?php echo htmlspecialchars($trabajador['nombre_completo']); ?></td>
                                        <td><?php echo htmlspecialchars($trabajador['cedula']); ?></td>
                                        <td><?php echo htmlspecialchars($trabajador['descripcion_oficio']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="buscar_trabajadores.php" class="btn btn-outline-dark mt-3"><i class="bi bi-arrow-left"></i> Nueva búsqueda</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>