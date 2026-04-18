<?php

    require_once('../core/conexion.php');
    

    class GeneroModel extends conexion {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM genero";
            return $this->select($query);
        }

    }
    