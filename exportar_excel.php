<?php
include 'seguridad.php';
include 'conexion.php';

// 1. Capturar los mismos filtros que el reporte
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date("Y-m-01");
$fecha_fin    = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date("Y-m-d");
$tipo_filtro  = isset($_GET['tipo_documento']) ? $_GET['tipo_documento'] : 'Todos';

// 2. Configurar cabeceras para descargar el archivo Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_SCD_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// 3. Construir la consulta SQL
$sql = "SELECT * FROM trabajadores WHERE DATE(fecha_registro) BETWEEN :fecha_inicio AND :fecha_fin";
if ($tipo_filtro != 'Todos') {
    $sql .= " AND tipo_documento = :tipo_filtro";
}
$sql .= " ORDER BY fecha_registro DESC";

$stmt = $conexion->prepare($sql);
$stmt->bindParam(':fecha_inicio', $fecha_inicio);
$stmt->bindParam(':fecha_fin', $fecha_fin);
if ($tipo_filtro != 'Todos') {
    $stmt->bindParam(':tipo_filtro', $tipo_filtro);
}
$stmt->execute();

// 4. Crear la estructura de la tabla para Excel
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table border="1">
    <thead>
        <tr style="background-color: #0d6efd; color: white;">
            <th colspan="5" style="font-size: 16px;">SISTEMA DE CONTROL DOCUMENTAL - REPORTE DE OFICIOS</th>
        </tr>
        <tr style="background-color: #f2f2f2;">
            <th colspan="5">Periodo: <?php echo htmlspecialchars($fecha_inicio); ?> al <?php echo htmlspecialchars($fecha_fin); ?> | Filtro: <?php echo htmlspecialchars($tipo_filtro); ?></th>
        </tr>
        <tr style="background-color: #333; color: white;">
            <th>Fecha Registro</th>
            <th>Código</th>
            <th>Nombre del Trabajador</th>
            <th>Cédula</th>
            <th>Tipo de Oficio</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td><?php echo date("d/m/Y", strtotime($row['fecha_registro'])); ?></td>
            <td><?php echo htmlspecialchars($row['codigo_trabajador']); ?></td>
            <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
            <td><?php echo htmlspecialchars($row['cedula']); ?></td>
            <td><?php echo htmlspecialchars($row['tipo_documento']); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>