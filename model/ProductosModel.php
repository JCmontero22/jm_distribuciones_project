<?php

    require_once('../core/conexion.php');

    class ProductosModel extends conexion
    {

        public function existeProducto(string $codigo) : bool {
            $query = "SELECT COUNT(*) AS total  FROM productos WHERE codigo_producto = :codigo";

            $params = [':codigo' => $codigo];

            $result = $this->select($query, $params);

            return !empty($result) && $result[0]['total'] > 0;
        }

        public function registrarProducto(array $data): mixed {
            $query = "INSERT INTO productos (
                nombre_producto,
                codigo_producto,
                id_categoria,
                id_marca,
                descripcion_producto,
                logo_producto                
            ) VALUES (:nombre, :codigo, :categoria, :marca, :descripcion, :logo)";

            $params = [
                ':nombre' => $data['nombre'],
                ':codigo' => $data['codigo'],
                ':categoria' => $data['categoria'],
                ':marca' => $data['marca'],
                ':descripcion' => $data['descripcion'],
                ':logo' => $data['logoProducto'] ?? null,
            ];
            return $this->execute($query, $params);
        }

        public function registroPresentacionProducto(array $presentacion): mixed {

            $query = "INSERT INTO productos_presentaciones (
                            id_producto,
                            nombre_presentacion,
                            codigo_presentacion,
                            precio_compra_presentacion,
                            precio_venta_presentacion,
                            id_tipo_producto,
                            img_presentacion,
                            unidad_medida_productos_presentacion,
                            id_genero,
                            favorito_presentacion,
                            descripcion_presentacion
                        )
                        VALUES (
                            :id_producto,
                            :nombre_presentacion,
                            :codigo_presentacion,
                            :precio_compra_presentacion,
                            :precio_venta_presentacion,
                            :id_tipo_producto,
                            :img_presentacion,
                            :unidad_medida_productos_presentacion,
                            :id_genero,
                            :favoritoPresentacion,
                            :descripcionPresentacion
                        )";

            $params = [
                ':id_producto' => $presentacion['idProducto'],
                ':nombre_presentacion' => $presentacion['nombrePresentacion'],
                ':codigo_presentacion' => $presentacion['codigoPresentacion'],
                ':precio_compra_presentacion' => $presentacion['precioCompraPresentacion'],
                ':precio_venta_presentacion' => $presentacion['precioVentaPresentacion'],
                ':id_tipo_producto' => $presentacion['tipoProducto'],
                ':img_presentacion' => $presentacion['imgPresentacion'] ?? null,
                ':unidad_medida_productos_presentacion' => $presentacion['unidadMedidaProductosPresentacion'],
                ':id_genero' => $presentacion['idGenero'] ?? null,
                ':favoritoPresentacion' => $presentacion['favoritoPresentacion'] ?? 0,
                ':descripcionPresentacion' => $presentacion['descripcionPresentacion'] ?? null
            ];

            return $this->execute($query, $params);
        }

        public function actualizarPresentacionProducto(array $presentacion): mixed {
            $query = "UPDATE productos_presentaciones SET
                        nombre_presentacion = :nombre_presentacion,
                        codigo_presentacion = :codigo_presentacion,
                        precio_compra_presentacion = :precio_compra_presentacion,
                        precio_venta_presentacion = :precio_venta_presentacion,
                        id_tipo_producto = :id_tipo_producto,
                        unidad_medida_productos_presentacion = :unidad_medida_productos_presentacion,
                        id_genero = :id_genero,
                        favorito_presentacion = :favoritoPresentacion,
                        descripcion_presentacion = :descripcionPresentacion";

            if (!empty($presentacion['imgPresentacion'])) {
                $query .= ", img_presentacion = :img_presentacion";
            }

            $query .= " WHERE id_presentacion = :idPresentacionExistente";

            $params = [
                ':nombre_presentacion' => $presentacion['nombrePresentacion'],
                ':codigo_presentacion' => $presentacion['codigoPresentacion'],
                ':precio_compra_presentacion' => $presentacion['precioCompraPresentacion'],
                ':precio_venta_presentacion' => $presentacion['precioVentaPresentacion'],
                ':id_tipo_producto' => $presentacion['tipoProducto'],
                ':unidad_medida_productos_presentacion' => $presentacion['unidadMedidaProductosPresentacion'],
                ':id_genero' => $presentacion['idGenero'] ?? null,
                ':favoritoPresentacion' => $presentacion['favoritoPresentacion'] ?? 0,
                ':descripcionPresentacion' => $presentacion['descripcionPresentacion'] ?? null,
                ':idPresentacionExistente' => $presentacion['idPresentacionExistente']
            ];

            if (!empty($presentacion['imgPresentacion'])) {
                $params[':img_presentacion'] = $presentacion['imgPresentacion'];
            }

            return $this->execute($query, $params);
        }

        public function obtenerProductos(string $nombreCategoria) :array {
            $query = "SELECT
                            p.id_producto,
                            p.nombre_producto,
                            p.codigo_producto,
                            p.descripcion_producto,
                            p.id_categoria,
                            p.id_marca,
                            p.logo_producto,
                            c.nombre_categoria,
                            m.nombre_marca,
                            g.nombre_genero,
                            pp.id_presentacion,
                            pp.id_genero,
                            pp.img_presentacion,
                            pp.precio_venta_presentacion,
                            pp.precio_compra_presentacion,
                            pp.nombre_presentacion,
                            pp.unidad_medida_productos_presentacion,
                            COALESCE(ie.cantidad_gramos, 0) AS cantidad_gramos,
                            COALESCE(ie.costo_por_gramo, 0) AS costo_por_gramo,
                            COALESCE(iss.stock_sede, 0) AS stock_total
                        FROM productos p
                        LEFT JOIN productos_presentaciones pp ON p.id_producto = pp.id_producto
                        LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                        LEFT JOIN marca_producto m ON p.id_marca = m.id_marca
                        LEFT JOIN genero g ON pp.id_genero = g.id_genero
                        LEFT JOIN inventario_esencias ie ON pp.id_presentacion = ie.id_presentacion
                        LEFT JOIN inventario_sedes iss ON pp.id_presentacion = iss.id_presentacion
                        WHERE c.nombre_categoria = :categoria AND iss.id_sede = 1 AND p.id_estado = 1 AND pp.id_estado = 1";

            $params = [':categoria' => $nombreCategoria];

            return $this->select($query, $params);
        }

        public function obtenerProductosActivos(): array {
            $query = "SELECT
                            p.id_producto,
                            p.nombre_producto,
                            p.codigo_producto,
                            p.id_categoria,
                            p.id_marca,
                            c.nombre_categoria,
                            m.nombre_marca,
                            COUNT(pp.id_presentacion) AS total_presentaciones
                        FROM productos p
                        LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                        LEFT JOIN marca_producto m ON p.id_marca = m.id_marca
                        INNER JOIN productos_presentaciones pp
                            ON p.id_producto = pp.id_producto
                            AND pp.id_estado = 1
                        WHERE p.id_estado = 1
                        GROUP BY
                            p.id_producto,
                            p.nombre_producto,
                            p.codigo_producto,
                            p.id_categoria,
                            p.id_marca,
                            c.nombre_categoria,
                            m.nombre_marca
                        ORDER BY p.nombre_producto";

            return $this->select($query);
        }

        public function obtenerPresentacionesPorProducto(int $idProducto): array {
            $query = "SELECT
                            pp.id_producto,
                            pp.id_presentacion,
                            pp.nombre_presentacion,
                            pp.codigo_presentacion,
                            pp.precio_compra_presentacion,
                            pp.precio_venta_presentacion,
                            pp.img_presentacion,
                            COALESCE(ie.cantidad_gramos, 0) AS stock_gramos,
                            COALESCE(ie.costo_por_gramo, 0) AS costo_por_gramo,
                            COALESCE(iss.stock_sede, 0) AS stock_total
                        FROM productos_presentaciones pp
                        LEFT JOIN inventario_esencias ie ON pp.id_presentacion = ie.id_presentacion
                        LEFT JOIN inventario_sedes iss ON pp.id_presentacion = iss.id_presentacion AND iss.id_sede = 1
                        WHERE pp.id_producto = :id_producto AND pp.id_estado = 1
                        ORDER BY pp.nombre_presentacion ";

            return $this->select($query, [':id_producto' => $idProducto]);
        }

        public function obtenerProductoPorId(int $id): array {
            $query = "SELECT id_producto, nombre_producto, codigo_producto, id_categoria, id_marca, descripcion_producto
                      FROM productos WHERE id_producto = :id LIMIT 1";
            $result = $this->select($query, [':id' => $id]);
            return $result[0] ?? [];
        }

        public function editarProducto(int $id, array $data): bool {
            $query = "UPDATE productos SET
                        nombre_producto = :nombre,
                        codigo_producto = :codigo,
                        id_categoria    = :categoria,
                        id_marca        = :marca,
                                                descripcion_producto = :descripcion
                      WHERE id_producto = :id";
            $params = [
                ':nombre'      => $data['nombre'],
                ':codigo'      => $data['codigo'],
                ':categoria'   => $data['categoria'],
                ':marca'       => $data['marca'],
                ':descripcion' => $data['descripcion'],
                ':id'          => $id
                
            ];
            return $this->execute($query, $params);
        }

        public function eliminarProducto(int $id): bool {
            $query = "UPDATE productos SET id_estado = 2 WHERE id_producto = :id";
            return $this->execute($query, [':id' => $id]);
        }

        public function eliminarPresentacion(int $idPresentacion): bool {
            $query = "UPDATE productos_presentaciones SET id_estado = 2 WHERE id_presentacion = :id";
            return $this->execute($query, [':id' => $idPresentacion]);
        }

        public function obtenerEsencias(): array {
            $query = "SELECT
                            p.id_producto,
                            p.nombre_producto,
                            p.codigo_producto,
                            p.descripcion_producto,
                            p.logo_producto,
                            m.nombre_marca,
                            MAX(pp.img_presentacion) AS img_presentacion,
                            SUM(COALESCE(ie.cantidad_gramos, 0)) AS cantidad_gramos,
                            CASE
                                WHEN SUM(COALESCE(ie.cantidad_gramos, 0)) > 0
                                THEN SUM(ie.cantidad_gramos * ie.costo_por_gramo) / SUM(ie.cantidad_gramos)
                                ELSE 0
                            END AS costo_por_gramo
                        FROM productos p
                        LEFT JOIN productos_presentaciones pp ON p.id_producto = pp.id_producto
                        LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                        LEFT JOIN marca_producto m ON p.id_marca = m.id_marca
                        LEFT JOIN inventario_esencias ie ON pp.id_presentacion = ie.id_presentacion
                        WHERE c.nombre_categoria = 'Esencias' AND p.id_estado = 1 AND ie.id_sede = 1
                        GROUP BY p.id_producto, p.nombre_producto, p.descripcion_producto, p.logo_producto, m.nombre_marca
                        ORDER BY p.nombre_producto";

            return $this->select($query);
        }

        public function obtenerProductosCompra() : array {
            $query = "SELECT
                            p.id_producto,
                            p.id_categoria,
                            p.nombre_producto,
                            pp.id_presentacion,
                            pp.nombre_presentacion,
                            pp.img_presentacion,
                            pp.precio_compra_presentacion,
                            ct.nombre_categoria
                        FROM productos p
                        LEFT JOIN productos_presentaciones pp ON p.id_producto = pp.id_producto
                        LEFT JOIN categoria_producto ct ON p.id_categoria = ct.id_categoria
                        WHERE p.id_categoria IN (1, 3, 4, 5) AND p.id_estado = 1";

            return $this->select($query);
        }

        public function obtenerIdsPresentacionesPorMarca(int $idMarca): array {
            $query = "SELECT pp.id_presentacion
                      FROM productos_presentaciones pp
                      LEFT JOIN productos p ON pp.id_producto = p.id_producto
                      WHERE p.id_marca = :idMarca AND pp.id_estado = 1";

            $resultado = $this->select($query, [':idMarca' => $idMarca]);
            return array_column($resultado, 'id_presentacion');
        }

        public function obtenerIdsPresentacionesPorProductoYGenero(int $idProducto, int $idGenero): array {
            $query = "SELECT pp.id_presentacion
                      FROM productos_presentaciones pp
                      WHERE pp.id_producto = :idProducto
                      AND pp.id_genero = :idGenero
                      AND pp.id_estado = 1";

            $resultado = $this->select($query, [
                ':idProducto' => $idProducto,
                ':idGenero' => $idGenero
            ]);
            return array_column($resultado, 'id_presentacion');
        }

        public function obtenerIdsPresentacionesPorGenero(int $idGenero): array {
            $query = "SELECT pp.id_presentacion
                      FROM productos_presentaciones pp
                      WHERE pp.id_genero = :idGenero
                      AND pp.id_estado = 1";

            $resultado = $this->select($query, [':idGenero' => $idGenero]);
            return array_column($resultado, 'id_presentacion');
        }

        public function obtenerIdsTodosPresentaciones(): array {
            $query = "SELECT pp.id_presentacion
                      FROM productos_presentaciones pp
                      WHERE pp.id_estado = 1";

            $resultado = $this->select($query);
            return array_column($resultado, 'id_presentacion');
        }

        public function obtenerIdsPresentacionesPorDescuento(int $idDescuento): array {
            $query = "SELECT pp.id_presentacion
                      FROM productos_presentaciones pp
                      WHERE pp.id_descuento = :idDescuento";

            $resultado = $this->select($query, [':idDescuento' => $idDescuento]);
            return array_column($resultado, 'id_presentacion');
        }

    }
    
