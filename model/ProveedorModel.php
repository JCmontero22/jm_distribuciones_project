<?php 

    require_once('../core/conexion.php');

    class ProveedorModel extends conexion {
        
        public function obtenerProveedoresSelect() :array{
            
            $query = "SELECT id_proveedor, nombre_proveedor, contacto_proveedor, telefono_proveedor FROM proveedor";
            return $this->select($query);
        }

        public function registrarProveedor($data){
            $query = "INSERT INTO proveedor (nombre_proveedor, contacto_proveedor, telefono_proveedor) VALUES (:nombre, :contacto, :telefono)";
            $params = [
                ':nombre' => $data['nombre'],
                ':contacto' => $data['contacto'],
                ':telefono' => $data['telefono']
            ];
            return $this->execute($query, $params);
        }
    }
    