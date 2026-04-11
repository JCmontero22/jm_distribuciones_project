<?php 

    require_once('../core/conexion.php');
    
    class CategoriaModel extends conexion{
    
        public function getCategorias() {
            
            $query = "SELECT * FROM categoria_producto";
            return $this->select($query);            
            
        }

    }
    