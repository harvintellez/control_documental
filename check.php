<?php
require 'conexion.php';
$stmt = $conexion->query('DESCRIBE trabajadores');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
