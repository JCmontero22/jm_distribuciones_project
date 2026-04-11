<?php 
    
    require_once('../core/conexion.php');

    class ProductoModel extends conexion
    {

        public function existeProducto($codigo){
            $query = "SELECT COUNT(*) AS total  FROM productos WHERE codigo_producto = :codigo";
            
            $params = [':codigo' => $codigo];
            
            $result = $this->select($query, $params);
            
            return !empty($result) && $result[0]['total'] > 0;
        }

        public function registroProducto(array $data) {
            $query = "INSERT INTO productos (nombre_producto, codigo_producto, id_categoria, id_marca, descripcion_producto, id_genero, 
            img_principal_producto) VALUES (:nombre, :codigo, :categoria, :marca, :descripcion, :genero, :imagen)";

            $params = [
                ':nombre' => $data['nombre'],
                ':codigo' => $data['codigo'],
                ':categoria' => $data['categoria'],
                ':marca' => $data['marca'],
                ':descripcion' => $data['descripcion'],
                ':genero' => $data['genero'],
                ':imagen' => $data['imagen']
            ];
            return $this->execute($query, $params);            
        }

       /*  public function registroDetalleProducto() {
            $query = "INSERT INTO detalle_producto_distribuciones"
        } */

        public function productos() :array {
            $query = "SELECT p.id_producto, p.nombre_producto, p.codigo_producto, p.descripcion_producto, c.nombre_categoria, m.nombre_marca, g.nombre_genero, p.img_principal_producto 
                        FROM productos p
                        LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                        LEFT JOIN marca_producto m ON p.id_marca = m.id_marca
                        LEFT JOIN genero g ON p.id_genero = g.id_genero";
            
            return $this->select($query);
        }
    }
    