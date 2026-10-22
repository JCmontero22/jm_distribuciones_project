<?php
require_once('../config/config.php');
require_once(__DIR__ . '/../core/Auth.php');
Auth::requireApi('gastosAjax');

require_once(__DIR__ . '/../core/ServiceContainer.php');
require_once(__DIR__ . '/../controller/GastosController.php');

$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : null);

$controller = new GastosController(ServiceContainer::getGastosService());

switch ($accion) {
    case 'registrarGasto':
        echo json_encode($controller->registrar($_POST));
        break;

    case 'listarGastos':
        echo json_encode($controller->listar($_GET));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
