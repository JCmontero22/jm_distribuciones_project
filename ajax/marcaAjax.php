<?php 

    require_once('../controller/MarcaController.php');
    require_once('../services/MarcaServicio.php');
    require_once('../model/MarcaModel.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : 'listadoMarcas');

    $modelo = new MarcaModel();
    $servicio = new MarcaService($modelo);
    $controller = new MarcaController($servicio);

    switch ($accion) {
        case 'listadoMarcas':
            echo json_encode($controller->listadoMarcas());
            break;
        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
