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

        // MARCAS
        case 'listadoMarcas':
            $resultado = $getCatalogoController()->obtenerMarcas();
            echo json_encode($resultado);
            break;

        case 'obtenerMarcaID':
            $resultado = $getCatalogoController()->obtenerMarcaPorID($_GET['idMarca'] ?? 0);
            echo json_encode($resultado);
            break;

        case 'registroMarca':
            $resultado = $getCatalogoController()->registrarMarca($_POST['nombreMarca'] ?? '', $_FILES);
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

        // CATEGORIAS
        case 'listadoCategorias':
            $resultado = $getCatalogoController()->obtenerCategorias();
            echo json_encode($resultado);
            break;

        case 'registrarCategoria':
            $resultado = $getCatalogoController()->registrarCategoria($_POST['nombreCategoria'] ?? '');
            echo json_encode($resultado);
            break;

        case 'actualizarCategoria':
            $resultado = $getCatalogoController()->actualizarCategoria((int)($_POST['idCategoria'] ?? 0), $_POST['nombreCategoria'] ?? '');
            echo json_encode($resultado);
            break;

        case 'eliminarCategoria':
            $resultado = $getCatalogoController()->eliminarCategoria((int)($_POST['idCategoria'] ?? 0));
            echo json_encode($resultado);
            break;

        // TIPOS DE PRODUCTOS
        case 'listadoTiposProductos':
            $resultado = $getCatalogoController()->obtenerTiposProductos();
            echo json_encode($resultado);
            break;

        case 'registrarTipoProducto':
            $resultado = $getCatalogoController()->registrarTipoProducto($_POST['descripcionTipoProducto'] ?? '');
            echo json_encode($resultado);
            break;

        case 'actualizarTipoProducto':
            $resultado = $getCatalogoController()->actualizarTipoProducto((int)($_POST['idTipoProducto'] ?? 0), $_POST['descripcionTipoProducto'] ?? '');
            echo json_encode($resultado);
            break;

        case 'eliminarTipoProducto':
            $resultado = $getCatalogoController()->eliminarTipoProducto((int)($_POST['idTipoProducto'] ?? 0));
            echo json_encode($resultado);
            break;

        // SEDES
        case 'listadoSedes':
            $resultado = $getCatalogoController()->obtenerSedes();
            echo json_encode($resultado);
            break;

        case 'registrarSede':
            $resultado = $getCatalogoController()->registrarSede($_POST);
            echo json_encode($resultado);
            break;

        case 'actualizarSede':
            $resultado = $getCatalogoController()->actualizarSede($_POST);
            echo json_encode($resultado);
            break;

        case 'eliminarSede':
            $resultado = $getCatalogoController()->eliminarSede((int)($_POST['idSede'] ?? 0));
            echo json_encode($resultado);
            break;

        // PRESENTACIONES
        case 'listadoPresentaciones':
            $resultado = $getCatalogoController()->obtenerPresentaciones();
            echo json_encode($resultado);
            break;

        // PRODUCTOS
        case 'listadoProductos':
            $productoController = new ProductoController(ServiceContainer::getProductoService());
            $resultado = $productoController->listarProductosActivos();
            echo json_encode($resultado);
            break;

        //CATALOGO
        case 'obtenerCatalogosCompletos':
            $resultado = $getCatalogoController()->obtenerCatalogosCompletos();
            echo json_encode($resultado);
            break;

        // GENEROS
        case 'listadoGeneros':
            $resultado = $getCatalogoController()->obtenerGeneros();
            echo json_encode($resultado);
            break;        

        default:
            echo json_encode(response::error('Acción no válida', ['accion' => $accion]));
            break;
    }
