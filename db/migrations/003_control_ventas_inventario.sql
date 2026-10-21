-- Migration: Control de ventas (contado y credito), descuento de stock por sede, kardex, caja y gastos
-- Date: 2026-10-21
-- Estado: BORRADOR. Revisar antes de ejecutar.
-- Precondiciones verificadas en jm_distribuciones:
--   ventas, detalle_venta, movimientos_inventario y tipos_movimiento estan vacias.
--   inventario_sedes e inventario_esencias no tienen duplicados por (id_sede, id_presentacion).

-- 1. Tablas de venta vacias y mal definidas (tipos_movimiento tiene la PK "id_tipo_movimiento|")
DROP TABLE IF EXISTS detalle_venta;
DROP TABLE IF EXISTS movimientos_inventario;
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS tipos_movimiento;

-- 2. Stock por sede en unidades: VARCHAR -> INT, sin duplicados por sede/presentacion
UPDATE inventario_sedes SET stock_sede = '0' WHERE stock_sede IS NULL OR stock_sede = '';

ALTER TABLE inventario_sedes
    MODIFY stock_sede INT NOT NULL DEFAULT 0,
    ADD UNIQUE KEY uq_inventario_sede_presentacion (id_sede, id_presentacion);

-- 3. Stock de esencias en gramos: sin duplicados por sede/presentacion
ALTER TABLE inventario_esencias
    ADD UNIQUE KEY uq_inventario_esencia_sede_presentacion (id_sede, id_presentacion);

-- 4. Clientes: tipos correctos (cedula y telefono no caben en INT/11 de forma segura) y nombre de columna
ALTER TABLE cliente
    MODIFY identificacion_cliente VARCHAR(20) NULL,
    MODIFY telefono_cliente VARCHAR(20) NULL,
    CHANGE doreccion_cliente direccion_cliente VARCHAR(250) NULL;

-- 5. Compras: total numerico y sede en el encabezado (la compra es por sede)
--    Filas 1-6 se guardaron con separador de miles ('27.000'); se quita el punto antes de convertir.
UPDATE compras SET total_compra = REPLACE(total_compra, '.', '') WHERE total_compra LIKE '%.%';
UPDATE compras SET total_compra = '0' WHERE total_compra IS NULL OR total_compra = '';

ALTER TABLE compras
    MODIFY total_compra DECIMAL(12,2) NOT NULL DEFAULT 0,
    ADD COLUMN id_sede INT NULL AFTER id_proveedor,
    ADD CONSTRAINT fk_compras_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede);

UPDATE compras c
    JOIN (SELECT id_compra, MIN(id_sede) AS id_sede FROM detalle_compra GROUP BY id_compra) d
      ON d.id_compra = c.id_compras
    SET c.id_sede = d.id_sede;

-- Compras sin detalle no tienen sede que heredar: revisar antes de ejecutar esta linea
ALTER TABLE compras MODIFY id_sede INT NOT NULL;

-- 6. Catalogo de metodos de pago
INSERT INTO metodos_pago (nombre_metodo_pago, id_estado) VALUES
    ('Efectivo', 1),
    ('Transferencia', 1),
    ('Nequi', 1),
    ('Daviplata', 1),
    ('Tarjeta', 1);

-- 7. Tipos de movimiento de inventario (kardex)
CREATE TABLE tipos_movimiento (
    id_tipo_movimiento INT NOT NULL AUTO_INCREMENT,
    nombre_movimiento VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_tipo_movimiento),
    UNIQUE KEY uq_tipos_movimiento_nombre (nombre_movimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tipos_movimiento (nombre_movimiento) VALUES
    ('COMPRA'),
    ('VENTA'),
    ('ANULACION_VENTA'),
    ('AJUSTE');

-- 8. Precio de venta por sede (si no existe fila, se usa productos_presentaciones.precio_venta_presentacion)
CREATE TABLE precios_presentacion_sede (
    id_precio_presentacion_sede INT NOT NULL AUTO_INCREMENT,
    id_presentacion INT NOT NULL,
    id_sede INT NOT NULL,
    precio_venta DECIMAL(12,2) NOT NULL,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_precio_presentacion_sede),
    UNIQUE KEY uq_precio_presentacion_sede (id_presentacion, id_sede),
    CONSTRAINT fk_precio_sede_presentacion FOREIGN KEY (id_presentacion) REFERENCES productos_presentaciones (id_presentacion),
    CONSTRAINT fk_precio_sede_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. Receta: que componentes consume cada presentacion vendible.
--    gramo  -> se referencia el PRODUCTO de la esencia; los gramos se descuentan por FIFO de sus lotes
--              (inventario_esencias de cualquier presentacion del producto).
--    unidad -> se referencia la PRESENTACION del envase (inventario_sedes).
--    El costo de la presentacion = SUM(cantidad × costo unitario del componente).
--    Toda presentacion de tipo 3 (preparado) debe tener receta; la venta se bloquea si no la tiene.
CREATE TABLE receta_presentacion (
    id_receta INT NOT NULL AUTO_INCREMENT,
    id_presentacion INT NOT NULL,
    unidad_componente ENUM('unidad', 'gramo') NOT NULL,
    id_producto_componente INT NULL,
    id_presentacion_componente INT NULL,
    cantidad_componente DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id_receta),
    UNIQUE KEY uq_receta_componente (id_presentacion, unidad_componente, id_producto_componente, id_presentacion_componente),
    CONSTRAINT chk_receta_componente CHECK (
        (unidad_componente = 'gramo' AND id_producto_componente IS NOT NULL AND id_presentacion_componente IS NULL)
        OR
        (unidad_componente = 'unidad' AND id_presentacion_componente IS NOT NULL AND id_producto_componente IS NULL)
    ),
    CONSTRAINT fk_receta_presentacion FOREIGN KEY (id_presentacion) REFERENCES productos_presentaciones (id_presentacion),
    CONSTRAINT fk_receta_producto_componente FOREIGN KEY (id_producto_componente) REFERENCES productos (id_producto),
    CONSTRAINT fk_receta_presentacion_componente FOREIGN KEY (id_presentacion_componente) REFERENCES productos_presentaciones (id_presentacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Semilla: lociones 212 MEN 30ml (presentaciones 4 y 6) con esencia 212 MEN (producto 2) y envase BALAS 30ml (presentacion 3)
INSERT INTO receta_presentacion (id_presentacion, unidad_componente, id_producto_componente, id_presentacion_componente, cantidad_componente) VALUES
    (4, 'gramo', 2, NULL, 15),
    (4, 'unidad', NULL, 3, 1),
    (6, 'gramo', 2, NULL, 17),
    (6, 'unidad', NULL, 3, 1);

-- 10. Ventas (encabezado). estado_venta se recalcula con los pagos: pendiente hasta cubrir el total.
CREATE TABLE ventas (
    id_venta INT NOT NULL AUTO_INCREMENT,
    id_sede INT NOT NULL,
    numero_venta VARCHAR(30) NOT NULL,
    id_cliente INT NULL,
    id_usuario INT NOT NULL,
    subtotal_venta DECIMAL(12,2) NOT NULL DEFAULT 0,
    descuento_venta DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_venta DECIMAL(12,2) NOT NULL DEFAULT 0,
    costo_total_venta DECIMAL(12,2) NOT NULL DEFAULT 0,
    estado_venta ENUM('pendiente', 'pagada', 'anulada') NOT NULL DEFAULT 'pendiente',
    fecha_venta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacion_venta VARCHAR(255) NULL,
    PRIMARY KEY (id_venta),
    UNIQUE KEY uq_ventas_sede_numero (id_sede, numero_venta),
    KEY idx_ventas_fecha (fecha_venta),
    CONSTRAINT fk_ventas_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede),
    CONSTRAINT fk_ventas_cliente FOREIGN KEY (id_cliente) REFERENCES cliente (id_cliente),
    CONSTRAINT fk_ventas_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 11. Ventas (detalle) con costo congelado al momento de vender
CREATE TABLE detalle_venta (
    id_detalle_venta INT NOT NULL AUTO_INCREMENT,
    id_venta INT NOT NULL,
    id_presentacion INT NOT NULL,
    cantidad_detalle_venta INT NOT NULL,
    precio_unitario_detalle_venta DECIMAL(12,2) NOT NULL,
    subtotal_detalle_venta DECIMAL(12,2) NOT NULL,
    costo_unitario_detalle_venta DECIMAL(12,2) NOT NULL DEFAULT 0,
    costo_total_detalle_venta DECIMAL(12,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (id_detalle_venta),
    KEY idx_detalle_venta_venta (id_venta),
    KEY idx_detalle_venta_presentacion (id_presentacion),
    CONSTRAINT fk_detalle_venta_venta FOREIGN KEY (id_venta) REFERENCES ventas (id_venta),
    CONSTRAINT fk_detalle_venta_presentacion FOREIGN KEY (id_presentacion) REFERENCES productos_presentaciones (id_presentacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 12. Pagos de una venta (contado = un pago; credito = abonos sucesivos)
CREATE TABLE pagos_venta (
    id_pago_venta INT NOT NULL AUTO_INCREMENT,
    id_venta INT NOT NULL,
    id_metodo_pago INT NOT NULL,
    monto_pago DECIMAL(12,2) NOT NULL,
    id_usuario INT NOT NULL,
    fecha_pago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_pago_venta),
    KEY idx_pagos_venta_venta (id_venta),
    CONSTRAINT fk_pagos_venta_venta FOREIGN KEY (id_venta) REFERENCES ventas (id_venta),
    CONSTRAINT fk_pagos_venta_metodo FOREIGN KEY (id_metodo_pago) REFERENCES metodos_pago (id_metodo_pago),
    CONSTRAINT fk_pagos_venta_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 13. Gastos por sede (arriendo, transporte, servicios, etc.)
CREATE TABLE gastos (
    id_gasto INT NOT NULL AUTO_INCREMENT,
    id_sede INT NOT NULL,
    categoria_gasto VARCHAR(100) NOT NULL,
    descripcion_gasto VARCHAR(255) NULL,
    monto_gasto DECIMAL(12,2) NOT NULL,
    id_metodo_pago INT NULL,
    id_usuario INT NOT NULL,
    fecha_gasto DATE NOT NULL DEFAULT (CURRENT_DATE),
    PRIMARY KEY (id_gasto),
    KEY idx_gastos_sede_fecha (id_sede, fecha_gasto),
    CONSTRAINT fk_gastos_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede),
    CONSTRAINT fk_gastos_metodo FOREIGN KEY (id_metodo_pago) REFERENCES metodos_pago (id_metodo_pago),
    CONSTRAINT fk_gastos_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 14. Kardex: cada entrada/salida de stock (compra, venta, anulacion, ajuste).
--     Una venta de loción genera una fila por componente (esencia en gramos y envase en unidades).
CREATE TABLE movimientos_inventario (
    id_movimientos_inventario INT NOT NULL AUTO_INCREMENT,
    id_sede INT NOT NULL,
    id_presentacion INT NOT NULL,
    id_tipo_movimiento INT NOT NULL,
    unidad_movimiento ENUM('unidad', 'gramo') NOT NULL,
    cantidad_movimiento DECIMAL(10,2) NOT NULL,
    saldo_resultante DECIMAL(10,2) NOT NULL,
    id_compra INT NULL,
    id_venta INT NULL,
    id_usuario INT NULL,
    observacion_movimiento VARCHAR(255) NULL,
    fecha_movimiento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_movimientos_inventario),
    KEY idx_movimientos_sede_presentacion (id_sede, id_presentacion),
    KEY idx_movimientos_fecha (fecha_movimiento),
    CONSTRAINT fk_movimientos_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede),
    CONSTRAINT fk_movimientos_presentacion FOREIGN KEY (id_presentacion) REFERENCES productos_presentaciones (id_presentacion),
    CONSTRAINT fk_movimientos_tipo FOREIGN KEY (id_tipo_movimiento) REFERENCES tipos_movimiento (id_tipo_movimiento),
    CONSTRAINT fk_movimientos_compra FOREIGN KEY (id_compra) REFERENCES compras (id_compras),
    CONSTRAINT fk_movimientos_venta FOREIGN KEY (id_venta) REFERENCES ventas (id_venta),
    CONSTRAINT fk_movimientos_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 15. Caja por sede: ingresos (pagos de ventas) y egresos (compras, gastos, anulaciones).
--     Saldo disponible para comprar = SUM(ingresos) - SUM(egresos) por sede.
CREATE TABLE caja_movimientos (
    id_caja_movimiento INT NOT NULL AUTO_INCREMENT,
    id_sede INT NOT NULL,
    tipo_caja ENUM('ingreso', 'egreso') NOT NULL,
    concepto_caja ENUM('pago_venta', 'anulacion_venta', 'compra', 'gasto', 'otro') NOT NULL,
    monto_caja DECIMAL(12,2) NOT NULL,
    id_metodo_pago INT NULL,
    id_pago_venta INT NULL,
    id_compra INT NULL,
    id_gasto INT NULL,
    id_usuario INT NULL,
    observacion_caja VARCHAR(255) NULL,
    fecha_caja DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_caja_movimiento),
    KEY idx_caja_sede_fecha (id_sede, fecha_caja),
    CONSTRAINT fk_caja_sede FOREIGN KEY (id_sede) REFERENCES sedes (id_sede),
    CONSTRAINT fk_caja_metodo_pago FOREIGN KEY (id_metodo_pago) REFERENCES metodos_pago (id_metodo_pago),
    CONSTRAINT fk_caja_pago_venta FOREIGN KEY (id_pago_venta) REFERENCES pagos_venta (id_pago_venta),
    CONSTRAINT fk_caja_compra FOREIGN KEY (id_compra) REFERENCES compras (id_compras),
    CONSTRAINT fk_caja_gasto FOREIGN KEY (id_gasto) REFERENCES gastos (id_gasto),
    CONSTRAINT fk_caja_usuario FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Pendiente (no incluido aqui):
--   * Migracion 002_add_cantidad_gramos_presentacion.sql no esta aplicada en la base, pero ComprasModel la
--     referencia. Se necesita para convertir cantidad de esencia a gramos.
--   * La receta de las lociones 50ml y 100ml se crea desde la app (no hay presentaciones de loción aun).
