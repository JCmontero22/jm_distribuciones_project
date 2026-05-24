<?php

    require_once(__DIR__ . '/../core/conexion.php');

    class MarcaModel extends conexion {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM marca_producto";
            return $this->select($query);
        }
        
    }
    