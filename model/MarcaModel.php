<?php

    require_once(__DIR__ . '/../core/conexion.php');

    class MarcaModel extends conexion {
    
        public function obtenerTodos() : array {
            $query = "SELECT * FROM marca_producto where id_estado = 1";
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

        public function obtenerMarcaPorID(int $idMarca): ?array {
            $query = "SELECT * FROM marca_producto WHERE id_marca = :idMarca";
            $params = [':idMarca' => $idMarca];
            $resultado = $this->select($query, $params);
            return !empty($resultado) ? [$resultado[0]] : null;
        }

        public function actualizarMarca(int $idMarca, string $nombreMarca, string $imagenMarca) : bool {
            $query = "UPDATE marca_producto SET nombre_marca = :nombreMarca, img_marca = :imagenMarca WHERE id_marca = :idMarca";
            $params = [
                ':idMarca' => $idMarca,
                ':nombreMarca' => $nombreMarca,
                ':imagenMarca' => $imagenMarca
            ];
            return $this->execute($query, $params);
        }

        public function eliminarMarca(int $idMarca) : bool {
            $query = "UPDATE marca_producto SET id_estado = 2 WHERE id_marca = :idMarca";
            $params = [':idMarca' => $idMarca];
            return $this->execute($query, $params);
        }

    }
