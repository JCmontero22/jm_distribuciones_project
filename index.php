<?php

/**
 * INDEX - Punto de entrada principal
 *
 * CAMBIAR ESTA CONSTANTE PARA ACTIVAR/DESACTIVAR AUTENTICACIÓN
 * true = Requiere login (PRODUCCIÓN)
 * false = Sin login (DESARROLLO)
 */
define('REQUIRE_AUTH', true);

require_once 'config/config.php';
require_once 'core/Auth.php';
require_once 'core/AuthMiddleware.php';
require_once 'core/ServiceContainer.php';
require_once 'core/Logger.php';

// Inicializar autenticación
if (REQUIRE_AUTH) {
    Auth::init();
}

$uri = strtok($_SERVER['REQUEST_URI'], '?');
$basePath = BASE_PATH;
$uri = str_replace($basePath, '', $uri);
$segments = explode('/', trim($uri, '/'));
$page = isset($segments[0]) ? $segments[0] : 'home';

// Validar acceso (solo si autenticación está activa)
if (REQUIRE_AUTH) {
    AuthMiddleware::validate($page);

    if (($page === 'login' || $page === '') && Auth::isAuthenticated()) {
        header('Location: ' . BASE_PATH . 'home');
        exit();
    }

    if ($page === 'login' || $page === '') {
        if (!Auth::isAuthenticated()) {
            include_once 'views/Auth/login.php';
            exit();
        }
        $page = 'home';
    }
}

require_once 'public/header.php';
?>

    <button class="btn-hamburger" id="btnHamburger" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="row g-0 layout-wrapper">
        <div class="sidebar-col" id="sidebarCol">
            <?php require_once 'public/menu.php'; ?>
        </div>
        <div class="content-col" id="contentCol">
            <?php require_once 'core/router.php'; ?>
            <script>
                var currentPage = '<?php echo htmlspecialchars($page); ?>';
                var csrfToken = '<?php echo REQUIRE_AUTH ? Auth::csrfToken() : ''; ?>';
            </script>
        </div>
    </div>

<?php require_once 'public/footer.php'; ?>
