<?php

    require_once('../core/conexion.php');
    

    class SedesModel extends conexion {
    
        public function obtenerSedes() : array {
            $query = "SELECT * FROM sedes";
            return $this->select($query);
        }

    }
    