<?php 

    require_once(__DIR__ . '/../core/conexion.php');

    class PermisosModel extends conexion{

        public function registroPermisos(array $data) {
            $quyery = "INSERT INTO permisos (nombre_permiso, descripcion_permiso) VALUES (:nombre, :descripcion)";
            $params = [
                ':nombre' => $data['nombrePermiso'],
                ':descripcion' => $data['descripcionPermiso']
            ];
            return $this->execute($quyery, $params);
        }

        public function obtenerPermisos() : array {
            $query = "SELECT * FROM permisos WHERE id_estado = 1";
            return $this->select($query);
        }

        public function actualizarPermiso(array $data) : bool {
            $query = "UPDATE permisos SET nombre_permiso = :nombre, descripcion_permiso = :descripcion WHERE id_permiso = :id";
            $params = [
                ':nombre' => $data['nombrePermiso'],
                ':descripcion' => $data['descripcionPermiso'],
                ':id' => $data['idPermiso']
            ];
            return $this->execute($query, $params);
        }
        
        public function eliminarPermiso(int $idPermiso) : bool {
            $query = "UPDATE permisos SET id_estado = 2 WHERE id_permiso = :id";
            $params = [':id' => $idPermiso];
            return $this->execute($query, $params);
        }
    }
    