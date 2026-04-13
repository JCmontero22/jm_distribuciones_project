<?php 
    require_once('../model/ProveedorModel.php');
    require_once('../services/ProveedorService.php');
    require_once('../controller/ProveedorController.php');
    

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    $modelo = new ProveedorModel();
    $servicio = new ProveedorService($modelo);
    $proveedorController = new ProveedorController($servicio);

    switch ($accion) {
        case 'listadoProveedoresSelect':
            $resultado = $proveedorController->obtenerProveedoresSelect();
            echo json_encode($resultado);
            break;
        case 'listadoProductosTable':
            $resultado = $proveedorController->obtenerProveedoresTabla();
            echo json_encode($resultado);
            break;

        case 'registrarProveedor':
            $resultado = $proveedorController->registrarProveedor($_POST);
            echo json_encode($resultado);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    }