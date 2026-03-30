<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $password_ingresada = $_POST['password']; // No escapamos aquí porque la función verify lo maneja

    // Solo buscamos por el nombre de usuario
    $sql = "SELECT id, usuario, password, rol FROM usuarios WHERE usuario = '$usuario'";
    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) == 1) {
        $datos = mysqli_fetch_assoc($resultado);
        
        // Verificamos si la contraseña ingresada coincide con el Hash de la base de datos
        if (password_verify($password_ingresada, $datos['password'])) {
            $_SESSION['usuario_id'] = $datos['id'];
            $_SESSION['usuario_nombre'] = $datos['usuario'];
            $_SESSION['rol'] = $datos['rol'];

            header("Location: panel.php");
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: index.php?error=1");
        }
    } else {
        // Usuario no existe
        header("Location: index.php?error=1");
    }
}
?>