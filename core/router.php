<?php

    require_once'config/config.php';

    $routes = [
        'home' => 'home.php',
        'relojes' => 'inventory/relojes.php',
        'lociones' => 'inventory/lociones.php',
        'historial' => 'historial.php',
        'compras' => 'shopping/compras.php',
        'proveedores' => 'shopping/proveedores.php',
    ];


    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    $basePath = BASE_PATH; 
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