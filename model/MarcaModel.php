<?php 

    require_once('../core/conexion.php');

    class MarcaModel extends conexion{
    
        public function getMarcas() {
            
            $query = "SELECT * FROM marca_distribuciones";
            return $this->select($query);            
            
        }
        
    }
    