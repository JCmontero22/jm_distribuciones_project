<?php

    $routes = [
        'home' => 'home.php',
        'inventario' => 'inventario.php',
        'historial' => 'historial.php',
        'compras' => 'compras.php',
    ];


    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    $basePath = '/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project'; //Remplazar en producción
    $uri = str_replace($basePath, '', $uri);
    $segments = explode('/', trim($uri, '/'));
    $page = $segments[0] ?? 'home';
    
    if ($page === '' ) {
        $page = 'home';
    }
    
    if (in_array($page, array_keys($routes))) {
        include_once 'views/' . $routes[$page];
    } else {
        include_once 'views/404.php';
    }