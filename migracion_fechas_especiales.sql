-- Migración: Fechas especiales para condiciones de Embargo
-- Ejecutar en la BD: control_documental

ALTER TABLE trabajadores
  ADD COLUMN fecha_especial_1 DATE NULL,
  ADD COLUMN fecha_especial_2 DATE NULL;

