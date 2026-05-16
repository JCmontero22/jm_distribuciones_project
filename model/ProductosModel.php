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
                es_preparado_presentacion_producto
            ) VALUES (:id_producto, :nombre_presentacion, :codigo_presentacion, :precio_compra_presentacion, :precio_venta_presentacion, :id_tipo_producto, :img_presentacion, :unidad_medida_productos_presentacion, :es_preparado_presentacion_producto)";

            $params = [
                ':id_producto' => $presentacion['idProducto'],
                ':nombre_presentacion' => $presentacion['nombrePresentacion'],
                ':codigo_presentacion' => $presentacion['codigoPresentacion'],
                ':precio_compra_presentacion' => $presentacion['precioCompraPresentacion'],
                ':precio_venta_presentacion' => $presentacion['precioVentaPresentacion'],
                ':id_tipo_producto' => $presentacion['tipoProducto'],
                ':img_presentacion' => $presentacion['imgPresentacion'] ?? null,
                ':unidad_medida_productos_presentacion' => $presentacion['unidadMedidaProductosPresentacion'],
                ':es_preparado_presentacion_producto' => $presentacion['esPreparadoPresentacionProducto']
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
                            pp.img_presentacion, 
                            st.stock_actual, 
                            pp.precio_venta_presentacion, 
                            pp.precio_compra_presentacion
                        FROM productos p
                        LEFT JOIN productos_presentaciones pp ON p.id_producto = pp.id_producto
                        LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                        LEFT JOIN marca_producto m ON p.id_marca = m.id_marca
                        LEFT JOIN genero g ON p.id_genero = g.id_genero
                        LEFT JOIN inventario_sedes st ON pp.id_presentacion = st.id_presentacion
                        WHERE p.id_categoria = (SELECT id_categoria FROM categoria_producto WHERE nombre_categoria = :categoria)
                        GROUP BY p.id_producto
                        ORDER BY p.nombre_producto ASC";

            $params = [':categoria' => $nombreCategoria];

            return $this->select($query, $params);
        }


    }
    