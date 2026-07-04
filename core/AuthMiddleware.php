<?php

/**
 * AUTH MIDDLEWARE - Validar acceso a rutas
 * Ejecuta ANTES del router para validar autenticación y permisos
 */

class AuthMiddleware {

    private static $rutasPublicas = array('login');

    /**
     * Validar acceso a una ruta
     */
    public static function validate($page) {
        if (self::isPublicRoute($page)) {
            return;
        }

        if (!Auth::isAuthenticated()) {
            header('Location: ' . BASE_PATH . 'login');
            exit();
        }

        if (!Auth::hasPermission($page)) {
            http_response_code(403);
            include_once 'views/403.php';
            exit();
        }
    }

    /**
     * Verificar si es una ruta pública
     */
    private static function isPublicRoute($page) {
        return in_array($page, self::$rutasPublicas) || $page === '';
    }

    /**
     * Agregar ruta pública
     */
    public static function addPublicRoute($route) {
        if (!in_array($route, self::$rutasPublicas)) {
            self::$rutasPublicas[] = $route;
        }
    }

    /**
     * Validar CSRF en POST requests
     */
    public static function validateCsrf() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

            if (!Auth::validateCsrfToken($token)) {
                http_response_code(403);
                die(json_encode(array('error' => 'CSRF token inválido')));
            }
        }
    }
}
