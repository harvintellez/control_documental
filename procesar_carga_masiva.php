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

$archivo_temp = $_POST['archivo_temp'] ?? '';
$separador = $_POST['separador'] ?? ',';
$filas_a_cargar = isset($_POST['filas_a_cargar']) ? $_POST['filas_a_cargar'] : [];

if (empty($archivo_temp) || empty($filas_a_cargar)) {
    header("Location: carga_masiva.php?error=No se seleccionaron registros para cargar.");
    exit;
}

$ruta_destino = "uploads/csv/" . basename($archivo_temp);

if (!file_exists($ruta_destino)) {
    header("Location: carga_masiva.php?error=El archivo temporal ha expirado o no existe.");
    exit;
}

$contador_insertados = 0;

try {
    $sql = "INSERT INTO trabajadores (codigo_trabajador, nombre_completo, cedula, tipo_documento, descripcion_oficio, valor_inicial, valor_final) 
            VALUES (:codigo, :nombre, :cedula, :tipo, :descripcion, :valor_inicial, :valor_final)";
    $stmt = $conexion->prepare($sql);

    if (($handle = fopen($ruta_destino, "r")) !== FALSE) {
        // Leer y descartar la primera línea (encabezados)
        fgetcsv($handle, 1000, $separador);
        
        $indice = 0;
        while (($datos = fgetcsv($handle, 1000, $separador)) !== FALSE) {
            if (count($datos) < 3 || empty(trim($datos[0]))) {
                continue; // Saltar vacías
            }

            // Solo procesar si el usuario seleccionó este índice en la vista previa
            if (in_array($indice, $filas_a_cargar)) {
                $codigo      = trim($datos[0]);
                $nombre      = trim($datos[1]);
                $cedula      = trim($datos[2]);
                $tipo        = trim($datos[3] ?? 'Otro');
                $descripcion = trim($datos[4] ?? '');
                $v_inicial   = !empty($datos[5]) ? floatval($datos[5]) : null;
                $v_final     = !empty($datos[6]) ? floatval($datos[6]) : null;

                $stmt->bindParam(':codigo',       $codigo);
                $stmt->bindParam(':nombre',       $nombre);
                $stmt->bindParam(':cedula',       $cedula);
                $stmt->bindParam(':tipo',         $tipo);
                $stmt->bindParam(':descripcion',  $descripcion);
                $stmt->bindParam(':valor_inicial',$v_inicial);
                $stmt->bindParam(':valor_final',  $v_final);

                if ($stmt->execute()) {
                    $contador_insertados++;
                }
            }
            $indice++;
        }
        fclose($handle);
    }
    
    // Eliminar archivo temporal
    @unlink($ruta_destino);

    header("Location: carga_masiva.php?res=ok&c=" . $contador_insertados);
    exit;

} catch (PDOException $e) {
    // Si falla, intentamos limpiar el archivo temporal de todos modos
    @unlink($ruta_destino);
    error_log("Error en carga masiva: " . $e->getMessage());
    header("Location: carga_masiva.php?error=Error de base de datos durante la carga masiva.");
    exit;
}
?>
