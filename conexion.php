<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'control_documental';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    function asegurar_esquema_trabajadores(PDO $conexion): void
    {
        $tabla = $conexion->query("SHOW TABLES LIKE 'trabajadores'")->fetch();
        if (!$tabla) {
            throw new RuntimeException('La tabla trabajadores no existe en la base de datos.');
        }

        $columnas = $conexion->query("SHOW COLUMNS FROM trabajadores")->fetchAll(PDO::FETCH_COLUMN);
        $existentes = array_flip($columnas);

        $migraciones = [
            'fecha_especial_1' => "ALTER TABLE trabajadores ADD COLUMN fecha_especial_1 DATE NULL",
            'fecha_especial_2' => "ALTER TABLE trabajadores ADD COLUMN fecha_especial_2 DATE NULL",
            'condicion_especial_1' => "ALTER TABLE trabajadores ADD COLUMN condicion_especial_1 TEXT NULL",
            'condicion_especial_2' => "ALTER TABLE trabajadores ADD COLUMN condicion_especial_2 TEXT NULL",
            'inhabilitado' => "ALTER TABLE trabajadores ADD COLUMN inhabilitado TINYINT(1) NOT NULL DEFAULT 0",
            'fecha_inhabilitacion' => "ALTER TABLE trabajadores ADD COLUMN fecha_inhabilitacion DATE NULL",
            'motivo_inhabilitacion' => "ALTER TABLE trabajadores ADD COLUMN motivo_inhabilitacion TEXT NULL",
            'doc_inhabilitacion' => "ALTER TABLE trabajadores ADD COLUMN doc_inhabilitacion VARCHAR(255) NULL",
            'usuario_inhabilito' => "ALTER TABLE trabajadores ADD COLUMN usuario_inhabilito VARCHAR(50) NULL",
            'valor_inicial' => "ALTER TABLE trabajadores ADD COLUMN valor_inicial DECIMAL(10,2) DEFAULT NULL",
            'valor_final' => "ALTER TABLE trabajadores ADD COLUMN valor_final DECIMAL(10,2) DEFAULT NULL",
            'banco_institucion' => "ALTER TABLE trabajadores ADD COLUMN banco_institucion VARCHAR(255) NULL"
        ];

        foreach ($migraciones as $columna => $sql) {
            if (!isset($existentes[$columna])) {
                $conexion->exec($sql);
            }
        }
    }

    function asegurar_tabla_bancos(PDO $conexion): void
    {
        $conexion->exec(
            "CREATE TABLE IF NOT EXISTS configuracion_bancos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(200) NOT NULL,
                activo TINYINT(1) NOT NULL DEFAULT 1,
                fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_nombre_banco (nombre)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $bancos_iniciales = ['Lafise Bancentro', 'BAC', 'BANPRO'];
        foreach ($bancos_iniciales as $nombre) {
            $stmt = $conexion->prepare("INSERT IGNORE INTO configuracion_bancos (nombre, activo) VALUES (:nombre, 1)");
            $stmt->execute([':nombre' => $nombre]);
        }
    }

    asegurar_esquema_trabajadores($conexion);
    asegurar_tabla_bancos($conexion);
} catch (PDOException $e) {
    error_log("Error de conexión BD: " . $e->getMessage());
    die("Error crítico: no se pudo conectar al servidor. Contacte al administrador.");
} catch (RuntimeException $e) {
    error_log("Error de esquema BD: " . $e->getMessage());
    die("Error crítico: la base de datos no tiene la estructura esperada. Contacte al administrador.");
}

define('URL_BASE', getenv('APP_URL') ?: 'http://localhost/control_documental/');
?>
