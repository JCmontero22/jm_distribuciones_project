<?php 

    require_once('../model/ProveedorModel.php');
    require_once('../services/ProveedorService.php');
    require_once('../controller/ProveedorController.php');
    var_dump('entro');die();

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    $modelo = new ProveedorModel();
    $servicio = new ProveedorService($modelo);
    $proveedorController = new ProveedorController($servicio);

    switch ($accion) {
        case 'listadoProveedores':
            $resultado = $proveedorController->obtenerProveedores();
            echo json_encode($resultado);
            break;
        
        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    }