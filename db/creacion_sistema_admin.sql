-- ============================================================
-- JM Distribuciones - Creación del sistema desde cero
-- Estructura actual (incluye migraciones 001 a 008) y datos de administración.
-- Incluye: usuarios administradores (SuperUsuario), perfiles y permisos,
-- catálogos del sistema y las categorías que usa el código.
-- NO incluye: sedes, productos, marcas, inventario, compras, ventas, gastos,
-- clientes ni proveedores. Esos se crean desde el sistema.
--
-- ADVERTENCIA: DROP TABLE IF EXISTS borra las tablas existentes de jm_distribuciones.
-- Ejecutar solo en una instalación nueva:
--   /opt/lampp/bin/mysql -uroot -h localhost < db/creacion_sistema_admin.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS jm_distribuciones CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE jm_distribuciones;

SET FOREIGN_KEY_CHECKS = 0;

-- ================= ESTRUCTURA =================
DROP TABLE IF EXISTS `banner_promociones`;
CREATE TABLE `banner_promociones` (
  `id_banner_promocion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_banner_promocion` varchar(100) DEFAULT NULL,
  `img_banner_promocion` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_banner_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `caja_movimientos`;
CREATE TABLE `caja_movimientos` (
  `id_caja_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `id_sede` int(11) NOT NULL,
  `tipo_caja` enum('ingreso','egreso') NOT NULL,
  `concepto_caja` enum('pago_venta','anulacion_venta','compra','gasto','otro') NOT NULL,
  `monto_caja` decimal(12,2) NOT NULL,
  `id_metodo_pago` int(11) DEFAULT NULL,
  `id_pago_venta` int(11) DEFAULT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `id_gasto` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `observacion_caja` varchar(255) DEFAULT NULL,
  `fecha_caja` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_caja_movimiento`),
  KEY `idx_caja_sede_fecha` (`id_sede`,`fecha_caja`),
  KEY `fk_caja_metodo_pago` (`id_metodo_pago`),
  KEY `fk_caja_pago_venta` (`id_pago_venta`),
  KEY `fk_caja_compra` (`id_compra`),
  KEY `fk_caja_gasto` (`id_gasto`),
  KEY `fk_caja_usuario` (`id_usuario`),
  KEY `fk_caja_venta` (`id_venta`),
  CONSTRAINT `fk_caja_compra` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compras`),
  CONSTRAINT `fk_caja_gasto` FOREIGN KEY (`id_gasto`) REFERENCES `gastos` (`id_gasto`),
  CONSTRAINT `fk_caja_metodo_pago` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pago` (`id_metodo_pago`),
  CONSTRAINT `fk_caja_pago_venta` FOREIGN KEY (`id_pago_venta`) REFERENCES `pagos_venta` (`id_pago_venta`),
  CONSTRAINT `fk_caja_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`),
  CONSTRAINT `fk_caja_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `fk_caja_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `categoria_producto`;
CREATE TABLE `categoria_producto` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(150) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_categoria`),
  KEY `categoria_distribuciones_estado_distribuciones_FK` (`id_estado`),
  CONSTRAINT `categoria_distribuciones_estado_distribuciones_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(250) DEFAULT NULL,
  `identificacion_cliente` varchar(20) DEFAULT NULL,
  `telefono_cliente` varchar(20) DEFAULT NULL,
  `direccion_cliente` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `compras`;
CREATE TABLE `compras` (
  `id_compras` int(11) NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(11) DEFAULT NULL,
  `id_sede` int(11) NOT NULL,
  `total_compra` decimal(12,2) NOT NULL DEFAULT 0.00,
  `numero_factura_compra` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  `fecha_compra` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_compras`),
  KEY `compras_distribuciones_proveedor_distribuciones_FK` (`id_proveedor`),
  KEY `compras_distribuciones_estado_distribuciones_FK` (`id_estado`),
  KEY `fk_compras_sede` (`id_sede`),
  CONSTRAINT `compras_distribuciones_estado_distribuciones_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
  CONSTRAINT `compras_distribuciones_proveedor_distribuciones_FK` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`),
  CONSTRAINT `fk_compras_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `descuentos`;
CREATE TABLE `descuentos` (
  `id_descuento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_descuento` varchar(100) DEFAULT NULL,
  `porcentaje_descuento` varchar(100) DEFAULT NULL,
  `fecha_inicio_descuento` date DEFAULT NULL,
  `fecha_fin_descuento` date DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_descuento`),
  KEY `Descuentos_estado_FK` (`id_estado`),
  CONSTRAINT `Descuentos_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `detalle_compra`;
CREATE TABLE `detalle_compra` (
  `id_detalle_compra` int(11) NOT NULL AUTO_INCREMENT,
  `id_compra` int(11) DEFAULT NULL,
  `id_presentacion` int(11) DEFAULT NULL,
  `cantidad_detalle_compra` int(11) DEFAULT NULL,
  `costo_unitario_detalle_compra` int(11) DEFAULT NULL,
  `subtotal_detalle_compra` int(11) DEFAULT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_detalle_compra`),
  KEY `detalle_compra_distribuciones_compras_distribuciones_FK` (`id_compra`),
  KEY `detalle_compra_distribuciones_estado_distribuciones_FK` (`id_estado`),
  KEY `detalle_compra_productos_presentaciones_FK` (`id_presentacion`),
  KEY `detalle_compra_sedes_FK` (`id_sede`),
  CONSTRAINT `detalle_compra_distribuciones_compras_distribuciones_FK` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compras`),
  CONSTRAINT `detalle_compra_distribuciones_estado_distribuciones_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
  CONSTRAINT `detalle_compra_productos_presentaciones_FK` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`),
  CONSTRAINT `detalle_compra_sedes_FK` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE `detalle_venta` (
  `id_detalle_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_presentacion` int(11) DEFAULT NULL,
  `id_producto_esencia` int(11) DEFAULT NULL,
  `id_formula` int(11) DEFAULT NULL,
  `cantidad_detalle_venta` int(11) NOT NULL,
  `precio_unitario_detalle_venta` decimal(12,2) NOT NULL,
  `subtotal_detalle_venta` decimal(12,2) NOT NULL,
  `costo_unitario_detalle_venta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_total_detalle_venta` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_detalle_venta`),
  KEY `idx_detalle_venta_venta` (`id_venta`),
  KEY `idx_detalle_venta_presentacion` (`id_presentacion`),
  KEY `fk_detalle_venta_esencia` (`id_producto_esencia`),
  KEY `fk_detalle_venta_formula` (`id_formula`),
  CONSTRAINT `fk_detalle_venta_esencia` FOREIGN KEY (`id_producto_esencia`) REFERENCES `productos` (`id_producto`),
  CONSTRAINT `fk_detalle_venta_formula` FOREIGN KEY (`id_formula`) REFERENCES `formulas` (`id_formula`),
  CONSTRAINT `fk_detalle_venta_presentacion` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`),
  CONSTRAINT `fk_detalle_venta_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `estado`;
CREATE TABLE `estado` (
  `id_estado` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_estado` varchar(100) DEFAULT NULL,
  `decripcion_estado` varchar(100) DEFAULT NULL,
  `estado` char(1) DEFAULT '1',
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `formulas`;
CREATE TABLE `formulas` (
  `id_formula` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_formula` varchar(100) DEFAULT NULL,
  `id_tipo_concentracion` int(11) DEFAULT NULL,
  `cantidad_esencia` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  `id_insumo_formula` int(11) DEFAULT NULL,
  `id_presentacion_envase` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_formula`),
  KEY `formulas_estado_FK` (`id_estado`),
  KEY `formulas_tipo_concentracion_FK` (`id_tipo_concentracion`),
  KEY `formulas_insumo_formulas_FK` (`id_insumo_formula`),
  KEY `fk_formulas_envase` (`id_presentacion_envase`),
  CONSTRAINT `fk_formulas_envase` FOREIGN KEY (`id_presentacion_envase`) REFERENCES `productos_presentaciones` (`id_presentacion`),
  CONSTRAINT `formulas_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
  CONSTRAINT `formulas_insumo_formulas_FK` FOREIGN KEY (`id_insumo_formula`) REFERENCES `insumo_formulas` (`id_insumo_formula`),
  CONSTRAINT `formulas_tipo_concentracion_FK` FOREIGN KEY (`id_tipo_concentracion`) REFERENCES `tipo_concentracion` (`id_tipo_concentracion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `gastos`;
CREATE TABLE `gastos` (
  `id_gasto` int(11) NOT NULL AUTO_INCREMENT,
  `id_sede` int(11) NOT NULL,
  `categoria_gasto` varchar(100) NOT NULL,
  `descripcion_gasto` varchar(255) DEFAULT NULL,
  `monto_gasto` decimal(12,2) NOT NULL,
  `id_metodo_pago` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_gasto` date NOT NULL DEFAULT curdate(),
  PRIMARY KEY (`id_gasto`),
  KEY `idx_gastos_sede_fecha` (`id_sede`,`fecha_gasto`),
  KEY `fk_gastos_metodo` (`id_metodo_pago`),
  KEY `fk_gastos_usuario` (`id_usuario`),
  CONSTRAINT `fk_gastos_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pago` (`id_metodo_pago`),
  CONSTRAINT `fk_gastos_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`),
  CONSTRAINT `fk_gastos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `genero`;
CREATE TABLE `genero` (
  `id_genero` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_genero` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_genero`),
  KEY `genero_estado_FK` (`id_estado`),
  CONSTRAINT `genero_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `insumo_formulas`;
CREATE TABLE `insumo_formulas` (
  `id_insumo_formula` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_insumo` varchar(100) DEFAULT NULL,
  `tamanio_insumo` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_insumo_formula`),
  KEY `insumo_formulas_estado_FK` (`id_estado`),
  CONSTRAINT `insumo_formulas_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `inventario_esencias`;
CREATE TABLE `inventario_esencias` (
  `id_inventario_esencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_presentacion` int(11) DEFAULT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `cantidad_gramos` decimal(10,2) DEFAULT NULL,
  `costo_por_gramo` decimal(10,2) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_inventario_esencia`),
  UNIQUE KEY `uq_inventario_esencia_sede_presentacion` (`id_sede`,`id_presentacion`),
  KEY `id_presentacion` (`id_presentacion`),
  CONSTRAINT `inventario_esencias_ibfk_1` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`),
  CONSTRAINT `inventario_esencias_ibfk_2` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `inventario_sedes`;
CREATE TABLE `inventario_sedes` (
  `id_inventario_sede` int(11) NOT NULL AUTO_INCREMENT,
  `id_presentacion` int(11) DEFAULT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `stock_sede` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_inventario_sede`),
  UNIQUE KEY `uq_inventario_sede_presentacion` (`id_sede`,`id_presentacion`),
  KEY `inventario_sedes_productos_presentaciones_FK` (`id_presentacion`),
  CONSTRAINT `inventario_sedes_productos_presentaciones_FK` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`),
  CONSTRAINT `inventario_sedes_sedes_FK` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `lotes_esencia`;
CREATE TABLE `lotes_esencia` (
  `id_lote` int(11) NOT NULL AUTO_INCREMENT,
  `id_sede` int(11) NOT NULL,
  `id_presentacion` int(11) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp(),
  `gramos_iniciales` decimal(10,2) NOT NULL,
  `gramos_disponibles` decimal(10,2) NOT NULL,
  `costo_por_gramo` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_lote`),
  KEY `idx_lotes_sede_presentacion` (`id_sede`,`id_presentacion`),
  KEY `idx_lotes_fifo` (`id_sede`,`fecha_ingreso`),
  KEY `fk_lotes_presentacion` (`id_presentacion`),
  KEY `fk_lotes_compra` (`id_compra`),
  CONSTRAINT `fk_lotes_compra` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compras`),
  CONSTRAINT `fk_lotes_presentacion` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`),
  CONSTRAINT `fk_lotes_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `marca_producto`;
CREATE TABLE `marca_producto` (
  `id_marca` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_marca` varchar(150) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  `img_marca` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_marca`),
  KEY `marca_distribuciones_estado_distribuciones_FK` (`id_estado`),
  CONSTRAINT `marca_distribuciones_estado_distribuciones_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `metodos_pago`;
CREATE TABLE `metodos_pago` (
  `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_metodo_pago` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `movimientos_inventario`;
CREATE TABLE `movimientos_inventario` (
  `id_movimientos_inventario` int(11) NOT NULL AUTO_INCREMENT,
  `id_sede` int(11) NOT NULL,
  `id_presentacion` int(11) NOT NULL,
  `id_tipo_movimiento` int(11) NOT NULL,
  `unidad_movimiento` enum('unidad','gramo') NOT NULL,
  `cantidad_movimiento` decimal(10,2) NOT NULL,
  `saldo_resultante` decimal(10,2) NOT NULL,
  `id_compra` int(11) DEFAULT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `id_lote_esencia` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `observacion_movimiento` varchar(255) DEFAULT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_movimientos_inventario`),
  KEY `idx_movimientos_sede_presentacion` (`id_sede`,`id_presentacion`),
  KEY `idx_movimientos_fecha` (`fecha_movimiento`),
  KEY `fk_movimientos_presentacion` (`id_presentacion`),
  KEY `fk_movimientos_tipo` (`id_tipo_movimiento`),
  KEY `fk_movimientos_compra` (`id_compra`),
  KEY `fk_movimientos_venta` (`id_venta`),
  KEY `fk_movimientos_usuario` (`id_usuario`),
  KEY `fk_movimientos_lote` (`id_lote_esencia`),
  CONSTRAINT `fk_movimientos_compra` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id_compras`),
  CONSTRAINT `fk_movimientos_lote` FOREIGN KEY (`id_lote_esencia`) REFERENCES `lotes_esencia` (`id_lote`),
  CONSTRAINT `fk_movimientos_presentacion` FOREIGN KEY (`id_presentacion`) REFERENCES `productos_presentaciones` (`id_presentacion`),
  CONSTRAINT `fk_movimientos_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`),
  CONSTRAINT `fk_movimientos_tipo` FOREIGN KEY (`id_tipo_movimiento`) REFERENCES `tipos_movimiento` (`id_tipo_movimiento`),
  CONSTRAINT `fk_movimientos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `fk_movimientos_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `pagos_venta`;
CREATE TABLE `pagos_venta` (
  `id_pago_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `monto_pago` decimal(12,2) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_pago` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pago_venta`),
  KEY `idx_pagos_venta_venta` (`id_venta`),
  KEY `fk_pagos_venta_metodo` (`id_metodo_pago`),
  KEY `fk_pagos_venta_usuario` (`id_usuario`),
  CONSTRAINT `fk_pagos_venta_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pago` (`id_metodo_pago`),
  CONSTRAINT `fk_pagos_venta_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `fk_pagos_venta_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `perfil_usuario`;
CREATE TABLE `perfil_usuario` (
  `id_perfil_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_perfil_usuario` varchar(100) DEFAULT NULL,
  `descripcion_perfil_usuario` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_perfil_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `perfil_usuario_permisos`;
CREATE TABLE `perfil_usuario_permisos` (
  `id_perfil_usuario_permiso` int(11) NOT NULL AUTO_INCREMENT,
  `id_perfil_usuario` int(11) DEFAULT NULL,
  `id_permiso` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_perfil_usuario_permiso`),
  KEY `perfil_usuario_permisos_perfil_usuario_FK` (`id_perfil_usuario`),
  KEY `perfil_usuario_permisos_permisos_FK` (`id_permiso`),
  KEY `perfil_usuario_permisos_estado_FK` (`id_estado`),
  CONSTRAINT `perfil_usuario_permisos_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
  CONSTRAINT `perfil_usuario_permisos_perfil_usuario_FK` FOREIGN KEY (`id_perfil_usuario`) REFERENCES `perfil_usuario` (`id_perfil_usuario`),
  CONSTRAINT `perfil_usuario_permisos_permisos_FK` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `permisos`;
CREATE TABLE `permisos` (
  `id_permiso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_permiso` varchar(100) DEFAULT NULL,
  `descripcion_permiso` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_permiso`),
  KEY `permisos_estado_FK` (`id_estado`),
  CONSTRAINT `permisos_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `precios_locion_sede`;
CREATE TABLE `precios_locion_sede` (
  `id_precio_locion` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto_esencia` int(11) NOT NULL,
  `id_formula` int(11) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `precio_venta` decimal(12,2) NOT NULL,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_precio_locion`),
  UNIQUE KEY `uq_precio_locion` (`id_producto_esencia`,`id_formula`,`id_sede`),
  KEY `fk_precio_locion_formula` (`id_formula`),
  KEY `fk_precio_locion_sede` (`id_sede`),
  CONSTRAINT `fk_precio_locion_esencia` FOREIGN KEY (`id_producto_esencia`) REFERENCES `productos` (`id_producto`),
  CONSTRAINT `fk_precio_locion_formula` FOREIGN KEY (`id_formula`) REFERENCES `formulas` (`id_formula`),
  CONSTRAINT `fk_precio_locion_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_producto` varchar(150) DEFAULT NULL,
  `codigo_producto` varchar(100) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_marca` int(11) DEFAULT NULL,
  `descripcion_producto` varchar(256) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `logo_producto` varchar(100) DEFAULT NULL,
  `faborito_producto` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id_producto`),
  KEY `productos_distribuciones_categoria_distribuciones_FK` (`id_categoria`),
  KEY `productos_distribuciones_marca_distribuciones_FK` (`id_marca`),
  KEY `productos_distribuciones_estado_distribuciones_FK` (`id_estado`),
  CONSTRAINT `productos_distribuciones_categoria_distribuciones_FK` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_producto` (`id_categoria`),
  CONSTRAINT `productos_distribuciones_estado_distribuciones_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
  CONSTRAINT `productos_distribuciones_marca_distribuciones_FK` FOREIGN KEY (`id_marca`) REFERENCES `marca_producto` (`id_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `productos_presentaciones`;
CREATE TABLE `productos_presentaciones` (
  `id_presentacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) DEFAULT NULL,
  `nombre_presentacion` varchar(200) DEFAULT NULL,
  `cantidad_gramos_presentacion` int(11) DEFAULT NULL,
  `codigo_presentacion` varchar(100) DEFAULT NULL,
  `precio_compra_presentacion` int(11) DEFAULT NULL,
  `precio_venta_presentacion` int(11) DEFAULT NULL,
  `id_tipo_producto` int(11) DEFAULT NULL,
  `img_presentacion` varchar(256) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `unidad_medida_productos_presentacion` varchar(100) DEFAULT NULL,
  `id_genero` int(11) DEFAULT NULL,
  `descripcion_presentacion` varchar(256) DEFAULT NULL,
  `favorito_presentacion` varchar(100) DEFAULT '0',
  `id_descuento` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_presentacion`),
  KEY `productos_presentaciones_productos_FK` (`id_producto`),
  KEY `productos_presentaciones_tipo_producto_FK` (`id_tipo_producto`),
  KEY `productos_presentaciones_estado_FK` (`id_estado`),
  KEY `productos_presentaciones_genero_FK` (`id_genero`),
  KEY `productos_presentaciones_descuentos_FK` (`id_descuento`),
  KEY `idx_cantidad_gramos_presentacion` (`cantidad_gramos_presentacion`),
  CONSTRAINT `productos_presentaciones_descuentos_FK` FOREIGN KEY (`id_descuento`) REFERENCES `descuentos` (`id_descuento`),
  CONSTRAINT `productos_presentaciones_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
  CONSTRAINT `productos_presentaciones_genero_FK` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`),
  CONSTRAINT `productos_presentaciones_productos_FK` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  CONSTRAINT `productos_presentaciones_tipo_producto_FK` FOREIGN KEY (`id_tipo_producto`) REFERENCES `tipo_producto` (`id_tipo_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_proveedor` varchar(250) DEFAULT NULL,
  `contacto_proveedor` varchar(250) DEFAULT NULL,
  `telefono_proveedor` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `sedes`;
CREATE TABLE `sedes` (
  `id_sede` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_sede` varchar(100) NOT NULL,
  `direccion_sede` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  `responsable_sede` varchar(100) DEFAULT NULL,
  `telefono_sede` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_sede`),
  KEY `sedes_estado_FK` (`id_estado`),
  CONSTRAINT `sedes_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `tipo_concentracion`;
CREATE TABLE `tipo_concentracion` (
  `id_tipo_concentracion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_concentracion` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_tipo_concentracion`),
  KEY `tipo_concentracion_estado_FK` (`id_estado`),
  CONSTRAINT `tipo_concentracion_estado_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `tipo_producto`;
CREATE TABLE `tipo_producto` (
  `id_tipo_producto` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion_tipo_producto` varchar(100) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_tipo_producto`),
  KEY `tipo_producto_estado_distribuciones_FK` (`id_estado`),
  CONSTRAINT `tipo_producto_estado_distribuciones_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `tipos_movimiento`;
CREATE TABLE `tipos_movimiento` (
  `id_tipo_movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_movimiento` varchar(100) NOT NULL,
  PRIMARY KEY (`id_tipo_movimiento`),
  UNIQUE KEY `uq_tipos_movimiento_nombre` (`nombre_movimiento`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(250) DEFAULT NULL,
  `user_usuario` varchar(100) DEFAULT NULL,
  `pass_usuario` varchar(150) DEFAULT NULL,
  `id_perfil` int(11) DEFAULT NULL,
  `id_sede` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT 1,
  PRIMARY KEY (`id_usuario`),
  KEY `usuario_distribuciones_perfil_usuario_distribuciones_FK` (`id_perfil`),
  KEY `usuario_distribuciones_estado_distribuciones_FK` (`id_estado`),
  KEY `fk_usuario_sede` (`id_sede`),
  CONSTRAINT `fk_usuario_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`),
  CONSTRAINT `usuario_distribuciones_estado_distribuciones_FK` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
  CONSTRAINT `usuario_distribuciones_perfil_usuario_distribuciones_FK` FOREIGN KEY (`id_perfil`) REFERENCES `perfil_usuario` (`id_perfil_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL AUTO_INCREMENT,
  `id_sede` int(11) NOT NULL,
  `numero_venta` varchar(30) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `subtotal_venta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento_venta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_venta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `costo_total_venta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado_venta` enum('pendiente','pagada','anulada') NOT NULL DEFAULT 'pendiente',
  `fecha_venta` datetime NOT NULL DEFAULT current_timestamp(),
  `observacion_venta` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_venta`),
  UNIQUE KEY `uq_ventas_sede_numero` (`id_sede`,`numero_venta`),
  KEY `idx_ventas_fecha` (`fecha_venta`),
  KEY `fk_ventas_cliente` (`id_cliente`),
  KEY `fk_ventas_usuario` (`id_usuario`),
  CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  CONSTRAINT `fk_ventas_sede` FOREIGN KEY (`id_sede`) REFERENCES `sedes` (`id_sede`),
  CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ================= DATOS =================
-- Datos de administración (sin sedes, productos, inventario, compras ni ventas)
-- estado
INSERT INTO estado (id_estado, nombre_estado, decripcion_estado, estado) VALUES
  ('1', 'Activo', 'Elemento activo', '1'),
  ('2', 'Inactivo', 'Elemento inactivo', '1');

-- perfil_usuario
INSERT INTO perfil_usuario (id_perfil_usuario, nombre_perfil_usuario, descripcion_perfil_usuario, id_estado) VALUES
  ('1', 'SuperUsuario', 'Administrador de todo el sistema', '1'),
  ('2', 'Vendedor', 'Vendedor de sede', '1');

-- permisos
INSERT INTO permisos (id_permiso, nombre_permiso, descripcion_permiso, id_estado) VALUES
  ('2', 'configuraciones', 'Permiso para administrar todas las configuraciones del sistema.', '1'),
  ('3', 'Promociones', 'Permiso para administrar modulo de promociones', '1'),
  ('4', 'Produccion', 'Permiso para administrar modulo de produccion', '1'),
  ('5', 'Compras', 'Permiso para administrar modulo de compras', '1'),
  ('6', 'Inventario', 'Permiso para administrar el modulo de inventario', '1'),
  ('7', 'Historial', 'Permiso para admistrar modulo de historial', '1'),
  ('8', 'Ventas', 'Permiso para realizacion de venta', '1'),
  ('9', 'Informe de produccion', 'Permiso para ver el informe de produccion de cada esencia', '1'),
  ('10', 'Registro Ventas', 'Permiso para registrar las ventas para el vendedor', '1'),
  ('11', 'Registro gastos', 'Permiso para registrar los gastos de la sede', '1');

-- perfil_usuario_permisos
INSERT INTO perfil_usuario_permisos (id_perfil_usuario, id_permiso, id_estado) VALUES
  ('1', '2', '1'),
  ('1', '3', '1'),
  ('1', '4', '1'),
  ('1', '5', '1'),
  ('1', '6', '1'),
  ('1', '7', '1'),
  ('1', '8', '1'),
  ('1', '9', '1'),
  ('1', '10', '1'),
  ('1', '11', '1'),
  ('2', '9', '1'),
  ('2', '10', '1'),
  ('2', '11', '1');

-- usuario
INSERT INTO usuario (id_usuario, nombre_usuario, user_usuario, pass_usuario, id_perfil, id_estado) VALUES
  ('1', 'Johan Montero', 'jmontero1', 'camilo94@', '1', '2'),
  ('2', 'Johan Montero', 'jmontero', '$2y$10$5y93s6HEMnvnwvyNtwew3eWWZmrOP9JxvY0wVgijO5UYB916vCqsa', '1', '1');

-- metodos_pago
INSERT INTO metodos_pago (id_metodo_pago, nombre_metodo_pago, id_estado) VALUES
  ('1', 'Efectivo', '1'),
  ('2', 'Transferencia', '1'),
  ('3', 'Nequi', '1'),
  ('4', 'Daviplata', '1'),
  ('5', 'Tarjeta', '1');

-- tipos_movimiento
INSERT INTO tipos_movimiento (id_tipo_movimiento, nombre_movimiento) VALUES
  ('1', 'COMPRA'),
  ('2', 'VENTA'),
  ('3', 'ANULACION_VENTA'),
  ('4', 'AJUSTE');

-- tipo_concentracion
INSERT INTO tipo_concentracion (id_tipo_concentracion, nombre_concentracion, id_estado) VALUES
  ('1', 'Normal', '1'),
  ('2', 'Concentrada', '1');

-- tipo_producto
INSERT INTO tipo_producto (id_tipo_producto, descripcion_tipo_producto, id_estado) VALUES
  ('1', 'REVENTA', '1'),
  ('3', 'INSUMO', '1');

-- genero
INSERT INTO genero (id_genero, nombre_genero) VALUES
  ('1', 'Dama'),
  ('2', 'Caballero');

-- categoria_producto
INSERT INTO categoria_producto (id_categoria, nombre_categoria, id_estado) VALUES
  ('1', 'Relojes', '1'),
  ('3', 'Esencias', '1'),
  ('4', 'Insumos', '1'),
  ('5', 'Lociones AAA', '1');

