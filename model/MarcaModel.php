<?php

    require_once(__DIR__ . '/../core/conexion.php');

    class MarcaModel extends conexion {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM marca_producto";
            return $this->select($query);
        }

        public function registrarMarca(string $nombreMarca, ?string $imagenMarca) : bool {
            $query = "INSERT INTO marca_producto (nombre_marca, img_marca) VALUES (:nombreMarca, :imagenMarca)";
            $params = [
                ':nombreMarca' => $nombreMarca,
                ':imagenMarca' => $imagenMarca
            ];
            return $this->execute($query, $params);
        }
        
    }
    