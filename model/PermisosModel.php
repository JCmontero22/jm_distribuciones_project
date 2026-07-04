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

        /**
         * Obtener permisos de un perfil de usuario
         * @param int $idPerfil ID del perfil
         * @return array Nombres de los permisos
         */
        public function obtenerPermisosDelPerfil(int $idPerfil) : array {
            $query = "
                SELECT nombre_permiso FROM permisos p
                INNER JOIN perfil_usuario_permisos pp ON p.id_permiso = pp.id_permiso
                WHERE pp.id_perfil_usuario = :idPerfil AND p.id_estado = 1
            ";
            $params = [':idPerfil' => $idPerfil];
            return $this->select($query, $params);
        }

        /**
         * Verificar si un usuario (por su perfil) tiene un permiso específico
         * @param int $idPerfil ID del perfil
         * @param string $nombrePermiso Nombre del permiso a validar
         * @return bool true si tiene el permiso
         */
        public function tienePermiso(int $idPerfil, string $nombrePermiso) : bool {
            $query = "
                SELECT COUNT(*) as count FROM permisos p
                INNER JOIN perfil_usuario_permisos pp ON p.id_permiso = pp.id_permiso
                WHERE pp.id_perfil_usuario = :idPerfil
                AND p.nombre_permiso = :nombre
                AND p.id_estado = 1
            ";
            $params = [
                ':idPerfil' => $idPerfil,
                ':nombre' => $nombrePermiso
            ];
            $result = $this->select($query, $params);
            return isset($result[0]['count']) && $result[0]['count'] > 0;
        }

        /**
         * Verificar si existe un permiso en la BD
         * @param string $nombrePermiso Nombre del permiso
         * @return bool true si existe
         */
        public function existePermiso(string $nombrePermiso) : bool {
            $query = "SELECT id_permiso FROM permisos WHERE nombre_permiso = :nombre AND id_estado = 1";
            $params = [':nombre' => $nombrePermiso];
            $result = $this->select($query, $params);
            return !empty($result);
        }
    }
    