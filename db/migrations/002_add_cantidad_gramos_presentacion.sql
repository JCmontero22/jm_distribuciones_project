-- Migration: Add cantidad_gramos_presentacion to productos_presentaciones
-- Date: 2026-05-24
-- Purpose: Store the size of each presentation in grams for proper essence inventory calculation
-- Example: Presentation "212 MEN - 125G" needs cantidad_gramos_presentacion = 125

ALTER TABLE `productos_presentaciones`
ADD COLUMN `cantidad_gramos_presentacion` INT DEFAULT NULL AFTER `nombre_presentacion`,
ADD INDEX `idx_cantidad_gramos_presentacion` (`cantidad_gramos_presentacion`);
