<?php 

    require_once('../core/conexion.php');

    class ProveedorModel extends conexion {
        
        public function proveedores() :array{
            
            $query = "SELECT id_proveedor, nombre_proveedor, contacto_proveedor, telefono_proveedor FROM proveedor";
            return $this->select($query);
        }
    }
    