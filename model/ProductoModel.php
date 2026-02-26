<?php 
    
    require_once('../core/conexion.php');

    class ProductoModel extends conexion
    {
        public function registoProducto(array $data) {
            $query = "INSERT INTO productos_distribuciones (nombre_producto, codigo_producto, id_categoria, id_marca, img_producto) VALUES (:nombre, :codigo, :categoria, :marca, :imagen)";

            $params = [
                ':nombre' => $data['nombre'],
                ':codigo' => $data['codigo'],
                ':categoria' => $data['categoria'],
                ':marca' => $data['marca'],
                ':imagen' => $data['imagen']
            ];
            return $this->execute($query, $params);            
        
        }

        public function existeProducto($codigo){
            $query = "SELECT COUNT(*) AS total  FROM productos_distribuciones WHERE codigo_producto = :codigo";
            
            $params = [':codigo' => $codigo];
            
            $result = $this->select($query, $params);
            
            return !empty($result) && $result[0]['total'] > 0;
        }
    }
    