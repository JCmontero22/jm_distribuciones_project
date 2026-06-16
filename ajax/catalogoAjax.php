<?php
require_once('../config/config.php');
    
    require_once(__DIR__ . '/../core/ServiceContainer.php');
    require_once(__DIR__ . '/../controller/CatalogoController.php');
    require_once(__DIR__ . '/../controller/ProductoController.php');
    
    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);

    $catalogoController = null;
    $getCatalogoController = function () use (&$catalogoController) {
        if ($catalogoController === null) {
            $servicio = ServiceContainer::getCatalogoService();
            $catalogoController = new CatalogoController($servicio);
        }

        return $catalogoController;
    };

    switch ($accion) {
        case 'listadoMarcas':
            $resultado = $getCatalogoController()->obtenerMarcas();
            echo json_encode($resultado);
            break;

        case 'listadoGeneros':
            $resultado = $getCatalogoController()->obtenerGeneros();
            echo json_encode($resultado);
            break;

        case 'listadoCategorias':
            $resultado = $getCatalogoController()->obtenerCategorias();
            echo json_encode($resultado);
            break;

        case 'listadoPresentaciones':
            $resultado = $getCatalogoController()->obtenerPresentaciones();
            echo json_encode($resultado);
            break;

        case 'listadoProductos':
            $productoController = new ProductoController(ServiceContainer::getProductoService());
            $resultado = $productoController->listarProductosActivos();
            echo json_encode($resultado);
            break;

        case 'obtenerCatalogosCompletos':
            $resultado = $getCatalogoController()->obtenerCatalogosCompletos();
            echo json_encode($resultado);
            break;

        case 'listadoTiposProductos':
            $resultado = $getCatalogoController()->obtenerTiposProductos();
            echo json_encode($resultado);
            break;

        case 'listadoSedes':
            $resultado = $getCatalogoController()->obtenerSedes();
            echo json_encode($resultado);
            break;

        case 'registroMarca':
            $resultado = $getCatalogoController()->registrarMarca($_POST['nombreMarca'] ?? '', $_FILES);
            echo json_encode($resultado);
            break;

        case 'obtenerMarcaID':
            $resultado = $getCatalogoController()->obtenerMarcaPorID($_GET['idMarca'] ?? 0);
            echo json_encode($resultado);
            break;

        case 'actualizarMarca':
            $resultado = $getCatalogoController()->actualizarMarca((int)($_POST['id_marca'] ?? 0), $_POST['nombreMarca'] ?? '', $_FILES);
            echo json_encode($resultado);
            break;

        case 'eliminarMarca':
            $resultado = $getCatalogoController()->eliminarMarca((int)($_POST['idMarca'] ?? 0));
            echo json_encode($resultado);
            break;

        default:
            echo json_encode(response::error('Acción no válida', ['accion' => $accion]));
            break;
    }
