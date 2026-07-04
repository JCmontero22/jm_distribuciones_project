<?php

/**
 * LOGIN AJAX - Solo router
 *
 * Patrón:
 * 1. Obtener acción
 * 2. Obtener servicio del ServiceContainer
 * 3. Obtener controller
 * 4. Ejecutar método del controller
 * 5. Retornar respuesta JSON
 */

// Limpiar output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Headers JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once('../config/config.php');
require_once('../core/ServiceContainer.php');
require_once('../controller/LoginController.php');

// Obtener acción
$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : null);

// Obtener servicios del contenedor
$loginService = ServiceContainer::getLoginService();
$loginController = new LoginController($loginService);

// Ejecutar acción
switch ($accion) {
    case 'validar':
        $resultado = $loginController->validarLogin($_POST);

        // Si es exitoso, crear sesión
        if ($resultado['success']) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $usuarioData = $resultado['data'];
            $_SESSION['usuario'] = [
                'id_usuario' => $usuarioData['id_usuario'],
                'nombre_usuario' => $usuarioData['nombre_usuario'],
                'user_usuario' => $usuarioData['user_usuario'],
                'id_perfil_usuario' => $usuarioData['id_perfil_usuario'],
                'nombre_perfil_usuario' => $usuarioData['nombre_perfil_usuario'],
            ];

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();

            // Agregar redirect a la respuesta
            $resultado['data']['redirect'] = BASE_PATH . 'home';
        }

        echo json_encode($resultado);
        break;

    case 'logout':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $resultado = $loginController->logout();
        echo json_encode($resultado);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción no válida', 'data' => []]);
        break;
}

exit();
