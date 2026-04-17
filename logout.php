<?php
session_start();

// 1. Vaciar todas las variables de sesión
$_SESSION = [];

// 2. Destruir la cookie de sesión del lado del cliente
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destruir la sesión del lado del servidor
session_destroy();

header("Location: index.php");
exit();
?>