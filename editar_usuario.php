<?php
include 'seguridad.php';
include 'conexion.php';

if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $id");
$usuarioData = mysqli_fetch_assoc($result);

if (!$usuarioData) {
    header('Location: usuarios.php');
    exit;
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $rol = ($_POST['rol'] === 'admin') ? 'admin' : 'consulta';

    $update_sql = "UPDATE usuarios SET usuario='$usuario', rol='$rol' WHERE id=$id";
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_sql = "UPDATE usuarios SET usuario='$usuario', password='$password', rol='$rol' WHERE id=$id";
    }
    if (mysqli_query($conexion, $update_sql)) {
        header('Location: usuarios.php');
        exit;
    } else {
        $mensaje = 'Error al actualizar usuario.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Sistema Control Documental NSEL-CLNSA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="panel.php"><i class="bi bi-briefcase-fill me-2"></i>Sistema de Control Documental NSEL-CLNSA</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="registro.php">Nuevo Registro</a></li>
                <li class="nav-item"><a class="nav-link" href="consulta.php">Consultas</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-secondary"><i class="bi bi-pencil"></i> Editar usuario</h2>
            <p class="text-muted">Modifica los datos del usuario seleccionado.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-5 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-danger"><?php echo $mensaje; ?></div>
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
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>