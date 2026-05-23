-- Migration: Add id_sede column to detalle_compra table
-- Date: 2026-05-23
-- Purpose: Track which branch (sede) the purchase detail belongs to

ALTER TABLE detalle_compra
ADD COLUMN id_sede INT NOT NULL
AFTER id_compra,
ADD CONSTRAINT fk_detalle_compra_sede
FOREIGN KEY (id_sede) REFERENCES sede(id_sede);

-- Add index for better query performance
CREATE INDEX idx_detalle_compra_sede ON detalle_compra(id_sede);
