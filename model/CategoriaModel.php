<?php

    require_once(__DIR__ . '/../core/conexion.php');
    

    class CategoriaModel extends conexion {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM categoria_producto";
            return $this->select($query);
        }

    }
    