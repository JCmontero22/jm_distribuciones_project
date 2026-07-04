<?php

require_once(__DIR__ . '/../model/LoginModel.php');
require_once(__DIR__ . '/../core/Logger.php');

class LoginService {
    private $loginModel;

    public function __construct(LoginModel $loginModel) {
        $this->loginModel = $loginModel;
    }

    /**
     * Validar credenciales de usuario
     * @param string $usuario Nombre de usuario
     * @param string $password Contraseña sin encriptar
     * @return array|null Datos del usuario si es válido, null si no
     */
    public function validarCredenciales($usuario, $password) {
        try {
            // ===== LOG: Búsqueda del usuario =====
            Logger::info("LOGIN: Buscando usuario: {$usuario}");

            // Obtener usuario de la BD
            $usuarioData = $this->loginModel->obtenerPorUsuario($usuario);

            if (!$usuarioData) {
                $motivo = "Usuario no existe";
                Logger::warning("LOGIN FALLIDO: {$motivo} - {$usuario}");
                $this->loginModel->registrarIntentoFallido($usuario, $motivo);
                return null;
            }

            // ===== LOG: Usuario encontrado =====
            Logger::info("LOGIN: Usuario encontrado - ID: {$usuarioData['id_usuario']}, Estado: {$usuarioData['nombre_estado']}");

            // Verificar estado del usuario
            if ($usuarioData['id_estado'] != 1) {
                $motivo = "Usuario inactivo (Estado: {$usuarioData['nombre_estado']})";
                Logger::warning("LOGIN FALLIDO: {$motivo} - {$usuario}");
                $this->loginModel->registrarIntentoFallido($usuario, $motivo);
                return null;
            }

            // ===== LOG: Validando contraseña =====
            Logger::info("LOGIN: Validando contraseña para {$usuario}");
            Logger::debug("Hash almacenado: " . substr($usuarioData['pass_usuario'], 0, 20) . "...");

            // Verificar contraseña
            if (!password_verify($password, $usuarioData['pass_usuario'])) {
                $motivo = "Contraseña incorrecta";
                Logger::warning("LOGIN FALLIDO: {$motivo} - {$usuario}");
                Logger::debug("Password enviada no coincide con hash almacenado");
                $this->loginModel->registrarIntentoFallido($usuario, $motivo);
                return null;
            }

            // ===== LOG: Login exitoso =====
            Logger::info("LOGIN EXITOSO: {$usuario} (ID: {$usuarioData['id_usuario']})");
            $this->loginModel->registrarLoginExitoso($usuarioData['id_usuario']);

            // Retornar datos del usuario
            return [
                'id_usuario' => $usuarioData['id_usuario'],
                'nombre_usuario' => $usuarioData['nombre_usuario'],
                'user_usuario' => $usuarioData['user_usuario'],
                'id_perfil_usuario' => $usuarioData['id_perfil_usuario'],
                'nombre_perfil_usuario' => $usuarioData['nombre_perfil_usuario'],
                'id_estado' => $usuarioData['id_estado'],
                'nombre_estado' => $usuarioData['nombre_estado']
            ];

        } catch (Exception $e) {
            $motivo = "Error en validación: " . $e->getMessage();
            Logger::error("LOGIN ERROR: {$motivo} - {$usuario}", $e);
            $this->loginModel->registrarIntentoFallido($usuario, $motivo);
            return null;
        }
    }
}
