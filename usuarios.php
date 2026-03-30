<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}
include 'conexion.php';

$resultado = mysqli_query($conexion, "SELECT id, usuario, rol FROM usuarios");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema Control Documental NSEL-CLNSA</title>
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
                <li class="nav-item"><a class="nav-link " href="buscar_trabajadores.php">Busquedas</a></li>
                <li class="nav-item"><a class="nav-link active" href="usuarios.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-secondary"><i class="bi bi-people"></i> Gestión de Usuarios</h2>
            <p class="text-muted">Administración de cuentas de acceso y roles del sistema.</p>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <a href="crear_usuario.php" class="btn btn-primary mb-3">
                <i class="bi bi-person-plus-fill"></i> Crear nuevo usuario
            </a>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                                    <td>
                                        <?php if ($row['rol'] == 'admin'): ?>
                                            <span class="badge bg-danger">Administrador</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Consulta</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="editar_usuario.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="eliminar_usuario.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este usuario?');">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <a href="panel.php" class="btn btn-outline-dark mt-3">
                <i class="bi bi-arrow-left"></i> Volver al panel principal
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>