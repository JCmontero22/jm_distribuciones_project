<?php

    require_once('../model/ProductosModel.php');
    require_once('../services/ProductoService.php');
    require_once('../controller/ProductoController.php');
    require_once('../Infrastructure/FileStorageService.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    $modelo = new ProductosModel();
    $storage = new LocalFileStorage('../assets/img/productos/');
    $servicio = new ProductoService($storage, $modelo);
    $productoController = new ProductoController($servicio);

    // Pasar la categoría en la request
    $request = array_merge($_GET, $_POST);
    $request['categoria'] = $_POST['categoriaProducto'] ?? $_GET['categoria'] ?? null;

    switch ($accion) {
        case 'registrarReloj':
        case 'registrarProducto':
            $resultado = $productoController->registrar($_POST);
            echo json_encode($resultado);
            break;

        case 'listadoRelojes':
        case 'listarProductos':
            $resultado = $productoController->listar($request);
            echo json_encode($resultado);
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
