<?php

    require_once('../core/conexion.php');


    class ComprasModel extends conexion {

        public function obtenerCompras(): array {
            $query = "SELECT
                        c.id_compras,
                        c.numero_factura_compra,
                        c.total_compra,
                        c.fecha_compra,
                        COUNT(dc.id_detalle_compra) as cantidad_detalles,
                        p.nombre_proveedor,
                        s.nombre_sede,
                        u.nombre_usuario AS registrada_por,
                        GROUP_CONCAT(DISTINCT pp.nombre_presentacion SEPARATOR ', ') as presentaciones
                    FROM compras c
                    LEFT JOIN detalle_compra dc ON c.id_compras = dc.id_compra
                    LEFT JOIN productos_presentaciones pp ON dc.id_presentacion = pp.id_presentacion
                    LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor
                    LEFT JOIN sedes s ON c.id_sede = s.id_sede
                    LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
                    GROUP BY c.id_compras, c.numero_factura_compra, c.total_compra, c.fecha_compra, p.nombre_proveedor, s.nombre_sede, u.nombre_usuario
                    ORDER BY c.id_compras DESC";

            return $this->select($query, []);
        }

        /**
         * Ejecuta $operacion dentro de una transaccion: si algo falla, no queda compra a medias.
         */
        public function ejecutarEnTransaccion(callable $operacion): mixed {
            $this->beginTransaction();
            try {
                $resultado = $operacion();
                $this->commit();
                return $resultado;
            } catch (\Throwable $e) {
                if ($this->inTransaction()) {
                    $this->rollBack();
                }
                throw $e;
            }
        }

        public function registrarCompra(array $data): mixed {
            $query = "INSERT INTO compras (
                id_proveedor,
                id_sede,
                id_usuario,
                total_compra,
                numero_factura_compra
            ) VALUES (:id_proveedor, :id_sede, :id_usuario, :total_compra, :numero_factura_compra)";

            $params = [
                ':id_usuario' => $data['idUsuario'] ?? null,
                ':id_proveedor' => $data['idProveedor'],
                ':id_sede' => $data['idSede'],
                ':total_compra' => $data['totalCompra'],
                ':numero_factura_compra' => $data['numeroFacturaCompra']
            ];

            return $this->execute($query, $params);
        }

        /**
         * Registra los detalles de la compra. $idUsuario queda en el kardex (puede ser null en la edicion).
         */
        public function registrarDetalleCompra(int $idCompra, array $detalle, ?int $idUsuario = null): mixed {
            $query = "INSERT INTO detalle_compra (
                id_compra,
                id_presentacion,
                id_sede,
                cantidad_detalle_compra,
                costo_unitario_detalle_compra,
                subtotal_detalle_compra,
                id_estado
            ) VALUES (:id_compra, :id_presentacion, :id_sede, :cantidad, :costo_unitario, :subtotal, 1)";

            foreach ($detalle as $item) {
                $params = [
                    ':id_compra' => $idCompra,
                    ':id_presentacion' => $item['idProductoPresentacion'],
                    ':id_sede' => $item['idSede'],
                    ':cantidad' => $item['cantidad'],
                    ':costo_unitario' => $item['precioCompra'],
                    ':subtotal' => $item['subTotal']
                ];

                $this->execute($query, $params);
                $this->recalculo($item, $idCompra, $idUsuario);
            }
            return true;
        }

        /**
         * Egreso de caja por la compra (la compra se paga desde la caja de la sede).
         */
        public function registrarEgresoCaja(int $idCompra, int $idSede, float $monto, ?int $idUsuario): void {
            $this->execute(
                "INSERT INTO caja_movimientos (
                    id_sede, tipo_caja, concepto_caja, monto_caja, id_compra, id_usuario, observacion_caja
                ) VALUES (:id_sede, 'egreso', 'compra', :monto, :id_compra, :id_usuario, :obs)",
                [
                    ':id_sede' => $idSede,
                    ':monto' => $monto,
                    ':id_compra' => $idCompra,
                    ':id_usuario' => $idUsuario,
                    ':obs' => "Compra #{$idCompra}"
                ]
            );
        }

        private function registrarKardexCompra(int $idSede, int $idPresentacion, string $unidad, float $cantidad, float $saldo, int $idCompra, ?int $idUsuario, ?int $idLote = null): void {
            $this->execute(
                "INSERT INTO movimientos_inventario (
                    id_sede, id_presentacion, id_tipo_movimiento, unidad_movimiento,
                    cantidad_movimiento, saldo_resultante, id_compra, id_usuario,
                    observacion_movimiento, id_lote_esencia
                ) VALUES (
                    :id_sede, :id_pres,
                    (SELECT id_tipo_movimiento FROM tipos_movimiento WHERE nombre_movimiento = 'COMPRA' LIMIT 1),
                    :unidad, :cantidad, :saldo, :id_compra, :id_usuario, :obs, :id_lote
                )",
                [
                    ':id_sede' => $idSede,
                    ':id_pres' => $idPresentacion,
                    ':unidad' => $unidad,
                    ':cantidad' => $cantidad,
                    ':saldo' => $saldo,
                    ':id_compra' => $idCompra,
                    ':id_usuario' => $idUsuario,
                    ':obs' => "Compra #{$idCompra}",
                    ':id_lote' => $idLote
                ]
            );
        }

        private function recalculo(array $data, int $idCompra, ?int $idUsuario){
            $dataProductoPresentacion = $this->dataProductoPresentacion($data['idProductoPresentacion']);
            $idSede = (int)$data['idSede'];

            // Diferenciar por tipo de unidad de medida
            if ($dataProductoPresentacion['unidad_medida_productos_presentacion'] === 'gramo') {
                $this->recalculoEsencia($data, $dataProductoPresentacion, $idSede, $idCompra, $idUsuario);
            } else {
                $this->recalculoProductoRegular($data, $dataProductoPresentacion, $idSede, $idCompra, $idUsuario);
            }
        }

        /**
         * Flujo para ESENCIAS: registra en gramos en inventario_esencias y recalcula fórmulas
         */
        /**
         * Flujo para ESENCIAS: suma gramos al saldo de la sede, crea un lote con su costo por gramo
         * (base del FIFO de ventas) y registra el movimiento en el kardex.
         */
        private function recalculoEsencia(array $data, array $dataPresentacion, int $idSede, int $idCompra, ?int $idUsuario): void {
            $idPresentacion = (int)$data['idProductoPresentacion'];
            $dataStockEsencia = $this->dataStockEsencia($idPresentacion, $idSede);

            // Tamaño de la presentación en gramos: campo de BD o, si no existe, el número del nombre (ej: "125G" -> 125)
            $tamanioPresentacion = $dataPresentacion['cantidad_gramos_presentacion'] ?? null;
            if (!$tamanioPresentacion) {
                $tamanioPresentacion = $this->extraerGramosDelNombre($dataPresentacion['nombre_presentacion']) ?? 1;
            }

            $cantidadGramos = (float)$data['cantidad'] * $tamanioPresentacion;
            // precioCompra es el costo unitario de UNA presentacion: se divide entre sus gramos, no entre el total comprado
            $costoPorGramo = (float)$data['precioCompra'] / $tamanioPresentacion;

            if (!$dataStockEsencia) {
                $this->registrarStockEsencia([
                    'id_presentacion' => $idPresentacion,
                    'id_sede' => $idSede,
                    'cantidad_gramos' => $cantidadGramos,
                    'costo_por_gramo' => $costoPorGramo
                ]);
                $saldo = $cantidadGramos;
            } else {
                $saldo = (float)$dataStockEsencia['cantidad_gramos'] + $cantidadGramos;
                $this->actualizarStockEsencia($idPresentacion, $idSede, $saldo, $costoPorGramo);
            }

            $idLote = (int)$this->execute(
                "INSERT INTO lotes_esencia (
                    id_sede, id_presentacion, id_compra, fecha_ingreso,
                    gramos_iniciales, gramos_disponibles, costo_por_gramo
                ) VALUES (:id_sede, :id_pres, :id_compra, NOW(), :gramos_iniciales, :gramos_disponibles, :costo)",
                [
                    ':id_sede' => $idSede,
                    ':id_pres' => $idPresentacion,
                    ':id_compra' => $idCompra,
                    ':gramos_iniciales' => $cantidadGramos,
                    ':gramos_disponibles' => $cantidadGramos,
                    ':costo' => $costoPorGramo
                ]
            );

            $this->registrarKardexCompra($idSede, $idPresentacion, 'gramo', $cantidadGramos, $saldo, $idCompra, $idUsuario, $idLote);

            $this->actualizarValorCompra($idPresentacion, (float)$data['precioCompra']);
        }

        /**
         * Flujo para PRODUCTOS REGULARES (Relojes, Lociones 1.1, Envases, etc): suma unidades al saldo de la sede.
         */
        private function recalculoProductoRegular(array $data, array $dataPresentacion, int $idSede, int $idCompra, ?int $idUsuario): void {
            $idPresentacion = (int)$data['idProductoPresentacion'];
            $cantidad = (int)$data['cantidad'];
            $dataStockSede = $this->dataStockSede($idPresentacion, $idSede);

            if (!$dataStockSede) {
                $this->registrarStockSede([
                    'id_sede' => $idSede,
                    'stock_sede' => $cantidad,
                    'id_presentacion' => $idPresentacion
                ]);
                $saldo = $cantidad;
            } else {
                $saldo = (int)$dataStockSede['stock_sede'] + $cantidad;
                $this->actualizarStockSede($idPresentacion, $idSede, $saldo);
            }

            $this->registrarKardexCompra($idSede, $idPresentacion, 'unidad', (float)$cantidad, (float)$saldo, $idCompra, $idUsuario);

            $this->actualizarValorCompra($idPresentacion, (float)$data['precioCompra']);
        }

        private function dataProductoPresentacion(int $idProductoPresentacion) {
            $query = "SELECT
                        productos.id_producto,
                        productos.nombre_producto,
                        productos.id_categoria,
                        productos_presentaciones.id_presentacion,
                        productos_presentaciones.nombre_presentacion,
                        productos_presentaciones.precio_compra_presentacion,
                        productos_presentaciones.unidad_medida_productos_presentacion,
                        productos_presentaciones.cantidad_gramos_presentacion,
                        tipo_producto.id_tipo_producto,
                        tipo_producto.descripcion_tipo_producto,
                        categoria_producto.id_categoria AS id_categoria_producto,
                        categoria_producto.nombre_categoria,
                        marca_producto.id_marca,
                        marca_producto.nombre_marca,
                        inventario_sedes.id_sede
                    FROM productos
                    INNER JOIN productos_presentaciones ON productos.id_producto = productos_presentaciones.id_producto
                    INNER JOIN tipo_producto ON productos_presentaciones.id_tipo_producto = tipo_producto.id_tipo_producto
                    INNER JOIN categoria_producto ON productos.id_categoria = categoria_producto.id_categoria
                    INNER JOIN marca_producto ON productos.id_marca = marca_producto.id_marca
                    LEFT JOIN inventario_sedes ON productos_presentaciones.id_presentacion = inventario_sedes.id_presentacion
                    WHERE productos_presentaciones.id_presentacion = :id_presentacion
                    LIMIT 1";
            $params = [':id_presentacion' => $idProductoPresentacion];
            $result = $this->select($query, $params);
            return !empty($result) ? $result[0] : null;
        }

        private function dataStockSede(int $idPresentacion, int $idSede) {
            $query = "SELECT * FROM inventario_sedes
                      WHERE id_presentacion = :id_presentacion
                      AND id_sede = :id_sede";
            $params = [
                ':id_presentacion' => $idPresentacion,
                ':id_sede' => $idSede
            ];
            $result = $this->select($query, $params);
            return !empty($result) ? $result[0] : null;
        }

        private function registrarStockSede(array $data) {
            $query = "INSERT INTO inventario_sedes (
                id_sede,
                stock_sede,
                id_presentacion
            ) VALUES (:id_sede, :stock_sede, :id_presentacion)";

            $params = [
                ':id_sede' => $data['id_sede'],
                ':stock_sede' => $data['stock_sede'],
                ':id_presentacion' => $data['id_presentacion']
            ];

            return $this->execute($query, $params);
        }

        private function actualizarStockSede(int $idPresentacion, int $idSede, int $nuevoStock) {
            $query = "UPDATE inventario_sedes SET stock_sede = :nuevo_stock WHERE id_sede = :id_sede AND id_presentacion = :id_presentacion";
            $params = [
                ':nuevo_stock' => $nuevoStock,
                ':id_sede' => $idSede,
                ':id_presentacion' => $idPresentacion
            ];
            return $this->execute($query, $params);
        }

        private function actualizarValorCompra(int $idProductoPresentacion, float $nuevoPrecioCompra) {
            $query = "UPDATE productos_presentaciones SET precio_compra_presentacion = :nuevo_precio_compra WHERE id_presentacion = :id_presentacion";
            $params = [
                ':nuevo_precio_compra' => $nuevoPrecioCompra,
                ':id_presentacion' => $idProductoPresentacion
            ];
            return $this->execute($query, $params);
        }

        private function dataStockEsencia(int $idPresentacion, int $idSede) {
            $query = "SELECT * FROM inventario_esencias
                      WHERE id_presentacion = :id_presentacion
                      AND id_sede = :id_sede";
            $params = [
                ':id_presentacion' => $idPresentacion,
                ':id_sede' => $idSede
            ];
            $result = $this->select($query, $params);
            return !empty($result) ? $result[0] : null;
        }

        private function registrarStockEsencia(array $data) {
            error_log("[ComprasModel::registrarStockEsencia] Insertando nuevo stock de esencia");
            error_log("  - ID Presentación: " . $data['id_presentacion']);
            error_log("  - ID Sede: " . $data['id_sede']);
            error_log("  - Cantidad Gramos: " . $data['cantidad_gramos']);
            error_log("  - Costo por Gramo: " . $data['costo_por_gramo']);

            $query = "INSERT INTO inventario_esencias (
                id_presentacion,
                id_sede,
                cantidad_gramos,
                costo_por_gramo,
                fecha_actualizacion
            ) VALUES (:id_presentacion, :id_sede, :cantidad_gramos, :costo_por_gramo, NOW())";

            $params = [
                ':id_presentacion' => $data['id_presentacion'],
                ':id_sede' => $data['id_sede'],
                ':cantidad_gramos' => $data['cantidad_gramos'],
                ':costo_por_gramo' => $data['costo_por_gramo']
            ];

            $result = $this->execute($query, $params);
            error_log("[ComprasModel::registrarStockEsencia] Resultado: " . ($result ? "OK" : "FAIL"));
            return $result;
        }

        private function actualizarStockEsencia(int $idPresentacion, int $idSede, float $nuevosGramos, float $costoPorGramo) {
            error_log("[ComprasModel::actualizarStockEsencia] Actualizando stock de esencia");
            error_log("  - ID Presentación: " . $idPresentacion);
            error_log("  - ID Sede: " . $idSede);
            error_log("  - Nuevos Gramos: " . $nuevosGramos);
            error_log("  - Costo por Gramo: " . $costoPorGramo);

            $query = "UPDATE inventario_esencias
                      SET cantidad_gramos = :cantidad_gramos,
                          costo_por_gramo = :costo_por_gramo,
                          fecha_actualizacion = NOW()
                      WHERE id_presentacion = :id_presentacion
                      AND id_sede = :id_sede";
            $params = [
                ':cantidad_gramos' => $nuevosGramos,
                ':costo_por_gramo' => $costoPorGramo,
                ':id_presentacion' => $idPresentacion,
                ':id_sede' => $idSede
            ];

            $result = $this->execute($query, $params);
            error_log("[ComprasModel::actualizarStockEsencia] Resultado: " . ($result ? "OK" : "FAIL"));
            return $result;
        }

        public function obtenerDetalleCompra(int $idCompra): array {
            $query = "SELECT
                        dc.id_detalle_compra,
                        dc.id_compra,
                        dc.id_presentacion,
                        dc.id_sede,
                        dc.cantidad_detalle_compra,
                        dc.costo_unitario_detalle_compra,
                        dc.subtotal_detalle_compra,
                        pp.nombre_presentacion,
                        p.nombre_producto,
                        cp.nombre_categoria,
                        s.nombre_sede
                    FROM detalle_compra dc
                    LEFT JOIN productos_presentaciones pp ON dc.id_presentacion = pp.id_presentacion
                    LEFT JOIN productos p ON pp.id_producto = p.id_producto
                    LEFT JOIN categoria_producto cp ON p.id_categoria = cp.id_categoria
                    LEFT JOIN sedes s ON dc.id_sede = s.id_sede
                    WHERE dc.id_compra = :id_compra";
            $params = [':id_compra' => $idCompra];
            return $this->select($query, $params);
        }

        public function obtenerCompraPorId(int $idCompra): ?array {
            $query = "SELECT c.*, p.nombre_proveedor
                      FROM compras c
                      LEFT JOIN proveedor p ON c.id_proveedor = p.id_proveedor
                      WHERE c.id_compras = :id_compras";
            $params = [':id_compras' => $idCompra];
            $result = $this->select($query, $params);
            return !empty($result) ? $result[0] : null;
        }

        public function actualizarCompra(int $idCompra, array $data): mixed {
            $query = "UPDATE compras SET
                        id_proveedor = :id_proveedor,
                        id_sede = :id_sede,
                        numero_factura_compra = :numero_factura_compra,
                        total_compra = :total_compra
                      WHERE id_compras = :id_compras";
            $params = [
                ':id_proveedor' => $data['idProveedor'],
                ':id_sede' => $data['idSede'],
                ':numero_factura_compra' => $data['numeroFacturaCompra'],
                ':total_compra' => $data['totalCompra'],
                ':id_compras' => $idCompra
            ];
            return $this->execute($query, $params);
        }

        /**
         * Revierte el stock, lotes, kardex y caja que dejo una compra, para poder editarla o eliminarla.
         * Valida todo antes de tocar nada: si ya se vendio parte de lo comprado, lanza DomainException.
         */
        public function revertirEfectosCompra(int $idCompra): void {
            $detalles = $this->select(
                "SELECT id_presentacion, id_sede, cantidad_detalle_compra FROM detalle_compra WHERE id_compra = :id",
                [':id' => $idCompra]
            );

            $reversiones = [];
            foreach ($detalles as $d) {
                $idPres = (int)$d['id_presentacion'];
                $idSede = (int)$d['id_sede'];
                $pres = $this->dataProductoPresentacion($idPres);
                $nombre = $pres ? "{$pres['nombre_producto']} - {$pres['nombre_presentacion']}" : "#{$idPres}";

                if (($pres['unidad_medida_productos_presentacion'] ?? '') === 'gramo') {
                    $lotes = $this->select(
                        "SELECT id_lote, gramos_iniciales, gramos_disponibles FROM lotes_esencia
                         WHERE id_compra = :id AND id_presentacion = :pres AND id_sede = :sede FOR UPDATE",
                        [':id' => $idCompra, ':pres' => $idPres, ':sede' => $idSede]
                    );
                    $gramos = 0.0;
                    foreach ($lotes as $l) {
                        if ((float)$l['gramos_disponibles'] + 0.001 < (float)$l['gramos_iniciales']) {
                            throw new \DomainException("No se puede modificar esta compra: parte de {$nombre} ya se vendió.");
                        }
                        $gramos += (float)$l['gramos_iniciales'];
                    }
                    $saldo = $this->select(
                        "SELECT cantidad_gramos FROM inventario_esencias WHERE id_presentacion = :pres AND id_sede = :sede FOR UPDATE",
                        [':pres' => $idPres, ':sede' => $idSede]
                    );
                    if ((float)($saldo[0]['cantidad_gramos'] ?? 0) + 0.001 < $gramos) {
                        throw new \DomainException("No se puede modificar esta compra: parte de {$nombre} ya se vendió.");
                    }
                    $reversiones[] = ['gramo', $idPres, $idSede, $gramos];
                } else {
                    $cantidad = (int)$d['cantidad_detalle_compra'];
                    $stock = $this->select(
                        "SELECT stock_sede FROM inventario_sedes WHERE id_presentacion = :pres AND id_sede = :sede FOR UPDATE",
                        [':pres' => $idPres, ':sede' => $idSede]
                    );
                    if ((int)($stock[0]['stock_sede'] ?? 0) < $cantidad) {
                        throw new \DomainException("No se puede modificar esta compra: ya se vendieron unidades de {$nombre}.");
                    }
                    $reversiones[] = ['unidad', $idPres, $idSede, $cantidad];
                }
            }

            $this->execute("DELETE FROM movimientos_inventario WHERE id_compra = :id", [':id' => $idCompra]);
            $this->execute("DELETE FROM caja_movimientos WHERE id_compra = :id", [':id' => $idCompra]);

            foreach ($reversiones as [$tipo, $idPres, $idSede, $cantidad]) {
                if ($tipo === 'gramo') {
                    $this->execute(
                        "DELETE FROM lotes_esencia WHERE id_compra = :id AND id_presentacion = :pres AND id_sede = :sede",
                        [':id' => $idCompra, ':pres' => $idPres, ':sede' => $idSede]
                    );
                    $this->execute(
                        "UPDATE inventario_esencias SET cantidad_gramos = cantidad_gramos - :c, fecha_actualizacion = NOW()
                         WHERE id_presentacion = :pres AND id_sede = :sede",
                        [':c' => $cantidad, ':pres' => $idPres, ':sede' => $idSede]
                    );
                } else {
                    $this->execute(
                        "UPDATE inventario_sedes SET stock_sede = stock_sede - :c WHERE id_presentacion = :pres AND id_sede = :sede",
                        [':c' => $cantidad, ':pres' => $idPres, ':sede' => $idSede]
                    );
                }
            }
        }

        public function eliminarDetallesCompra(int $idCompra): mixed {
            $query = "DELETE FROM detalle_compra WHERE id_compra = :id_compra";
            return $this->execute($query, [':id_compra' => $idCompra]);
        }

        public function eliminarCompra(int $idCompra): mixed {
            $this->revertirEfectosCompra($idCompra);
            $this->eliminarDetallesCompra($idCompra);
            $query = "DELETE FROM compras WHERE id_compras = :id_compras";
            return $this->execute($query, [':id_compras' => $idCompra]);
        }

        private function extraerGramosDelNombre(?string $nombrePresentacion): ?int {
            if (!$nombrePresentacion) {
                return null;
            }

            // Intenta extraer: "125G", "125g", "125 gramos", etc.
            if (preg_match('/(\d+)\s*[gG](?:ramos)?/', $nombrePresentacion, $matches)) {
                return (int)$matches[1];
            }

            // Fallback: si el nombre es solo números, asumir que son gramos
            // Ej: "250" se interpreta como "250 gramos"
            if (preg_match('/^(\d+)$/', $nombrePresentacion, $matches)) {
                return (int)$matches[1];
            }

            return null;
        }

    }
    