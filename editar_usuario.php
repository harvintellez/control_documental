<?php
include 'seguridad.php';
include 'conexion.php';

if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$id = intval($_GET['id']);
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuarioData) {
    header('Location: usuarios.php');
    exit;
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $rol = ($_POST['rol'] === 'admin') ? 'admin' : 'consulta';

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_sql = "UPDATE usuarios SET usuario=:usuario, password=:password, rol=:rol WHERE id=:id";
        $stmt_update = $conexion->prepare($update_sql);
        $stmt_update->bindParam(':usuario', $usuario);
        $stmt_update->bindParam(':password', $password);
        $stmt_update->bindParam(':rol', $rol);
        $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        $update_sql = "UPDATE usuarios SET usuario=:usuario, rol=:rol WHERE id=:id";
        $stmt_update = $conexion->prepare($update_sql);
        $stmt_update->bindParam(':usuario', $usuario);
        $stmt_update->bindParam(':rol', $rol);
        $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
    }

    if ($stmt_update->execute()) {
        header('Location: usuarios.php');
        exit;
    } else {
        $mensaje = 'Error al actualizar usuario.';
    }
}

include 'includes/header.php';
?>

<div class="row mb-4 mt-5">
    <div class="col">
        <h2 class="fw-bold text-secondary"><i class="bi bi-pencil"></i> Editar usuario</h2>
        <p class="text-muted">Modifica los datos del usuario seleccionado.</p>
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
                        <input type="text" name="usuario" id="usuario" class="form-control" value="<?php echo htmlspecialchars($usuarioData['usuario']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña (dejar vacío para no cambiar)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select name="rol" id="rol" class="form-select">
                            <option value="admin" <?php if($usuarioData['rol']=='admin') echo 'selected'; ?>>Administrador</option>
                            <option value="consulta" <?php if($usuarioData['rol']=='consulta') echo 'selected'; ?>>Consulta</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-pencil"></i> Actualizar usuario
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