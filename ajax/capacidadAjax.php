<?php

$projectRoot = __DIR__ . '/..';
require_once($projectRoot . '/core/ServiceContainer.php');
require_once($projectRoot . '/controller/CapacidadProduccionController.php');

// Obtener acción de GET o POST
$accion = isset($_GET['accion']) ? $_GET['accion'] : (isset($_POST['accion']) ? $_POST['accion'] : null);

try {
    $servicio = ServiceContainer::getCapacidadService();
    $controller = new CapacidadProduccionController($servicio);

    switch ($accion) {
        case 'capacidadProduccion':
            $resultado = $controller->obtenerReporte();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($resultado);
            break;

        default:
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Acción no válida', 'data' => []]);
            break;
    }
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'data' => []]);
}
