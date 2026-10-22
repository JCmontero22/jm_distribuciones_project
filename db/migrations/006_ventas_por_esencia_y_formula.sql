-- Migration: Ventas por esencia + formula (lociones calculadas, no presentaciones)
-- Date: 2026-10-21
-- Precondicion: 005 aplicada.
--
-- Modelo:
--   Esencia (productos de la categoria Esencias) + formula (formulas) = loción vendible.
--   Stock de la loción = lo que alcanza con la esencia en lotes y el envase en inventario (mismo calculo del informe).
--   Precio por esencia, formula y sede: precios_locion_sede.
--   Reventa (id_tipo_producto = 1): sigue como presentación normal.
--
-- Se quitan los vínculos de presentación (id_producto_esencia, id_formula), la receta anterior y el precio por presentación.

CREATE TABLE precios_locion_sede (
    id_precio_locion INT NOT NULL AUTO_INCREMENT,
    id_producto_esencia INT NOT NULL,
    id_formula INT NOT NULL,
    id_sede INT NOT NULL,
    precio_venta DECIMAL(12,2) NOT NULL,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_precio_locion),
    UNIQUE KEY uq_precio_locion (id_producto_esencia, id_formula, id_sede),
    CONSTRAINT fk_precio_locion_esencia FOREIGN KEY (id_producto_esencia) REFERENCES productos (id_producto),
    CONSTRAINT fk_precio_locion_formula FOREIGN KEY (id_formula) REFERENCES formulas (id_formula),
    CONSTRAINT fk_precio_locion_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Detalle de venta: una linea es una presentacion de reventa o una loción (esencia + formula)
ALTER TABLE detalle_venta
    MODIFY id_presentacion INT NULL,
    ADD COLUMN id_producto_esencia INT NULL AFTER id_presentacion,
    ADD COLUMN id_formula INT NULL AFTER id_producto_esencia,
    ADD CONSTRAINT fk_detalle_venta_esencia FOREIGN KEY (id_producto_esencia) REFERENCES productos (id_producto),
    ADD CONSTRAINT fk_detalle_venta_formula FOREIGN KEY (id_formula) REFERENCES formulas (id_formula);

-- Vinculos de presentacion ya no se usan
ALTER TABLE productos_presentaciones
    DROP FOREIGN KEY fk_presentacion_esencia,
    DROP FOREIGN KEY fk_presentacion_formula,
    DROP COLUMN id_producto_esencia,
    DROP COLUMN id_formula;

DROP TABLE IF EXISTS receta_presentacion;
DROP TABLE IF EXISTS precios_presentacion_sede;
