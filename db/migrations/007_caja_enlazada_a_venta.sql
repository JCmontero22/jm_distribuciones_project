-- Migration: Caja enlazada a la venta (pagos y anulaciones)
-- Date: 2026-10-22
-- Precondicion: 003 aplicada.
-- Permite saber a que venta pertenece cada movimiento de caja (ingresos por pago y egresos por anulacion).

ALTER TABLE caja_movimientos
    ADD COLUMN id_venta INT NULL AFTER id_pago_venta,
    ADD CONSTRAINT fk_caja_venta FOREIGN KEY (id_venta) REFERENCES ventas (id_venta);
