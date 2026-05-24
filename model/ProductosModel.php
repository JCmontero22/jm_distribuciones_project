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
                id_genero
            ) VALUES (:nombre, :codigo, :categoria, :marca, :descripcion, :genero)";

            $params = [
                ':nombre' => $data['nombre'],
                ':codigo' => $data['codigo'],
                ':categoria' => $data['categoria'],
                ':marca' => $data['marca'],
                ':descripcion' => $data['descripcion'],
                ':genero' => $data['genero']
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
                es_preparado_presentacion_producto,
                id_formula
            ) VALUES (:id_producto, :nombre_presentacion, :codigo_presentacion, :precio_compra_presentacion, :precio_venta_presentacion, :id_tipo_producto, :img_presentacion, :unidad_medida_productos_presentacion, :es_preparado_presentacion_producto, :id_formula)";

            $params = [
                ':id_producto' => $presentacion['idProducto'],
                ':nombre_presentacion' => $presentacion['nombrePresentacion'],
                ':codigo_presentacion' => $presentacion['codigoPresentacion'],
                ':precio_compra_presentacion' => $presentacion['precioCompraPresentacion'],
                ':precio_venta_presentacion' => $presentacion['precioVentaPresentacion'],
                ':id_tipo_producto' => $presentacion['tipoProducto'],
                ':img_presentacion' => $presentacion['imgPresentacion'] ?? null,
                ':unidad_medida_productos_presentacion' => $presentacion['unidadMedidaProductosPresentacion'],
                ':es_preparado_presentacion_producto' => $presentacion['esPreparadoPresentacionProducto'],
                ':id_formula' => $presentacion['idFormula'] ?? null
            ];

            return $this->execute($query, $params);
        }

        public function obtenerProductos(string $nombreCategoria) :array {
            $query = "SELECT
                            p.id_producto,
                            p.nombre_producto,
                            p.codigo_producto,
                            p.descripcion_producto,
                            c.nombre_categoria,
                            m.nombre_marca,
                            g.nombre_genero,
                            pp.id_presentacion,
                            pp.img_presentacion,
                            pp.precio_venta_presentacion,
                            pp.precio_compra_presentacion,
                            pp.nombre_presentacion,
                            pp.unidad_medida_productos_presentacion,
                            COALESCE(ie.cantidad_gramos, 0) AS cantidad_gramos,
                            COALESCE(ie.costo_por_gramo, 0) AS costo_por_gramo
                        FROM productos p
                        LEFT JOIN productos_presentaciones pp ON p.id_producto = pp.id_producto
                        LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                        LEFT JOIN marca_producto m ON p.id_marca = m.id_marca
                        LEFT JOIN genero g ON p.id_genero = g.id_genero
                        LEFT JOIN inventario_esencias ie ON pp.id_presentacion = ie.id_presentacion
                        WHERE c.nombre_categoria = :categoria";

            $params = [':categoria' => $nombreCategoria];

            return $this->select($query, $params);
        }

        public function obtenerProductosCompra() : array {
            $query = "SELECT
                            p.id_producto,
                            p.nombre_producto,
                            pp.id_presentacion,
                            pp.nombre_presentacion,
                            pp.img_presentacion,
                            pp.precio_compra_presentacion,
                            ct.nombre_categoria
                        FROM productos p
                        LEFT JOIN productos_presentaciones pp ON p.id_producto = pp.id_producto
                        LEFT JOIN categoria_producto ct ON p.id_categoria = ct.id_categoria
                        WHERE p.id_categoria IN (1, 3, 4, 5)";

            return $this->select($query);
        }

    }
    