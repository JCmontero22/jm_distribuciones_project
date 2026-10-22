-- Limpieza de datos de negocio de jm_distribuciones
-- Date: 2026-10-21
-- Conserva: usuario, perfil_usuario, perfil_usuario_permisos, permisos, sedes, estado,
--           metodos_pago, tipos_movimiento, tipo_producto, categoria_producto,
--           marca_producto, genero, tipo_concentracion.
-- Respaldo previo recomendado:
--   mysqldump -uroot --single-transaction jm_distribuciones > respaldo.sql
-- Ejecutar:
--   /opt/lampp/bin/mysql -uroot -h localhost jm_distribuciones < db/limpieza_datos_negocio.sql

SET FOREIGN_KEY_CHECKS = 0;

-- Ventas y caja
TRUNCATE TABLE detalle_venta;
TRUNCATE TABLE pagos_venta;
TRUNCATE TABLE caja_movimientos;
TRUNCATE TABLE movimientos_inventario;
TRUNCATE TABLE ventas;
TRUNCATE TABLE gastos;

-- Inventario y lotes
TRUNCATE TABLE lotes_esencia;
TRUNCATE TABLE inventario_sedes;
TRUNCATE TABLE inventario_esencias;

-- Compras y terceros
TRUNCATE TABLE detalle_compra;
TRUNCATE TABLE compras;
TRUNCATE TABLE proveedor;
TRUNCATE TABLE cliente;

-- Productos, recetas y fórmulas
TRUNCATE TABLE precios_presentacion_sede;
TRUNCATE TABLE receta_presentacion;
TRUNCATE TABLE productos_presentaciones;
TRUNCATE TABLE productos;
TRUNCATE TABLE formulas;
TRUNCATE TABLE insumo_formulas;

-- Promociones
TRUNCATE TABLE descuentos;
TRUNCATE TABLE banner_promociones;

SET FOREIGN_KEY_CHECKS = 1;

-- Verificación: conteos esperados 0 en las tablas vaciadas
SELECT 'usuario' AS tabla, COUNT(*) AS filas FROM usuario
UNION ALL SELECT 'sedes', COUNT(*) FROM sedes
UNION ALL SELECT 'productos', COUNT(*) FROM productos
UNION ALL SELECT 'ventas', COUNT(*) FROM ventas
UNION ALL SELECT 'inventario_sedes', COUNT(*) FROM inventario_sedes;
