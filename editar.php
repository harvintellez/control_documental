<?php
include 'conexion.php';
include 'seguridad.php';

// 1. Obtener el ID del trabajador desde la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM trabajadores WHERE id = $id";
    $resultado = mysqli_query($conexion, $query);

    if (mysqli_num_rows($resultado) == 1) {
        $row = mysqli_fetch_assoc($resultado);
        $nombre = $row['nombre_completo'];
        $cedula = $row['cedula'];
        $codigo = $row['codigo_trabajador'];
        $descripcion = $row['descripcion_oficio'];
        $tipo = $row['tipo_documento'];
    } else {
        header("Location: consulta.php?msg=No+encontrado");
        exit();
    }
}

// 2. Lógica para procesar la actualización
if (isset($_POST['actualizar'])) {
    $id = $_GET['id'];
    $nombre_nuevo = $_POST['nombre'];
    $cedula_nueva = $_POST['cedula'];
    $desc_nueva = $_POST['descripcion'];
    $tipo_nuevo = $_POST['tipo'];

    // Query de actualización básica (puedes expandirlo para fotos y PDF)
    $update_query = "UPDATE trabajadores SET 
        nombre_completo = '$nombre_nuevo', 
        cedula = '$cedula_nueva', 
        descripcion_oficio = '$desc_nueva',
        tipo_documento = '$tipo_nuevo' 
        WHERE id = $id";

    if (mysqli_query($conexion, $update_query)) {
        header("Location: consulta.php?status=success");
    } else {
        echo "Error al actualizar: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Trabajador - NSEL-CLSNA SCD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-header bg-warning py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Editar Registro: <?php echo $codigo; ?></h5>
                </div>
                <div class="card-body p-4">
                    <form action="editar.php?id=<?php echo $id; ?>" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" value="<?php echo $nombre; ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Cédula de Identidad</label>
                                <input type="text" name="cedula" class="form-control" value="<?php echo $cedula; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tipo de Oficio</label>
                                <select name="tipo" class="form-select">
                                    <option value="Embargo Judicial" <?php if($tipo == 'Embargo Judicial') echo 'selected'; ?>>Embargo Judicial</option>
                                    <option value="Pensión Alimenticia" <?php if($tipo == 'Pensión Alimenticia') echo 'selected'; ?>>Pensión Alimenticia</option>
                                    <option value="Otro" <?php if($tipo == 'Otro') echo 'selected'; ?>>Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Descripción del Oficio</label>
                            <textarea name="descripcion" class="form-control" rows="4"><?php echo $descripcion; ?></textarea>
                        </div>

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle me-2"></i>Nota: Para cambiar la fotografía o el PDF, favor de realizar un nuevo registro o contactar al administrador del sistema.
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="consulta.php" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" name="actualizar" class="btn btn-warning fw-bold">Actualizar Información</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>