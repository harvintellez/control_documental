<?php
include 'seguridad.php';
include 'conexion.php';

// Solo administradores pueden acceder
if ($_SESSION['rol'] !== 'admin') {
    header("Location: panel.php?error=sin_permiso");
    exit();
}

$mensaje = "";
$error = "";

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
