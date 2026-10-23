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
    /** Sede asignada al usuario (vendedor). Null para administrador. */
    public static function sedeUsuario(): ?int {
        // Sesiones creadas antes de guardar la sede no traen el dato: se lee de la base una vez
        if (isset($_SESSION['usuario']) && !array_key_exists('id_sede', $_SESSION['usuario'])) {
            require_once(__DIR__ . '/../model/LoginModel.php');
            $datos = (new LoginModel())->obtenerPorUsuario($_SESSION['usuario']['user_usuario'] ?? '');
            $_SESSION['usuario']['id_sede'] = $datos['id_sede'] ?? null;
        }
        $sede = $_SESSION['usuario']['id_sede'] ?? null;
        return $sede ? (int)$sede : null;
    }

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
     * Sección de permiso que controla cada ruta y cada endpoint AJAX.
     * Varias rutas comparten la misma sección (p. ej. todo el inventario usa "Inventario").
     * Toda ruta o módulo que no aparezca aquí (y no esté en $soloAutenticado) se DENIEGA.
     */
    private static $permisoPorRuta = array(
        'relojes'            => 'Inventario',
        'esencias'           => 'Inventario',
        'insumos'            => 'Inventario',
        'locionesAAA'        => 'Inventario',
        'lociones'           => 'Inventario',
        'compras'            => 'Compras',
        'proveedores'        => 'Compras',
        'informeProduccion'  => 'Informe de produccion',
        'formulas'           => 'Produccion',
        'marcas'             => 'Produccion',
        'bannerPromociones'  => 'Promociones',
        'descuentos'         => 'Promociones',
        'configuracion'      => 'configuraciones',
        'historial'          => 'Historial',
        'ventas'             => 'Registro Ventas',
        'gastos'             => 'Registro gastos',
        'historialVentas'    => 'Historial',
        'balanceMensual'     => 'Historial',

        // Endpoints AJAX
        'productosAjax'         => 'Inventario',
        'comprasAjax'           => 'Compras',
        'proveedorAjax'         => 'Compras',
        'formulasAjax'          => 'Produccion',
        'informeProduccionAjax' => 'Informe de produccion',
        'promocionesAjax'       => 'Promociones',
        'descuentosAjax'        => 'Promociones',
        'usuariosAjax'          => 'configuraciones',
        'ventasAjax'            => 'Registro Ventas',
        'gastosAjax'            => 'Registro gastos',
        'historialAjax'         => 'Historial',
        'balanceAjax'           => 'Historial',
    );

    /**
     * Rutas y endpoints que solo requieren sesión iniciada (sin sección de permiso)
     */
    private static $soloAutenticado = array('home', 'catalogoAjax', 'homeAjax');

    /**
     * Permisos cargados de BD una sola vez por petición
     */
    private static $permisosCache = null;

    /**
     * Obtener el nombre de permiso que controla una ruta
     */
    public static function permisoDeRuta($ruta) {
        return isset(self::$permisoPorRuta[$ruta]) ? self::$permisoPorRuta[$ruta] : $ruta;
    }

    /**
     * Cargar (una vez por petición) los permisos existentes y los del perfil del usuario
     */
    private static function cargarPermisos($idPerfil) {
        if (self::$permisosCache === null) {
            require_once(__DIR__ . '/../model/PermisosModel.php');
            $permisosModel = new PermisosModel();

            self::$permisosCache = array(
                'existentes' => array_map('strtolower', array_column($permisosModel->obtenerPermisos(), 'nombre_permiso')),
                'delPerfil'  => array_map('strtolower', array_column($permisosModel->obtenerPermisosDelPerfil((int)$idPerfil), 'nombre_permiso')),
            );
        }
        return self::$permisosCache;
    }

    /**
     * Validar permiso para acceder a una ruta o endpoint AJAX
     *
     * LÓGICA (fail-closed):
     * 1. Si usuario NO está autenticado → DENEGAR
     * 2. Si la ruta es solo-autenticado → PERMITIR
     * 3. Si la sección de la ruta NO existe en BD → DENEGAR
     * 4. Si la sección existe → PERMITIR solo si el perfil la tiene
     * 5. Si hay error al consultar BD → DENEGAR
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

        if (in_array($ruta, self::$soloAutenticado, true)) {
            return true;
        }

        try {
            $permisos = self::cargarPermisos($usuario['id_perfil_usuario']);
            $permiso = strtolower(self::permisoDeRuta($ruta));

            // Sección no existe en BD → Denegar
            if (!in_array($permiso, $permisos['existentes'], true)) {
                return false;
            }

            // Sección existe en BD → Validar que el perfil la tenga
            return in_array($permiso, $permisos['delPerfil'], true);

        } catch (Exception $e) {
            // Si hay error, denegar acceso
            error_log("Error al validar permisos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guard para endpoints AJAX (ajax/*.php)
     * Valida sesión, permiso del módulo y token CSRF en POST.
     * Responde JSON y termina la ejecución si no se cumple.
     *
     * @param string $modulo Nombre del endpoint (p. ej. 'productosAjax')
     */
    public static function requireApi($modulo) {
        self::init();

        if (!self::isAuthenticated()) {
            self::responderErrorJson(401, 'Sesión no iniciada o expirada');
        }

        if (!self::hasPermission($modulo)) {
            self::responderErrorJson(403, 'No tienes permiso para esta acción');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_SERVER['HTTP_X_CSRF_TOKEN'])
                ? $_SERVER['HTTP_X_CSRF_TOKEN']
                : (isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '');

            if (!self::validateCsrfToken($token)) {
                self::responderErrorJson(403, 'Token CSRF inválido');
            }
        }
    }

    /**
     * Responder error en formato JSON del proyecto y terminar
     */
    private static function responderErrorJson($codigo, $mensaje) {
        http_response_code($codigo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('success' => false, 'message' => $mensaje, 'data' => array()));
        exit();
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
