<?php
require_once('../config/config.php');

    require_once('../core/ServiceContainer.php');
    require_once('../controller/ProductoController.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    $servicio = ServiceContainer::getProductoService();
    $productoController = new ProductoController($servicio);

    // Pasar la categoría en la request
    $request = array_merge($_GET, $_POST);
    $request['categoria'] = $_POST['categoriaProducto'] ?? $_GET['categoria'] ?? null;

    switch ($accion) {
        case 'registrarProducto':
            $resultado = $productoController->registrar($_POST);
            echo json_encode($resultado);
            break;

        case 'registrarPresentaciones':
            
            $presentaciones = isset($_POST['presentaciones']) ? json_decode($_POST['presentaciones'], true) : [];

            /* var_dump($presentaciones); */
            $resultado = $productoController->registrarPresentaciones($presentaciones, $_FILES);
            echo json_encode($resultado);
            break;

        case 'listarProductos':
            $resultado = $productoController->listar($request);
            echo json_encode($resultado);
            break;
        
        case 'productosCompra':
            $resultado = $productoController->listarProductosCompra($request);
            echo json_encode($resultado);
            break;

        case 'listarProductosActivos':
            $resultado = $productoController->listarProductosActivos();
            echo json_encode($resultado);
            break;

        case 'listarEsencias':
            $resultado = $productoController->listarEsencias();
            echo json_encode($resultado);
            break;

        case 'listarPresentacionesPorProducto':
            $resultado = $productoController->listarPresentacionesPorProducto($request);
            echo json_encode($resultado);
            break;

        case 'obtenerProducto':
            $resultado = $productoController->obtenerProducto($request);
            echo json_encode($resultado);
            break;

        case 'editarProducto':
            $resultado = $productoController->editar($_POST);
            echo json_encode($resultado);
            break;

        case 'eliminarProducto':
            $resultado = $productoController->eliminar($request);
            echo json_encode($resultado);
            break;

        case 'eliminarPresentacion':
            $resultado = $productoController->eliminarPresentacion($request);
            echo json_encode($resultado);
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
