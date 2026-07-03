<?php
function asegurar_tabla_auditoria(PDO $conexion): void
{
    $conexion->exec(
        "CREATE TABLE IF NOT EXISTS trabajadores_auditoria (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trabajador_id INT NOT NULL,
            accion VARCHAR(50) NOT NULL,
            campo VARCHAR(100) DEFAULT NULL,
            valor_anterior TEXT DEFAULT NULL,
            valor_nuevo TEXT DEFAULT NULL,
            usuario VARCHAR(255) DEFAULT NULL,
            fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ip VARCHAR(45) DEFAULT NULL,
            detalles TEXT DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

function normalizar_valor_auditoria($valor): string
{
    if ($valor === null) {
        return '';
    }

    if (is_array($valor)) {
        return json_encode($valor, JSON_UNESCAPED_UNICODE);
    }

    if (is_bool($valor)) {
        return $valor ? '1' : '0';
    }

    return (string) $valor;
}

function registrar_auditoria(PDO $conexion, int $trabajador_id, string $accion, $usuario = null, $campo = null, $valor_anterior = null, $valor_nuevo = null, $detalles = null): void
{
    asegurar_tabla_auditoria($conexion);

    $stmt = $conexion->prepare(
        "INSERT INTO trabajadores_auditoria (trabajador_id, accion, campo, valor_anterior, valor_nuevo, usuario, ip, detalles)
         VALUES (:trabajador_id, :accion, :campo, :valor_anterior, :valor_nuevo, :usuario, :ip, :detalles)"
    );

    $stmt->execute([
        ':trabajador_id' => $trabajador_id,
        ':accion' => $accion,
        ':campo' => $campo,
        ':valor_anterior' => normalizar_valor_auditoria($valor_anterior),
        ':valor_nuevo' => normalizar_valor_auditoria($valor_nuevo),
        ':usuario' => $usuario,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ':detalles' => is_array($detalles) ? json_encode($detalles, JSON_UNESCAPED_UNICODE) : $detalles,
    ]);
}
?>
