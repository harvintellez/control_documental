<?php
include 'seguridad.php';
include 'conexion.php';

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Top10_Embargos_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$sql = "SELECT codigo_trabajador, nombre_completo, MIN(cedula) AS cedula, COUNT(*) AS total_embargos " .
       "FROM trabajadores " .
       "WHERE inhabilitado = 0 " .
       "GROUP BY codigo_trabajador, nombre_completo " .
       "HAVING total_embargos > 1 " .
       "ORDER BY total_embargos DESC, nombre_completo ASC " .
       "LIMIT 10";

$stmt = $conexion->query($sql);
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table border="1">
    <thead>
        <tr style="background-color: #0d6efd; color: white;">
            <th colspan="5" style="font-size: 16px;">SISTEMA DE CONTROL DOCUMENTAL - TOP 10 TRABAJADORES CON MÁS DE 1 EMBARGO</th>
        </tr>
        <tr style="background-color: #f2f2f2;">
            <th colspan="5">Generado el: <?php echo date('d/m/Y H:i'); ?></th>
        </tr>
        <tr style="background-color: #333; color: white;">
            <th>#</th>
            <th>Código</th>
            <th>Nombre del Trabajador</th>
            <th>Cédula</th>
            <th>Embargos</th>
        </tr>
    </thead>
    <tbody>
        <?php $contador = 1; ?>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo $contador++; ?></td>
                <td><?php echo htmlspecialchars($row['codigo_trabajador']); ?></td>
                <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                <td><?php echo htmlspecialchars($row['total_embargos']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
