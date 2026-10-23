<?php
require_once('../config/config.php');
require_once(__DIR__ . '/../core/Auth.php');
Auth::requireApi('ventasAjax');

require_once(__DIR__ . '/../core/ServiceContainer.php');
require_once(__DIR__ . '/../controller/VentasController.php');

$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : null);

$servicio = ServiceContainer::getVentasService();
$ventasController = new VentasController($servicio);

switch ($accion) {
    case 'listarVentas':
        $resultado = $ventasController->listar($_GET);
        echo json_encode($resultado);
        break;

    case 'catalogosVenta':
        $resultado = $ventasController->catalogosVenta($_GET);
        echo json_encode($resultado);
        break;

    case 'productosVentaPorSede':
        $resultado = $ventasController->productosPorSede($_GET);
        echo json_encode($resultado);
        break;

    case 'detalleVenta':
        $resultado = $ventasController->detalle($_GET);
        echo json_encode($resultado);
        break;

    case 'registrarVenta':
        $resultado = $ventasController->registrar($_POST);
        echo json_encode($resultado);
        break;

    case 'registrarAbono':
        $resultado = $ventasController->registrarAbono($_POST);
        echo json_encode($resultado);
        break;

    case 'anularVenta':
        $resultado = $ventasController->anular($_POST);
        echo json_encode($resultado);
        break;

    case 'preciosLocion':
        echo json_encode($ventasController->preciosLocion($_GET));
        break;

    case 'guardarPrecioLocion':
        echo json_encode($ventasController->guardarPrecioLocion($_POST));
        break;

    case 'preciosFormula':
        echo json_encode($ventasController->preciosFormula($_GET));
        break;

    case 'guardarPrecioFormula':
        echo json_encode($ventasController->guardarPrecioFormula($_POST));
        break;

    case 'esenciasPrecioFormula':
        echo json_encode($ventasController->esenciasPrecioFormula($_GET));
        break;

    case 'aplicarPrecioFormulaATodas':
        echo json_encode($ventasController->aplicarPrecioFormulaATodas($_POST));
        break;

    case 'registrarCliente':
        $resultado = $ventasController->registrarCliente($_POST);
        echo json_encode($resultado);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

