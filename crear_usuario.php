<?php
include 'conexion.php';

$user = 'usuario';
$pass_plano = 'usuario'; 

// Encriptamos la contraseña
$pass_encriptada = password_hash($pass_plano, PASSWORD_DEFAULT);
$rol  = 'admin';

// Limpiamos la tabla antes (opcional, solo para esta prueba)
mysqli_query($conexion, "DELETE FROM usuarios WHERE usuario = '$user'");

$sql = "INSERT INTO usuarios (usuario, password, rol) VALUES ('$user', '$pass_encriptada', '$rol')";

if (mysqli_query($conexion, $sql)) {
    echo "✅ Usuario '$user' creado con contraseña encriptada.";
    echo "<br>Hash generado: " . $pass_encriptada;
}
?>