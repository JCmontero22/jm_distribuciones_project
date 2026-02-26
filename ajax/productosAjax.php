<?php
    
    require_once('../model/ProductoModel.php'); 
    require_once('../services/ProductoService.php');
    require_once('../controller/ProductoController.php');
    require_once('../Infrastructure/FileStorageService.php');
    
        
    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : null);
    
    $modelo = new ProductoModel();
    $storage = new LocalFileStorage('../assets/img/productos/');
    $servicio = new ProductoService($storage, $modelo);
    $productoController = new ProductoController($servicio);

    switch ($accion) {
        case 'registrarProducto':
            $resultado = $productoController->registrarProducto($_POST);
            echo  json_encode($resultado);
        break;
        
        default:
        
}