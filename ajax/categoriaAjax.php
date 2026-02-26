<?php

    require_once('../model/CategoriaModel.php');
    require_once('../services/CategoriaServices.php');
    require_once('../controller/CategoriaController.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : 'listadoCategorias');
    
    $modelo = new CategoriaModel();
    $servicio = new CategoriaServices($modelo);
    $categoriaController = new CategoriaController($servicio);
    
    switch ($accion) {
        case 'listadoCategorias':
            
            $resultado = $categoriaController->listaCategorias();
            echo  json_encode($resultado);
        break;
    }