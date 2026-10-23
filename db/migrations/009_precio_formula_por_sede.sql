-- Migration: Precio por formula y sede (aplica a todas las esencias sin precio propio)
-- Date: 2026-10-23
-- Precondicion: 006 aplicada.
--
-- Modelo:
--   Precio de una loción = precio propio de la esencia + formula + sede (precios_locion_sede)
--                          o, si no tiene, el precio de la formula en la sede (precios_formula_sede).
--   precios_locion_sede se conserva: sirve para excepciones por esencia.

CREATE TABLE precios_formula_sede (
    id_precio_formula INT NOT NULL AUTO_INCREMENT,
    id_formula INT NOT NULL,
    id_sede INT NOT NULL,
    precio_venta DECIMAL(12,2) NOT NULL,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_precio_formula),
    UNIQUE KEY uq_precio_formula_sede (id_formula, id_sede),
    CONSTRAINT fk_precio_formula_formula FOREIGN KEY (id_formula) REFERENCES formulas (id_formula),
    CONSTRAINT fk_precio_formula_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
