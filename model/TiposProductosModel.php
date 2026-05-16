<?php

    require_once('../core/conexion.php');
    

    class TiposProductosModel extends conexion {
    
        public function obtenerTiposProductos() : array {
            $query = "SELECT * FROM tipo_producto";
            return $this->select($query);
        }

    }
    