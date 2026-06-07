<?php

    require_once'config/config.php';

    $routes = [
        'home' => 'home.php',
        'relojes' => 'inventory/relojes.php',
        'lociones' => 'inventory/lociones.php',
        'locionesAAA' => 'inventory/locionesAAA.php',
        'locionesPreparadas' => 'inventory/locionesPreparadas.php',
        'esencias' => 'inventory/esencias.php',
        'insumos' => 'inventory/insumos.php',
        'historial' => 'historial.php',
        'compras' => 'shopping/compras.php',
        'proveedores' => 'shopping/proveedores.php',
        'formulas' => 'production/formulas.php',
        'informeProduccion' => 'production/informeProduccion.php',
        'marcas' => 'production/marcas.php',
        'bannerPromociones' => 'promotions/bannerPromocion.php',
        'promocionesProductos' => 'promotions/promocionesProductos.php',
        
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