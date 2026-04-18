<?php

    require_once('../core/conexion.php');
    require_once('../contracts/ICatalogModel.php');

    class CategoriaModel extends conexion implements ICatalogModel {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM categoria_producto";
            return $this->select($query);
        }

    }
    