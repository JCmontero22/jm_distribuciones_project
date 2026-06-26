<?php

    require_once(__DIR__ . '/../core/conexion.php');
    

    class TiposProductosModel extends conexion {
    
        public function obtenerTiposProductos() : array {
            $query = "SELECT * FROM tipo_producto WHERE id_estado = 1 ORDER BY id_tipo_producto DESC";

            return $this->select($query);
        }

        public function crearTipoProducto(string $descripcionTipoProducto) : bool {
            $query = "INSERT INTO tipo_producto (descripcion_tipo_producto) VALUES (:descripcion)";
            $params = [
                ':descripcion' => $descripcionTipoProducto
            ];

            return $this->execute($query, $params);
        }

        public function updateTipoProducto(int $idTipoProducto, string $descripcionTipoProducto) : bool {
            $query = "UPDATE tipo_producto SET descripcion_tipo_producto = :descripcion WHERE id_tipo_producto = :id";
            $params = [
                ':descripcion' => $descripcionTipoProducto,
                ':id' => $idTipoProducto
            ];

            return $this->execute($query, $params);
        }

        public function deleteTipoProducto(int $idTipoProducto) : bool {
            $query = "UPDATE tipo_producto SET id_estado = 2 WHERE id_tipo_producto = :id";
            $params = [
                ':id' => $idTipoProducto
            ];

            return $this->execute($query, $params);
        }



    }
    