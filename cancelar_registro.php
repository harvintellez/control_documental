<?php
include 'seguridad.php';

if (isset($_SESSION['archivos_temp'])) {
    if (!empty($_SESSION['archivos_temp']['foto']) && file_exists($_SESSION['archivos_temp']['foto'])) {
        unlink($_SESSION['archivos_temp']['foto']);
    }
    if (!empty($_SESSION['archivos_temp']['doc']) && file_exists($_SESSION['archivos_temp']['doc'])) {
        unlink($_SESSION['archivos_temp']['doc']);
    }
}

unset($_SESSION['registro_duplicado'], $_SESSION['archivos_temp']);
header('Location: registro.php');
exit();
?>