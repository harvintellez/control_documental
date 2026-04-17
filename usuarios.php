<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}
include 'conexion.php';

$resultado = $conexion->query("SELECT id, usuario, rol FROM usuarios");

include 'includes/header.php';
?>

<div class="row mb-4 mt-5">
    <div class="col">
        <h2 class="fw-bold text-secondary"><i class="bi bi-people"></i> Gestión de Usuarios</h2>
        <p class="text-muted">Administración de cuentas de acceso y roles del sistema.</p>
    </div>
</div>

<div class="row mb-5">
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
                            <?php while($row = $resultado->fetch(PDO::FETCH_ASSOC)): ?>
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

<?php include 'includes/footer.php'; ?>