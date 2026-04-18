<?php

    require_once('../core/conexion.php');
    require_once('../contracts/ICatalogModel.php');

    class GeneroModel extends conexion implements ICatalogModel {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM genero";
            return $this->select($query);
        }

    }
    