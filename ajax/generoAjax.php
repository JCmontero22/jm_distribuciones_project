<?php

    require_once('../model/GeneroModel.php');
    require_once('../services/GeneroService.php');
    require_once('../controller/GeneroController.php');

    $accion = isset($_POST['accion']) ? $_POST['accion'] :  (isset($_GET['accion']) ? $_GET['accion'] : 'listadoGeneros');
    
    $modelo = new GeneroModel();
    $servicio = new GeneroService($modelo);
    $generoController = new GeneroController($servicio);
    
    switch ($accion) {
        case 'listadoGeneros':
            
            $resultado = $generoController->listaGeneros();
            echo  json_encode($resultado);
        break;
    }