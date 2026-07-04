<?php

/**
 * AUTH - Helper de sesión
 *
 * SOLO helpers para validar sesión, permisos, etc.
 * La lógica de LOGIN está en: LoginController → LoginService → LoginModel
 */

class Auth {
    private static $sessionTimeout = 3600;

    /**
     * Inicializar sesión
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (self::isAuthenticated()) {
            self::validateSessionTimeout();
        }
    }

    /**
     * Verificar si está autenticado
     */
    public static function isAuthenticated() {
        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
    }

    /**
     * Obtener usuario actual
     */
    public static function user() {
        return isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
    }

    /**
     * Obtener ID del usuario
     */
    public static function userId() {
        return isset($_SESSION['usuario']['id_usuario']) ? $_SESSION['usuario']['id_usuario'] : null;
    }

    /**
     * Obtener perfil del usuario
     */
    public static function userProfile() {
        return isset($_SESSION['usuario']['nombre_perfil_usuario']) ? $_SESSION['usuario']['nombre_perfil_usuario'] : 'Usuario';
    }

    /**
     * Obtener CSRF token
     */
    public static function csrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validar CSRF token
     */
    public static function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Cerrar sesión
     */
    public static function logout() {
        $_SESSION = array();
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
    }

    /**
     * Validar timeout de sesión
     */
    private static function validateSessionTimeout() {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return;
        }

        $elapsed = time() - $_SESSION['last_activity'];

        if ($elapsed > self::$sessionTimeout) {
            self::logout();
            header('Location: ' . BASE_PATH . 'login?expired=1');
            exit();
        }

        $_SESSION['last_activity'] = time();
    }

    /**
     * Validar permiso para acceder a una ruta
     *
     * LÓGICA:
     * 1. Si usuario NO está autenticado → DENEGAR
     * 2. Si existe permiso en BD para esa ruta → VALIDAR que lo tenga
     * 3. Si permiso NO existe en BD → PERMITIR (sin validación)
     */
    public static function hasPermission($ruta) {
        // Si no hay usuario logueado, denegar acceso
        if (!self::isAuthenticated()) {
            return false;
        }

        $usuario = self::user();
        if (!isset($usuario['id_perfil_usuario'])) {
            return false;
        }

        try {
            require_once(__DIR__ . '/../model/PermisosModel.php');
            $permisosModel = new PermisosModel();

            // Verificar si existe el permiso en la BD para esta ruta
            if (!$permisosModel->existePermiso($ruta)) {
                // Permiso no existe en BD → Permitir acceso
                return true;
            }

            // Permiso existe en BD → Validar que el usuario lo tenga
            return $permisosModel->tienePermiso(
                $usuario['id_perfil_usuario'],
                $ruta
            );

        } catch (Exception $e) {
            // Si hay error, permitir acceso (no romper la app)
            error_log("Error al validar permisos: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Require autenticación
     */
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            header('Location: ' . BASE_PATH . 'login');
            exit();
        }
    }

    /**
     * Require permiso específico
     */
    public static function requirePermission($ruta) {
        self::requireAuth();

        if (!self::hasPermission($ruta)) {
            http_response_code(403);
            include_once 'views/403.php';
            exit();
        }
    }
}
