<?php 

    require_once('../core/ServiceContainer.php');;
    require_once('../controller/ComprasController.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    $servicio = ServiceContainer::getComprasService();
    $comprasController = new ComprasController($servicio);

    switch ($accion) {
        case 'registrarCompra':
            $resultado = $comprasController->registrar($_POST);
            echo json_encode($resultado);
            break;

        case 'registrarDetallesCompra':
            $resultado = $comprasController->registrarDetalles($_POST);
            echo json_encode($resultado);
            break;

        case 'listarCompras':
            $resultado = $comprasController->listar();
            echo json_encode($resultado);
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }