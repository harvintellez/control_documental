-- Migración: Indicación/condición asociada a fechas especiales
-- Ejecutar en la BD: control_documental

ALTER TABLE trabajadores
  ADD COLUMN condicion_especial_1 TEXT NULL,
  ADD COLUMN condicion_especial_2 TEXT NULL;

