<?php

require_once(__DIR__ . '/../core/response.php');
require_once(__DIR__ . '/../core/Logger.php');
require_once(__DIR__ . '/../helper/utils.php');

class LoginController {
    private $loginService;

    public function __construct(LoginService $loginService) {
        $this->loginService = $loginService;
    }

    /**
     * Validar credenciales y retornar datos del usuario
     */
    public function validarLogin(array $data) {
        try {
            // Validar campos requeridos
            if (!isset($data['usuario']) || !isset($data['password'])) {
                Logger::warning("LOGIN: Campos requeridos faltantes");
                return response::error('Usuario y contraseña son obligatorios');
            }

            $usuario = trim($data['usuario']);
            $password = $data['password'];

            // Validaciones de entrada
            if (empty($usuario) || empty($password)) {
                Logger::warning("LOGIN: Campos vacíos - usuario: '$usuario'");
                return response::error('Usuario y contraseña son obligatorios');
            }

            // Validar credenciales mediante el servicio
            $usuarioData = $this->loginService->validarCredenciales($usuario, $password);

            if (!$usuarioData) {
                return response::error('Usuario o contraseña incorrectos');
            }

            // Login exitoso - retornar datos del usuario
            return response::success(
                $usuarioData,
                'Login exitoso'
            );

        } catch (Exception $e) {
            Logger::error("Error en LoginController::validarLogin", $e, $data);
            return response::error('Error al procesar el login');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $usuario = isset($_SESSION['usuario']['user_usuario']) ? $_SESSION['usuario']['user_usuario'] : 'desconocido';
            Logger::info("LOGOUT: Usuario {$usuario}");

            $_SESSION = [];
            session_destroy();

            return response::success([], 'Sesión cerrada exitosamente');
        } catch (Exception $e) {
            Logger::error("Error en LoginController::logout", $e);
            return response::error('Error al cerrar sesión');
        }
    }
}
