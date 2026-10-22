-- Migration: Lotes de esencia (FIFO real) y lote en el kardex
-- Date: 2026-10-21
-- Precondicion: 003_control_ventas_inventario.sql aplicada.
-- Cada compra de esencia crea un lote con su costo por gramo. Las ventas consumen lotes
-- del mas antiguo al mas nuevo (fecha_ingreso) y el costo de la venta sale de los lotes consumidos.
-- inventario_esencias se mantiene como saldo total por sede y presentacion.

CREATE TABLE lotes_esencia (
    id_lote INT NOT NULL AUTO_INCREMENT,
    id_sede INT NOT NULL,
    id_presentacion INT NOT NULL,
    id_compra INT NULL,
    fecha_ingreso DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    gramos_iniciales DECIMAL(10,2) NOT NULL,
    gramos_disponibles DECIMAL(10,2) NOT NULL,
    costo_por_gramo DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_lote),
    KEY idx_lotes_sede_presentacion (id_sede, id_presentacion),
    KEY idx_lotes_fifo (id_sede, fecha_ingreso),
    CONSTRAINT fk_lotes_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede),
    CONSTRAINT fk_lotes_presentacion FOREIGN KEY (id_presentacion) REFERENCES productos_presentaciones (id_presentacion),
    CONSTRAINT fk_lotes_compra FOREIGN KEY (id_compra) REFERENCES compras (id_compras)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Saldo inicial: un lote por cada saldo existente. La fecha es aproximada (fecha_actualizacion del saldo).
INSERT INTO lotes_esencia (id_sede, id_presentacion, fecha_ingreso, gramos_iniciales, gramos_disponibles, costo_por_gramo)
SELECT id_sede, id_presentacion, fecha_actualizacion, cantidad_gramos, cantidad_gramos, costo_por_gramo
FROM inventario_esencias
WHERE cantidad_gramos > 0;

-- El kardex indica de que lote salieron los gramos (para revertirlos exactamente al anular)
ALTER TABLE movimientos_inventario
    ADD COLUMN id_lote_esencia INT NULL AFTER id_venta,
    ADD CONSTRAINT fk_movimientos_lote FOREIGN KEY (id_lote_esencia) REFERENCES lotes_esencia (id_lote);
