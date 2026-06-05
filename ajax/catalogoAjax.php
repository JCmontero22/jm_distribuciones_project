<?php
    
    require_once(__DIR__ . '/../core/ServiceContainer.php');
    require_once(__DIR__ . '/../controller/CatalogoController.php');
    
    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    // Instanciar modelos
    $servicio = ServiceContainer::getCatalogoService();

    // Crear servicio y controlador
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

        case 'registroMarca':
            $resultado = $catalogoController->registrarMarca($_POST['nombreMarca'] ?? '', $_FILES);
            echo json_encode($resultado);
            break;

        default:
    }
