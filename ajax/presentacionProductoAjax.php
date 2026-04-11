<?php
    
    require_once('../model/PresentacionProductoModel.php'); 
    require_once('../services/PresentacionProductoService.php');
    require_once('../controller/PresentacionProductoController.php');
    require_once('../Infrastructure/FileStorageService.php');
    
        
    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);
    
    $modelo = new PresentacionProductoModel();
    $storage = new LocalFileStorage('../assets/img/productos/');
    $servicio = new PresentacionProductoService($storage, $modelo);
    $presentacionProductoController = new PresentacionProductoController($servicio);

    switch ($accion) {
        case 'registrarPresentacion':
            $resultado = $presentacionProductoController->registroPresentacionProducto($_POST);
            echo  json_encode($resultado);
        break;

        case 'registrarDetalleProducto':
        # code...
        break;
    
       /*  case 'listadoProductos':
            $resultado = $presentacionProductoController->listadoPresentacionProductos();
            echo json_encode($resultado);
            break;
 */
        
        
        default:
        
}