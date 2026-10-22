<?php
require_once('../config/config.php');
require_once(__DIR__ . '/../core/Auth.php');
Auth::requireApi('balanceAjax');

require_once(__DIR__ . '/../model/BalanceModel.php');
require_once(__DIR__ . '/../services/BalanceService.php');
require_once(__DIR__ . '/../controller/BalanceController.php');

$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;

switch ($accion) {
    case 'balanceMensual':
        $controller = new BalanceController(new BalanceService(new BalanceModel()));
        echo json_encode($controller->balance($_GET));
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
