<?php 

    require_once(__DIR__ . '/../core/conexion.php');

    class UsuariosModel extends conexion 
    {
        public function obtenerUsuarios() : array {
            $query = "SELECT u.id_usuario, u.nombre_usuario, u.user_usuario, u.pass_usuario, p.nombre_perfil_usuario, e.nombre_estado
                      FROM usuario u
                      JOIN perfil_usuario p ON u.id_perfil = p.id_perfil_usuario
                      JOIN estado e ON u.id_estado = e.id_estado
                      WHERE u.id_estado = 1";
            return $this->select($query);
        }

        public function obtenerPorUsuario(string $usuario) : ?array {
            $query = "SELECT u.id_usuario, u.nombre_usuario, u.user_usuario, u.pass_usuario,
                            u.id_perfil, u.id_estado, p.nombre_perfil_usuario, e.nombre_estado
                      FROM usuario u
                      LEFT JOIN perfil_usuario p ON u.id_perfil = p.id_perfil_usuario
                      LEFT JOIN estado e ON u.id_estado = e.id_estado
                      WHERE u.user_usuario = :usuario";
            $params = [':usuario' => $usuario];
            $result = $this->select($query, $params);
            return isset($result[0]) ? $result[0] : null;
        }

        public function registroUsuario(array $data) {
            $query = "INSERT INTO usuario (nombre_usuario, user_usuario, pass_usuario, id_perfil) VALUES (:nombre, :usuario, :password, :perfil)";
            $params = [
                ':nombre' => $data['nombreUsuario'],
                ':usuario' => $data['userUsuario'],
                ':password' => $data['passwordUsuario'],
                ':perfil' => $data['perfilUsuario']
            ];
            return $this->execute($query, $params);
        }

        public function actualizarUsuario(array $data) : bool {
            // Si no se proporciona contraseña, no la actualizar
            if (empty($data['passwordUsuario'])) {
                $query = "UPDATE usuario SET nombre_usuario = :nombre, user_usuario = :usuario, id_perfil = :perfil WHERE id_usuario = :id";
                $params = [
                    ':nombre' => $data['nombreUsuario'],
                    ':usuario' => $data['userUsuario'],
                    ':perfil' => $data['perfilUsuario'],
                    ':id' => $data['idUsuario']
                ];
            } else {
                // Si se proporciona, actualizar incluyendo la contraseña
                $query = "UPDATE usuario SET nombre_usuario = :nombre, user_usuario = :usuario, pass_usuario = :password, id_perfil = :perfil WHERE id_usuario = :id";
                $params = [
                    ':nombre' => $data['nombreUsuario'],
                    ':usuario' => $data['userUsuario'],
                    ':password' => $data['passwordUsuario'],
                    ':perfil' => $data['perfilUsuario'],
                    ':id' => $data['idUsuario']
                ];
            }
            return $this->execute($query, $params);
        }

        public function eliminarUsuario(int $idUsuario) : bool {
            $query = "UPDATE usuario SET id_estado = 2 WHERE id_usuario = :id";
            $params = [':id' => $idUsuario];
            return $this->execute($query, $params);
        }

        public function existeUsuario(string $userUsuario) : bool {
            $query = "SELECT COUNT(*) as count FROM usuario WHERE user_usuario = :usuario";
            $params = [':usuario' => $userUsuario];
            $result = $this->select($query, $params);
            return isset($result[0]['count']) && $result[0]['count'] > 0;
        }
    }
    