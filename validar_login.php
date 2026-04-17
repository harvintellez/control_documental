<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password_ingresada = $_POST['password'];

    // Consulta preparada con PDO
    $stmt = $conexion->prepare("SELECT id, usuario, password, rol FROM usuarios WHERE usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        
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