-- =========================================================
-- MIGRACIÓN: Módulo de Inhabilitación de Embargos
-- Ejecutar en phpMyAdmin sobre la BD: control_documental
-- =========================================================

-- 1. Eliminar UNIQUE en codigo_trabajador (permite múltiples embargos por trabajador)
ALTER TABLE `trabajadores` DROP INDEX `codigo_trabajador`;

-- 2. Agregar campos de inhabilitación
ALTER TABLE `trabajadores`
    ADD COLUMN `inhabilitado`          TINYINT(1)   NOT NULL DEFAULT 0  AFTER `fecha_registro`,
    ADD COLUMN `fecha_inhabilitacion`  DATE         NULL                AFTER `inhabilitado`,
    ADD COLUMN `motivo_inhabilitacion` TEXT         NULL                AFTER `fecha_inhabilitacion`,
    ADD COLUMN `doc_inhabilitacion`    VARCHAR(255) NULL                AFTER `motivo_inhabilitacion`,
    ADD COLUMN `usuario_inhabilito`    VARCHAR(50)  NULL                AFTER `doc_inhabilitacion`;

-- 3. Agregar campos de valores monetarios si aún no existen
ALTER TABLE `trabajadores`
    ADD COLUMN IF NOT EXISTS `valor_inicial` DECIMAL(10,2) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `valor_final`   DECIMAL(10,2) DEFAULT NULL;
