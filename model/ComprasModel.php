<?php

    require_once('../core/conexion.php');
    require_once('../services/FormulaCalculatorService.php');


    class ComprasModel extends conexion {

        public function obtenerCompras(): array {
            $query = "SELECT
                        c.id_compras,
                        c.numero_factura_compra,
                        c.total_compra,
                        COUNT(dc.id_detalle_compra) as cantidad_detalles,
                        GROUP_CONCAT(DISTINCT pp.nombre_presentacion SEPARATOR ', ') as presentaciones
                    FROM compras c
                    LEFT JOIN detalle_compra dc ON c.id_compras = dc.id_compra
                    LEFT JOIN productos_presentaciones pp ON dc.id_presentacion = pp.id_presentacion
                    GROUP BY c.id_compras, c.numero_factura_compra, c.total_compra
                    ORDER BY c.id_compras DESC";

            return $this->select($query, []);
        }

        public function registrarCompra(array $data): mixed {
            $query = "INSERT INTO compras (
                id_proveedor,
                total_compra,
                numero_factura_compra
            ) VALUES (:id_proveedor, :total_compra, :numero_factura_compra)";

            $params = [
                ':id_proveedor' => $data['idProveedor'],
                ':total_compra' => $data['totalCompra'],
                ':numero_factura_compra' => $data['numeroFacturaCompra']
            ];

            return $this->execute($query, $params);
        }   

        public function registrarDetalleCompra(int $idCompra, array $detalle): mixed {

            $query = "INSERT INTO detalle_compra (
                id_compra,
                id_presentacion,
                id_sede,
                cantidad_detalle_compra,
                costo_unitario_detalle_compra,
                subtotal_detalle_compra
            ) VALUES (:id_compra, :id_presentacion, :id_sede, :cantidad, :costo_unitario, :subtotal)";

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
                $this->recalculo($item);
            }

            return true;
        }

        private function recalculo(array $data){
            $dataProductoPresentacion = $this->dataProductoPresentacion($data['idProductoPresentacion']);
            $idSede = $data['idSede'];

            error_log("[ComprasModel::recalculo] Presentación: " . $dataProductoPresentacion['nombre_presentacion'] .
                      " | Unidad: " . $dataProductoPresentacion['unidad_medida_productos_presentacion']);

            // Diferenciar por tipo de unidad de medida
            if ($dataProductoPresentacion['unidad_medida_productos_presentacion'] === 'gramo') {
                error_log("[ComprasModel::recalculo] ES ESENCIA - Llamando recalculoEsencia");
                $this->recalculoEsencia($data, $dataProductoPresentacion, $idSede);
            } else {
                error_log("[ComprasModel::recalculo] NO ES ESENCIA - Llamando recalculoProductoRegular");
                $this->recalculoProductoRegular($data, $dataProductoPresentacion, $idSede);
            }
        }

        /**
         * Flujo para ESENCIAS: registra en gramos en inventario_esencias y recalcula fórmulas
         */
        private function recalculoEsencia(array $data, array $dataPresentacion, int $idSede): void {
            error_log("[ComprasModel::recalculoEsencia] Iniciando recalculoEsencia");
            error_log("  - Presentación: " . $dataPresentacion['nombre_presentacion']);
            error_log("  - Cantidad comprada: " . $data['cantidad']);
            error_log("  - Precio compra: " . $data['precioCompra']);
            error_log("  - Sede: " . $idSede);

            $dataStockEsencia = $this->dataStockEsencia($data['idProductoPresentacion'], $idSede);

            // Obtener tamaño de presentación: usar campo o extraer del nombre
            $tamanioPresentacion = $dataPresentacion['cantidad_gramos_presentacion'];
            error_log("  - cantidad_gramos_presentacion (BD): " . ($tamanioPresentacion ?? 'NULL'));

            if (!$tamanioPresentacion) {
                // Fallback: extraer gramos del nombre de presentación (ej: "212 MEN - 125G" -> 125)
                $tamanioPresentacion = $this->extraerGramosDelNombre($dataPresentacion['nombre_presentacion']) ?? 1;
                error_log("  - cantidad_gramos_presentacion (PARSED): " . $tamanioPresentacion);
            }

            // Cantidad total de gramos = cantidad_comprada × tamaño_presentacion
            $cantidadGramos = $data['cantidad'] * $tamanioPresentacion;
            error_log("  - Total gramos a registrar: " . $cantidadGramos);

            // Calcular costo por gramo
            $costoPorGramo = $data['precioCompra'] / $cantidadGramos;

            // Crear o actualizar inventario en GRAMOS
            if (!$dataStockEsencia) {
                $registroStockEsencia = [
                    'id_presentacion' => $data['idProductoPresentacion'],
                    'id_sede' => $idSede,
                    'cantidad_gramos' => $cantidadGramos,
                    'costo_por_gramo' => $costoPorGramo
                ];
                $this->registrarStockEsencia($registroStockEsencia);
            } else {
                $nuevoStock = $dataStockEsencia['cantidad_gramos'] + $cantidadGramos;
                $this->actualizarStockEsencia(
                    $data['idProductoPresentacion'],
                    $idSede,
                    $nuevoStock,
                    $costoPorGramo
                );
            }

            // Actualizar precio de presentación (para referencia)
            $nuevoPrecioCompra = $data['precioCompra'];
            $this->actualizarValorCompra($data['idProductoPresentacion'], $nuevoPrecioCompra);

            // DISPARA RECÁLCULO DE TODAS LAS FÓRMULAS QUE USAN ESTA ESENCIA
            // Obtener id_producto de la presentación comprada
            $dataPresentacion = $this->dataProductoPresentacion($data['idProductoPresentacion']);
            if ($dataPresentacion) {
                $this->dispararRecalculoFórmulas($dataPresentacion['id_producto'], $idSede);
            }
        }

        /**
         * Flujo para PRODUCTOS REGULARES (Relojes, Lociones 1.1, Envases, etc)
         */
        private function recalculoProductoRegular(array $data, array $dataPresentacion, int $idSede): void {
            $dataStockSede = $this->dataStockSede($data['idProductoPresentacion'], $idSede);

            // Crear o actualizar inventario en UNIDADES
            if (!$dataStockSede) {
                $registroStockSede = [
                    'id_sede' => $idSede,
                    'stock_sede' => $data['cantidad'],
                    'id_presentacion' => $data['idProductoPresentacion']
                ];
                $this->registrarStockSede($registroStockSede);
            } else {
                $nuevoStock = $dataStockSede['stock_sede'] + $data['cantidad'];
                $this->actualizarStockSede(
                    $data['idProductoPresentacion'],
                    $idSede,
                    $nuevoStock
                );
            }

            // Actualizar precio de compra
            $nuevoPrecioCompra = $data['precioCompra'];
            $this->actualizarValorCompra($data['idProductoPresentacion'], $nuevoPrecioCompra);
        }

        /**
         * Dispara el recálculo de costos de producción de fórmulas
         */
        private function dispararRecalculoFórmulas(int $idProductoEsencia, int $idSede): void {
            try {
                $calculator = new FormulaCalculatorService();
                $calculator->recalcularCostoProduccionPorEsencia($idProductoEsencia, $idSede);
            } catch (Exception $e) {
                // Log error pero no detiene el registro de compra
                error_log("Error al recalcular fórmulas: " . $e->getMessage());
            }
        }


        private function dataProductoPresentacion(int $idProductoPresentacion) {
            $query = "SELECT
                        productos.id_producto,
                        productos.nombre_producto,
                        productos.id_categoria,
                        productos_presentaciones.id_presentacion,
                        productos_presentaciones.nombre_presentacion,
                        productos_presentaciones.precio_compra_presentacion,
                        productos_presentaciones.cantidad_gramos_presentacion,
                        productos_presentaciones.unidad_medida_productos_presentacion,
                        productos_presentaciones.es_preparado_presentacion_producto,
                        productos_presentaciones.id_formula,
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
    