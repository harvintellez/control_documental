<?php
include 'seguridad.php';
include 'conexion.php';
include 'includes/auditoria.php';

// Solo administradores pueden acceder
if ($_SESSION['rol'] !== 'admin') {
    header("Location: panel.php?error=sin_permiso");
    exit();
}

$mensaje = "";
$error = "";

if (isset($_POST['guardar_banco'])) {
    $nombreBanco = trim($_POST['nombre_banco'] ?? '');
    if ($nombreBanco === '') {
        $error = 'El nombre del banco o institución es obligatorio.';
    } else {
        try {
            $stmt = $conexion->prepare("INSERT INTO configuracion_bancos (nombre, activo) VALUES (:nombre, 1) ON DUPLICATE KEY UPDATE activo = 1");
            $stmt->execute([':nombre' => $nombreBanco]);
            registrar_auditoria_configuracion($conexion, 'CONFIG_BANCO_AGREGAR', $_SESSION['usuario_nombre'] ?? $_SESSION['usuario'], 'configuracion_bancos', null, $nombreBanco, 'Se agregó el banco/institución al catálogo.' );
            $mensaje = 'Banco o institución agregada correctamente.';
        } catch (Exception $e) {
            $error = 'No se pudo guardar el banco: ' . $e->getMessage();
        }
    }
}

if (isset($_POST['eliminar_banco'])) {
    $idBanco = isset($_POST['id_banco']) ? (int) $_POST['id_banco'] : 0;
    if ($idBanco <= 0) {
        $error = 'No se pudo identificar el banco a eliminar.';
    } else {
        try {
            $stmt = $conexion->prepare("SELECT nombre FROM configuracion_bancos WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $idBanco]);
            $banco = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($banco) {
                $stmtDelete = $conexion->prepare("DELETE FROM configuracion_bancos WHERE id = :id");
                $stmtDelete->execute([':id' => $idBanco]);
                registrar_auditoria_configuracion($conexion, 'CONFIG_BANCO_ELIMINAR', $_SESSION['usuario_nombre'] ?? $_SESSION['usuario'], 'configuracion_bancos', $banco['nombre'], null, 'Se eliminó el banco/institución del catálogo.');
                $mensaje = 'Banco o institución eliminada correctamente.';
            }
        } catch (Exception $e) {
            $error = 'No se pudo eliminar el banco: ' . $e->getMessage();
        }
    }
}

if (isset($_POST['guardar_edicion_banco'])) {
    $idBanco = isset($_POST['id_banco_editar']) ? (int) $_POST['id_banco_editar'] : 0;
    $nombreBanco = trim($_POST['nombre_banco_editar'] ?? '');

    if ($idBanco <= 0 || $nombreBanco === '') {
        $error = 'La edición del banco requiere un nombre válido.';
    } else {
        try {
            $stmtActual = $conexion->prepare("SELECT nombre FROM configuracion_bancos WHERE id = :id LIMIT 1");
            $stmtActual->execute([':id' => $idBanco]);
            $bancoActual = $stmtActual->fetch(PDO::FETCH_ASSOC);

            if ($bancoActual) {
                $stmtUpdate = $conexion->prepare("UPDATE configuracion_bancos SET nombre = :nombre WHERE id = :id");
                $stmtUpdate->execute([
                    ':nombre' => $nombreBanco,
                    ':id' => $idBanco,
                ]);

                registrar_auditoria_configuracion(
                    $conexion,
                    'CONFIG_BANCO_EDITAR',
                    $_SESSION['usuario_nombre'] ?? $_SESSION['usuario'],
                    'configuracion_bancos',
                    $bancoActual['nombre'],
                    $nombreBanco,
                    'Se editó el nombre del banco/institución del catálogo.'
                );

                $mensaje = 'Banco o institución actualizada correctamente.';
            }
        } catch (Exception $e) {
            $error = 'No se pudo editar el banco: ' . $e->getMessage();
        }
    }
}

$bancos_config = $conexion->query("SELECT id, nombre FROM configuracion_bancos WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

// Lógica para exportar estructura y usuarios
if (isset($_POST['descargar_sql'])) {
    try {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="estructura_y_usuarios_' . date('Ymd_His') . '.sql"');

        // 1. Obtener estructura de las tablas (trabajadores y usuarios)
        $tablas = ['trabajadores', 'usuarios'];
        echo "-- Estructura de la base de datos limpia\n";
        echo "-- Generado el: " . date('Y-m-d H:i:s') . "\n\n";
        echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tablas as $tabla) {
            $res = $conexion->query("SHOW CREATE TABLE `$tabla`")->fetch(PDO::FETCH_ASSOC);
            echo "DROP TABLE IF EXISTS `$tabla`;\n";
            echo $res['Create Table'] . ";\n\n";
        }

        // 2. Exportar datos de la tabla usuarios
        echo "-- Datos de la tabla usuarios\n";
        $stmt = $conexion->query("SELECT * FROM usuarios");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($usuarios)) {
            $columnas = array_keys($usuarios[0]);
            $cols_str = implode("`, `", $columnas);
            echo "INSERT INTO `usuarios` (`$cols_str`) VALUES\n";
            
            $values = [];
            foreach ($usuarios as $u) {
                $row_values = array_map(function($v) use ($conexion) {
                    if ($v === null) return 'NULL';
                    return $conexion->quote($v);
                }, array_values($u));
                $values[] = "(" . implode(", ", $row_values) . ")";
            }
            echo implode(",\n", $values) . ";\n\n";
        }

        echo "SET FOREIGN_KEY_CHECKS = 1;\n";
        exit();

    } catch (Exception $e) {
        $error = "Error al generar el archivo SQL: " . $e->getMessage();
    }
}// Lógica para respaldo completo (ZIP: SQL + Uploads) usando comando del sistema (tar/zip)
if (isset($_POST['respaldo_completo'])) {
    try {
        $backup_name = 'respaldo_total_' . date('Ymd_His') . '.zip';
        $sql_file = 'temp_db_' . time() . '.sql';

        // 1. Generar SQL de TODA la base de datos
        $sql_content = "-- Respaldo Total de Base de Datos\n-- Generado el: " . date('Y-m-d H:i:s') . "\n\n";
        $sql_content .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        
        $tablas = ['usuarios', 'trabajadores'];
        foreach ($tablas as $tabla) {
            $res = $conexion->query("SHOW CREATE TABLE `$tabla` ")->fetch(PDO::FETCH_ASSOC);
            $sql_content .= "DROP TABLE IF EXISTS `$tabla`;\n" . $res['Create Table'] . ";\n\n";
            
            $stmt = $conexion->query("SELECT * FROM `$tabla` ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $cols = implode("`, `", array_keys($rows[0]));
                $sql_content .= "INSERT INTO `$tabla` (`$cols`) VALUES\n";
                $vals = [];
                foreach ($rows as $r) {
                    $row_vals = array_map(function($v) use ($conexion) {
                        return ($v === null) ? 'NULL' : $conexion->quote($v);
                    }, array_values($r));
                    $vals[] = "(" . implode(", ", $row_vals) . ")";
                }
                $sql_content .= implode(",\n", $vals) . ";\n\n";
            }
        }
        $sql_content .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        file_put_contents($sql_file, $sql_content);

        // 2. Crear ZIP usando el comando 'tar' de Windows (que soporta formato zip)
        // tar -a -c -f nombre.zip archivo1 carpeta
        $comando = "tar -a -c -f $backup_name $sql_file uploads";
        exec($comando, $output, $return_var);

        if ($return_var !== 0) {
            throw new Exception("Error al ejecutar el comando de compresión: " . implode("\n", $output));
        }

        // Enviar el archivo para descarga
        if (file_exists($backup_name)) {
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $backup_name);
            header('Content-Length: ' . filesize($backup_name));
            readfile($backup_name);
            
            // Limpieza
            unlink($sql_file);
            unlink($backup_name);
            exit();
        } else {
            throw new Exception("No se pudo generar el archivo de respaldo.");
        }

    } catch (Exception $e) {
        if (isset($sql_file) && file_exists($sql_file)) unlink($sql_file);
        $error = "Error al generar el respaldo total: " . $e->getMessage();
    }
}

// Lógica para limpiar la base de datos y archivos (BORRADO TOTAL)
if (isset($_POST['limpiar_bd'])) {
    if ($_POST['confirmacion'] === 'LIMPIAR_SISTEMA_AHORA') {
        try {
            // 1. Limpiamos la tabla de trabajadores
            $conexion->exec("TRUNCATE TABLE trabajadores");

            // 2. Limpiamos físicamente los archivos en uploads
            $base_uploads = 'uploads/';
            $subcarpetas = ['fotos', 'documentos', 'inhabilitaciones', 'csv'];
            
            foreach ($subcarpetas as $sub) {
                $dir = $base_uploads . $sub . '/';
                if (is_dir($dir)) {
                    $files = glob($dir . '*'); // Obtener todos los archivos
                    foreach ($files as $file) {
                        if (is_file($file) && basename($file) !== 'index.php' && basename($file) !== '.htaccess') {
                            unlink($file); // Eliminar archivo
                        }
                    }
                }
            }

            $mensaje = "El sistema ha sido limpiado por completo. Se eliminaron todos los registros de trabajadores y sus archivos adjuntos.";
        } catch (Exception $e) {
            $error = "Error al limpiar el sistema: " . $e->getMessage();
        }
    } else {
        $error = "La palabra de confirmación no es correcta.";
    }
}

include 'includes/header.php';
?>

<div class="row mt-5 mb-5">
    <div class="col-md-8 mx-auto">
        <div class="card shadow border-0">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Mantenimiento del Sistema (Solo Admin)</h5>
            </div>
            <div class="card-body p-4">
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?php echo $mensaje; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <h6 class="fw-bold"><i class="bi bi-info-circle-fill me-2"></i>Instrucciones:</h6>
                    <p class="mb-0">Desde esta página puede descargar la estructura actual del sistema junto con los usuarios configurados. También puede limpiar los datos de trabajadores para comenzar una nueva gestión desde cero.</p>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-12" id="configuracion-bancos">
                        <div class="card border-info shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1"><i class="bi bi-bank me-2"></i>Configuración de Bancos / Instituciones</h6>
                                        <p class="small text-muted mb-0">Estos valores se usan cuando el tipo de embargo es judicial y solo pueden gestionarlos los administradores.</p>
                                    </div>
                                </div>

                                <form method="POST" class="row g-2 align-items-end mb-3">
                                    <div class="col-md-9">
                                        <label class="form-label fw-bold">Agregar banco o institución</label>
                                        <input type="text" name="nombre_banco" class="form-control" placeholder="Ej: Lafise Bancentro, BAC, BANPRO" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" name="guardar_banco" class="btn btn-info w-100 text-white">
                                            <i class="bi bi-plus-circle me-2"></i>Agregar
                                        </button>
                                    </div>
                                </form>

                                <?php if (!empty($bancos_config)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Banco / Institución</th>
                                                    <th class="text-end">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($bancos_config as $banco): ?>
                                                    <tr>
                                                        <td class="pe-3">
                                                            <div class="fw-semibold"><?= htmlspecialchars($banco['nombre']) ?></div>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                                                <form method="POST" class="d-flex gap-2 align-items-center">
                                                                    <input type="hidden" name="id_banco_editar" value="<?= (int)$banco['id'] ?>">
                                                                    <input type="text" name="nombre_banco_editar" value="<?= htmlspecialchars($banco['nombre']) ?>" class="form-control form-control-sm" style="min-width: 220px;" required>
                                                                    <button type="submit" name="guardar_edicion_banco" class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-pencil-square me-1"></i>Guardar
                                                                    </button>
                                                                </form>
                                                                <form method="POST" onsubmit="return confirm('¿Desea eliminar este banco o institución de la lista?');">
                                                                    <input type="hidden" name="id_banco" value="<?= (int)$banco['id'] ?>">
                                                                    <button type="submit" name="eliminar_banco" class="btn btn-sm btn-outline-danger">
                                                                        <i class="bi bi-trash me-1"></i>Eliminar
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light border mb-0">No hay bancos configurados aún.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Respaldo Total -->
                    <div class="col-md-12">
                        <div class="card border-primary shadow-sm">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-archive-fill text-primary me-3" style="font-size: 2.5rem;"></i>
                                    <div class="text-start">
                                        <h6 class="fw-bold mb-1">Respaldo Total del Sistema</h6>
                                        <p class="small text-muted mb-0">Descarga un archivo ZIP con <b>toda</b> la base de datos y todos los archivos subidos (fotos y documentos).</p>
                                    </div>
                                </div>
                                <form method="POST">
                                    <button type="submit" name="respaldo_completo" class="btn btn-primary px-4">
                                        <i class="bi bi-cloud-download me-2"></i>Generar Respaldo (.zip)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Exportar Estructura -->
                    <div class="col-md-6">
                        <div class="card h-100 border-secondary">
                            <div class="card-body text-center">
                                <i class="bi bi-file-earmark-arrow-down text-primary mb-3" style="font-size: 3rem;"></i>
                                <h6 class="fw-bold">Exportar Estructura</h6>
                                <p class="small text-muted">Descarga un archivo .sql con la creación de tablas y los usuarios actuales (sin trabajadores).</p>
                                <form method="POST">
                                    <button type="submit" name="descargar_sql" class="btn btn-primary w-100">
                                        <i class="bi bi-download me-2"></i>Descargar SQL
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sección Limpiar -->
                    <div class="col-md-6">
                        <div class="card h-100 border-danger">
                            <div class="card-body text-center">
                                <i class="bi bi-trash3 text-danger mb-3" style="font-size: 3rem;"></i>
                                <h6 class="fw-bold">Limpiar Datos y Archivos</h6>
                                <p class="small text-muted">Elimina permanentemente <b>todos</b> los registros de trabajadores y borra todas las fotos y documentos subidos.</p>
                                
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#modalConfirmarLimpieza">
                                    <i class="bi bi-eraser-fill me-2"></i>Limpiar Sistema
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="panel.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación Crítica -->
<div class="modal fade" id="modalConfirmarLimpieza" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>¡Advertencia Crítica!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger fw-bold">Esta acción no se puede deshacer.</p>
                    <p>Se borrarán todos los registros de la tabla de trabajadores y <b>se eliminarán físicamente todos los archivos</b> (fotos y documentos) del servidor.</p>
                    <p>Para confirmar, escriba la siguiente frase exactamente:</p>
                    <div class="bg-light p-2 text-center mb-3 border rounded">
                        <code class="fw-bold text-dark">LIMPIAR_SISTEMA_AHORA</code>
                    </div>
                    <input type="text" name="confirmacion" class="form-control" placeholder="Escriba la frase aquí..." required autocomplete="off">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="limpiar_bd" class="btn btn-danger fw-bold">Sí, borrar todo y empezar de 0</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
