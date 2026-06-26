<?php

    require_once(__DIR__ . '/../core/conexion.php');
    

    class CategoriaModel extends conexion {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM categoria_producto WHERE id_estado = 1 ORDER BY nombre_categoria DESC";
            return $this->select($query);
        }

        public function registrarCategoria(string $nombreCategoria) : bool {
            $query = "INSERT INTO categoria_producto (nombre_categoria) VALUES (:nombre_categoria)";
            $params = [':nombre_categoria' => $nombreCategoria];
            return $this->execute($query, $params);
        }

        public function actualizarCategoria(int $idCategoria, string $nombreCategoria) : bool {
            $query = "UPDATE categoria_producto SET nombre_categoria = :nombre_categoria WHERE id_categoria = :id_categoria";
            $params = [
                ':nombre_categoria' => $nombreCategoria,
                ':id_categoria' => $idCategoria
            ];

            return $this->execute($query, $params);
        }

        public function eliminarCategoria(int $idCategoria) : bool {
            $query = "UPDATE categoria_producto SET id_estado = 2 WHERE id_categoria = :id_categoria";
            $params = [':id_categoria' => $idCategoria];
            return $this->execute($query, $params);
        }

    }
    