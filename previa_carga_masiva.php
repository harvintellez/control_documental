<?php
include 'seguridad.php';
if ($_SESSION['rol'] !== 'admin') {
    header('Location: panel.php');
    exit;
}
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: carga_masiva.php');
    exit;
}

$dir_csv = "uploads/csv/";
if (!file_exists($dir_csv)) {
    mkdir($dir_csv, 0755, true);
}

if (!isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
    header("Location: carga_masiva.php?error=Error al subir el archivo.");
    exit;
}

// Validar tamaño (5MB)
if ($_FILES['archivo_csv']['size'] > 5 * 1024 * 1024) {
    header("Location: carga_masiva.php?error=El archivo supera el tamaño máximo de 5MB.");
    exit;
}

// Validar extensión
$ext = strtolower(pathinfo($_FILES['archivo_csv']['name'], PATHINFO_EXTENSION));
if ($ext !== 'csv') {
    header("Location: carga_masiva.php?error=Solo se permiten archivos CSV.");
    exit;
}

$tmp_name = $_FILES['archivo_csv']['tmp_name'];
$nuevo_nombre = "carga_" . time() . "_" . uniqid() . ".csv";
$ruta_destino = $dir_csv . $nuevo_nombre;

if (!move_uploaded_file($tmp_name, $ruta_destino)) {
    header("Location: carga_masiva.php?error=Error al guardar el archivo temporalmente.");
    exit;
}

// Leer CSV
$filas = [];
if (($handle = fopen($ruta_destino, "r")) !== FALSE) {
    // Detectar separador (coma o punto y coma)
    $primera_linea = fgets($handle);
    $separador = strpos($primera_linea, ';') !== false ? ';' : ',';
    rewind($handle);
    
    $encabezados = fgetcsv($handle, 1000, $separador); // Leer encabezados
    
    // Opcional: limpiar BOM si existe
    if(isset($encabezados[0])) {
        $encabezados[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $encabezados[0]);
    }

    while (($datos = fgetcsv($handle, 1000, $separador)) !== FALSE) {
        if(count($datos) < 3 || empty(trim($datos[0]))) continue; // Saltar filas vacías
        $filas[] = [
            'codigo_trabajador'  => trim($datos[0] ?? ''),
            'nombre_completo'    => trim($datos[1] ?? ''),
            'cedula'             => trim($datos[2] ?? ''),
            'tipo_documento'     => trim($datos[3] ?? 'Otro'),
            'descripcion_oficio' => trim($datos[4] ?? ''),
            'valor_inicial'      => !empty($datos[5]) ? floatval($datos[5]) : null,
            'valor_final'        => !empty($datos[6]) ? floatval($datos[6]) : null
        ];
    }
    fclose($handle);
} else {
    header("Location: carga_masiva.php?error=No se pudo leer el archivo CSV.");
    exit;
}

if (empty($filas)) {
    header("Location: carga_masiva.php?error=El archivo está vacío o tiene formato incorrecto.");
    exit;
}

// Verificar duplicados en base de datos
$stmt_check = $conexion->prepare("SELECT COUNT(*) FROM trabajadores WHERE codigo_trabajador = :codigo OR cedula = :cedula");

include 'includes/header.php';
?>

<div class="row mt-4 mb-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark py-3">
                <h4 class="mb-0"><i class="bi bi-eye me-2"></i>Vista Previa de Carga Masiva</h4>
            </div>
            <div class="card-body p-0">
                
                <div class="alert alert-warning m-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Se encontraron <strong><?php echo count($filas); ?></strong> registros en el archivo.
                    Revise la tabla a continuación. Los registros marcados como <strong>"Posible Duplicado"</strong> ya tienen un embargo registrado en el sistema bajo ese Código o Cédula. 
                    Debe seleccionar explícitamente la casilla si desea forzar su carga.
                </div>

                <form action="procesar_carga_masiva.php" method="POST">
                    <input type="hidden" name="archivo_temp" value="<?php echo htmlspecialchars($nuevo_nombre); ?>">
                    <input type="hidden" name="separador" value="<?php echo htmlspecialchars($separador); ?>">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <input class="form-check-input" type="checkbox" id="checkAll" checked title="Seleccionar/Deseleccionar todos (excepto duplicados)">
                                    </th>
                                    <th>Estado</th>
                                    <th>Código</th>
                                    <th>Nombre Completo</th>
                                    <th>Cédula</th>
                                    <th>Tipo Documento</th>
                                    <th>Valores</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filas as $indice => $fila): ?>
                                    <?php 
                                    // Comprobar duplicado
                                    $stmt_check->bindParam(':codigo', $fila['codigo_trabajador']);
                                    $stmt_check->bindParam(':cedula', $fila['cedula']);
                                    $stmt_check->execute();
                                    $es_duplicado = $stmt_check->fetchColumn() > 0;
                                    ?>
                                    <tr class="<?php echo $es_duplicado ? 'table-warning' : ''; ?>">
                                        <td class="text-center">
                                            <input class="form-check-input check-row" type="checkbox" name="filas_a_cargar[]" value="<?php echo $indice; ?>" <?php echo $es_duplicado ? '' : 'checked'; ?>>
                                        </td>
                                        <td>
                                            <?php if ($es_duplicado): ?>
                                                <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Posible Duplicado</span>
                                            <?php else: ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Nuevo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($fila['codigo_trabajador']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($fila['nombre_completo']); ?></td>
                                        <td><?php echo htmlspecialchars($fila['cedula']); ?></td>
                                        <td><?php echo htmlspecialchars($fila['tipo_documento']); ?></td>
                                        <td>
                                            <small class="text-muted">
                                            I: <?php echo $fila['valor_inicial'] ? '$'.$fila['valor_inicial'] : '-'; ?><br>
                                            F: <?php echo $fila['valor_final'] ? '$'.$fila['valor_final'] : '-'; ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center p-3">
                        <a href="carga_masiva.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancelar y Volver
                        </a>
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="bi bi-upload me-2"></i>Procesar Registros Seleccionados
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    // Script para el checkbox de seleccionar todos (excepto los duplicados por seguridad)
    document.getElementById('checkAll').addEventListener('change', function() {
        const isChecked = this.checked;
        const checkboxes = document.querySelectorAll('.check-row');
        checkboxes.forEach(cb => {
            // Si el padre de la fila tiene la clase table-warning, es duplicado
            const isDuplicate = cb.closest('tr').classList.contains('table-warning');
            
            // Solo auto-chequeamos los NO duplicados. Los duplicados se deben chequear manual.
            if (!isDuplicate || !isChecked) {
                cb.checked = isChecked;
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
