<?php 

    require_once(__DIR__ . '/../core/conexion.php');

    class PerfilUsuarioModel extends conexion{
        
        public function obtenerPerfiles() : array {
            $query = "SELECT * FROM perfil_usuario WHERE id_estado = 1";
            return $this->select($query);
        }

        public function registroPerfil(array $data) {
            $query = "INSERT INTO perfil_usuario (nombre_perfil_usuario, descripcion_perfil_usuario) VALUES (:nombre, :descripcion)";
            $params = [
                ':nombre' => $data['nombrePerfil'],
                ':descripcion' => $data['descripcionPerfil']
            ];
            return $this->execute($query, $params);
        }

        public function actualizarPerfil(array $data) : bool {
            $query = "UPDATE perfil_usuario SET nombre_perfil_usuario = :nombre, descripcion_perfil_usuario = :descripcion WHERE id_perfil_usuario = :id";
            $params = [
                ':nombre' => $data['nombrePerfil'],
                ':descripcion' => $data['descripcionPerfil'],
                ':id' => $data['idPerfil']
            ];
            return $this->execute($query, $params);
        }
        
        public function eliminarPerfil(int $idPerfil) : bool {
            $query = "UPDATE perfil_usuario SET id_estado = 2 WHERE id_perfil_usuario = :id";
            $params = [':id' => $idPerfil];
            return $this->execute($query, $params);
        }

        // Relación Perfil - Permisos
        public function obtenerPerfilPermisos() : array {
            $query = "
                SELECT
                    pu.id_perfil_usuario,
                    pu.nombre_perfil_usuario,
                    COALESCE(GROUP_CONCAT(p.nombre_permiso SEPARATOR ', '), 'Sin permisos asignados') AS permisos_asignados,
                    COUNT(pp.id_permiso) AS cantidad_permisos
                FROM perfil_usuario pu
                LEFT JOIN perfil_usuario_permisos pp ON pu.id_perfil_usuario = pp.id_perfil_usuario
                LEFT JOIN permisos p ON pp.id_permiso = p.id_permiso
                WHERE pu.id_estado = 1
                GROUP BY pu.id_perfil_usuario, pu.nombre_perfil_usuario
            ";
            return $this->select($query);
        }

        public function obtenerPermisosDelPerfil(int $idPerfil) : array {
            $query = "
                SELECT p.* FROM permisos p
                INNER JOIN perfil_usuario_permisos pp ON p.id_permiso = pp.id_permiso
                WHERE pp.id_perfil_usuario = :idPerfil
            ";
            $params = [':idPerfil' => $idPerfil];
            return $this->select($query, $params);
        }

        public function asignarPermisosAlPerfil(int $idPerfil, array $permisos) : bool {
            foreach ($permisos as $idPermiso) {
                $query = "INSERT INTO perfil_usuario_permisos (id_perfil_usuario, id_permiso) VALUES (:idPerfil, :idPermiso)";
                $params = [
                    ':idPerfil' => $idPerfil,
                    ':idPermiso' => (int)$idPermiso
                ];
                if (!$this->execute($query, $params)) {
                    return false;
                }
            }
            return true;
        }

        public function eliminarPermisosDelPerfil(int $idPerfil) : bool {
            $query = "DELETE FROM perfil_usuario_permisos WHERE id_perfil_usuario = :idPerfil";
            $params = [':idPerfil' => $idPerfil];
            return $this->execute($query, $params);
        }
    }
