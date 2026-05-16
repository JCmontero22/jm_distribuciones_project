<?php

    require_once('../services/CatalogoService.php');
    require_once('../controller/CatalogoController.php');
    require_once('../model/TiposProductosModel.php');
    require_once('../model/SedesModel.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    // Instanciar modelos
    $marcaModel = new MarcaModel();
    $generoModel = new GeneroModel();
    $categoriaModel = new CategoriaModel();
    $presentacionModel = new PresentacionProductoModel();
    $tiposProductoModel = new TiposProductosModel();
    $sedesModel = new SedesModel();

    // Crear servicio y controlador
    $servicio = new CatalogoService($marcaModel, $generoModel, $categoriaModel, $presentacionModel, $tiposProductoModel, $sedesModel);
    $catalogoController = new CatalogoController($servicio);

    switch ($accion) {
        case 'listadoMarcas':
            $resultado = $catalogoController->obtenerMarcas();
            echo json_encode($resultado);
            break;

        case 'listadoGeneros':
            $resultado = $catalogoController->obtenerGeneros();
            echo json_encode($resultado);
            break;

        case 'listadoCategorias':
            $resultado = $catalogoController->obtenerCategorias();
            echo json_encode($resultado);
            break;

        case 'listadoPresentaciones':
            $resultado = $catalogoController->obtenerPresentaciones();
            echo json_encode($resultado);
            break;

        case 'obtenerCatalogosCompletos':
            $resultado = $catalogoController->obtenerCatalogosCompletos();
            echo json_encode($resultado);
            break;

        case 'listadoTiposProductos':
            $resultado = $catalogoController->obtenerTiposProductos();
            echo json_encode($resultado);
            break;

        case 'listadoSedes':
            $resultado = $catalogoController->obtenerSedes();
            echo json_encode($resultado);
            break;

        default:
    }
