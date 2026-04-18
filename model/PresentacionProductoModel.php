<?php

    require_once('../core/conexion.php');
    

    class PresentacionProductoModel extends conexion {

        public function obtenerTodos() : array {
            $query = "SELECT * FROM productos_presentaciones";
            return $this->select($query);
        }

        public function registroPresentacionProducto(array $data) {
            
            $query = "INSERT INTO productos_presentaciones (
                id_producto, 
                nombre_presentacion, 
                codigo_presentacion, 
                id_tipo_producto, 
                img_presentacion) 
            VALUES (:id_producto, :nombre_presentacion, :codigo_presentacion, :id_tipo_producto, :img_presentacion)";

            $params = [
                ':id_producto' => $data['idProducto'],
                ':nombre_presentacion' => $data['nombre'],
                ':codigo_presentacion' => $data['codigo'],
                ':id_tipo_producto' => $data['tipo'],
                ':img_presentacion' => $data['imgPresentacion']
            ];
            return $this->execute($query, $params);            
        }
    }