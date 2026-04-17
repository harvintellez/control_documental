<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}
include 'conexion.php';

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = ($_POST['rol'] === 'admin') ? 'admin' : 'consulta';

    // Verificar si existe usando PDO
    $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
    $stmt_check->bindParam(':usuario', $usuario);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $mensaje = 'El usuario ya existe.';
    } else {
        $sql = "INSERT INTO usuarios (usuario, password, rol) VALUES (:usuario, :password, :rol)";
        $stmt_insert = $conexion->prepare($sql);
        $stmt_insert->bindParam(':usuario', $usuario);
        $stmt_insert->bindParam(':password', $password);
        $stmt_insert->bindParam(':rol', $rol);
        
        if ($stmt_insert->execute()) {
            header('Location: usuarios.php');
            exit;
        } else {
            $mensaje = 'Error al crear usuario.';
        }
    }
}

include 'includes/header.php';
?>

<div class="row mb-4 mt-5">
    <div class="col">
        <h2 class="fw-bold text-secondary"><i class="bi bi-person-plus-fill"></i> Crear nuevo usuario</h2>
        <p class="text-muted">Completa el siguiente formulario para registrar un nuevo usuario en el sistema.</p>
    </div>
</div>
<div class="row mb-5">
    <div class="col-md-6 col-lg-5 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($mensaje); ?></div>
                <?php endif; ?>
                <form method="post" autocomplete="off">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" name="usuario" id="usuario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select name="rol" id="rol" class="form-select">
                            <option value="admin">Administrador</option>
                            <option value="consulta">Consulta</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-person-plus"></i> Crear usuario
                    </button>
                </form>
            </div>
        </div>
        <a href="usuarios.php" class="btn btn-outline-dark mt-3">
            <i class="bi bi-arrow-left"></i> Volver a la lista de usuarios
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>