<?php

    require_once('../core/conexion.php');
    require_once('../contracts/ICatalogModel.php');

    class MarcaModel extends conexion implements ICatalogModel {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM marca_producto";
            return $this->select($query);
        }
        
    }
    