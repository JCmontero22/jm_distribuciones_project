<?php

/**
 * ============================================================
 * CONFIGURACIÓN PRINCIPAL DEL PROYECTO
 * ============================================================
 *
 * IMPORTANTE: Solo modificar BASE_PROJECT_PATH si cambias
 * la ubicación del proyecto en el servidor.
 *
 * Ejemplos:
 * - Localhost: '/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/'
 * - Producción: '/jm_distribuciones/' (si está en raíz)
 * - Subdominio: '/' (si es el proyecto raíz)
 */

// ============================================================
// 1. RUTA BASE DEL PROYECTO (PUNTO CRÍTICO - CAMBIAR AQUÍ PARA SERVIDOR)
// ============================================================
define('BASE_PROJECT_PATH', '/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/');

// ============================================================
// 2. RUTAS DE DIRECTORIOS DEL SISTEMA
// ============================================================
define('BASE_URL', dirname(__DIR__) . '/');
define('BASE_PATH', BASE_PROJECT_PATH);

// Directorios del proyecto
define('DIR_CONTROLLER', BASE_URL . 'controller/');
define('DIR_SERVICE', BASE_URL . 'services/');
define('DIR_MODEL', BASE_URL . 'model/');
define('DIR_VIEWS', BASE_URL . 'views/');
define('DIR_AJAX', BASE_URL . 'ajax/');
define('DIR_CORE', BASE_URL . 'core/');
define('DIR_CONFIG', BASE_URL . 'config/');
define('DIR_HELPER', BASE_URL . 'helper/');
define('DIR_INFRASTRUCTURE', BASE_URL . 'Infrastructure/');
define('DIR_PUBLIC', BASE_URL . 'public/');
define('DIR_LOGS', BASE_URL . 'logs/');
define('DIR_ASSETS', BASE_URL . 'assets/');

// ============================================================
// 3. RUTAS DE UPLOADS Y ALMACENAMIENTO
// ============================================================
define('UPLOAD_PRODUCTS', DIR_ASSETS . 'img/productos/');
define('UPLOAD_BANNERS', DIR_ASSETS . 'img/banners/');
define('UPLOAD_MARCAS', DIR_ASSETS . 'img/marcas/');
define('UPLOAD_TEMP', DIR_ASSETS . 'img/temp/');
define('IMG_COMUNES', DIR_ASSETS . '../../../imagenes-jm-distribuciones/bannerPromociones/');

// ============================================================
// 4. RUTAS WEB (PARA JAVASCRIPT Y NAVEGADOR)
// ============================================================
define('WEB_BASE', BASE_PROJECT_PATH);
define('WEB_AJAX', WEB_BASE . 'ajax/');
define('WEB_ASSETS', WEB_BASE . 'assets/');
define('WEB_IMG', WEB_ASSETS . 'img/');
define('WEB_CSS', WEB_ASSETS . 'css/');
define('WEB_JS', WEB_ASSETS . 'js/');

// ============================================================
// 5. CONFIGURACIÓN DE BASE DE DATOS
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jm_distribuciones');
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', 3306);

// ============================================================
// 6. CONFIGURACIÓN DE APLICACIÓN
// ============================================================
define('APP_NAME', 'JM Distribuciones');
define('APP_VERSION', '1.0.0');
define('APP_ENVIRONMENT', 'development'); // development, production
define('APP_DEBUG', APP_ENVIRONMENT === 'development');

// ============================================================
// 7. VALIDACIÓN Y SEGURIDAD
// ============================================================
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// ============================================================
// 8. VALIDACIÓN AUTOMÁTICA DE CONFIGURACIÓN
// ============================================================
if (!file_exists(DIR_LOGS)) {
    @mkdir(DIR_LOGS, 0755, true);
}

if (!file_exists(UPLOAD_PRODUCTS)) {
    @mkdir(UPLOAD_PRODUCTS, 0755, true);
}

if (!file_exists(UPLOAD_BANNERS)) {
    @mkdir(UPLOAD_BANNERS, 0755, true);
}

if (!file_exists(UPLOAD_MARCAS)) {
    @mkdir(UPLOAD_MARCAS, 0755, true);
}