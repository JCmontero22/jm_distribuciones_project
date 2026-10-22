<?php
require_once('../config/config.php');
require_once(__DIR__ . '/../core/Auth.php');
Auth::requireApi('historialAjax');

require_once(__DIR__ . '/../core/ServiceContainer.php');
require_once(__DIR__ . '/../controller/VentasController.php');

$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;

$controller = new VentasController(ServiceContainer::getVentasService());

switch ($accion) {
    case 'listarVentas':
        echo json_encode($controller->listar($_GET));
        break;

    case 'detalleVenta':
        echo json_encode($controller->detalle($_GET));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
