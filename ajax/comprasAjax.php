<?php 
require_once('../config/config.php');

    require_once('../core/ServiceContainer.php');;
    require_once('../controller/ComprasController.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    $servicio = ServiceContainer::getComprasService();
    $comprasController = new ComprasController($servicio);

    switch ($accion) {
        case 'registrarCompra':
            // Espera: idProveedor, totalCompra, numeroFacturaCompra, detalles (JSON)
            $resultado = $comprasController->registrar($_POST);
            echo json_encode($resultado);
            break;

        case 'listarCompras':
            $resultado = $comprasController->listar();
            echo json_encode($resultado);
            break;

        case 'detalleCompra':
            $resultado = $comprasController->detalle($_GET);
            echo json_encode($resultado);
            break;

        case 'actualizarCompra':
            $resultado = $comprasController->actualizar($_POST);
            echo json_encode($resultado);
            break;

        case 'eliminarCompra':
            $resultado = $comprasController->eliminar($_POST);
            echo json_encode($resultado);
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }