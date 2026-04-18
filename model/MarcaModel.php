<?php

    require_once('../core/conexion.php');

    class MarcaModel extends conexion {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM marca_producto";
            return $this->select($query);
        }
        
    }
    