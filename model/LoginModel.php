<?php

require_once(__DIR__ . '/../core/conexion.php');

class LoginModel extends conexion {

    /**
     * Obtener usuario por nombre de usuario
     * @param string $usuario Nombre de usuario
     * @return array|null Datos del usuario o null
     */
    public function obtenerPorUsuario($usuario) {
        try {
            $query = "
                SELECT
                    u.id_usuario,
                    u.nombre_usuario,
                    u.user_usuario,
                    u.pass_usuario,
                    u.id_perfil,
                    u.id_estado,
                    pu.nombre_perfil_usuario,
                    pu.id_perfil_usuario,
                    e.nombre_estado
                FROM usuario u
                LEFT JOIN perfil_usuario pu ON u.id_perfil = pu.id_perfil_usuario
                LEFT JOIN estado e ON u.id_estado = e.id_estado
                WHERE u.user_usuario = :usuario
                LIMIT 1
            ";

            $params = [':usuario' => $usuario];
            $resultado = $this->select($query, $params);

            if (empty($resultado)) {
                return null;
            }

            return $resultado[0];
        } catch (Exception $e) {
            throw new Exception("Error en LoginModel::obtenerPorUsuario: " . $e->getMessage());
        }
    }

    /**
     * Registrar intento de login fallido (LOG)
     * @param string $usuario
     * @param string $motivo
     */
    public function registrarIntentoFallido($usuario, $motivo) {
        try {
            // TODO: Guardar en tabla de auditoría cuando esté implementada
            // Por ahora solo logging en archivo
            error_log("[LOGIN FALLIDO] Usuario: {$usuario}, Motivo: {$motivo}", 3, __DIR__ . '/../logs/login.log');
        } catch (Exception $e) {
            error_log("Error registrando intento fallido: " . $e->getMessage(), 3, __DIR__ . '/../logs/login.log');
        }
    }

    /**
     * Registrar login exitoso (LOG)
     * @param int $idUsuario
     */
    public function registrarLoginExitoso($idUsuario) {
        try {
            // TODO: Guardar en tabla de auditoría cuando esté implementada
            error_log("[LOGIN EXITOSO] ID Usuario: {$idUsuario}, Timestamp: " . date('Y-m-d H:i:s'), 3, __DIR__ . '/../logs/login.log');
        } catch (Exception $e) {
            error_log("Error registrando login exitoso: " . $e->getMessage(), 3, __DIR__ . '/../logs/login.log');
        }
    }
}
