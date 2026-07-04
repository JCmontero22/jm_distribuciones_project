<?php

/**
 * ROUTER - Enrutador de páginas
 */

$routes = array(
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
    'descuentos' => 'promotions/descuentos.php',
    'configuracion' => 'configuration/configuracion.php',
);

$page = isset($page) ? $page : 'home';

if (!isset($routes[$page])) {
    http_response_code(404);
    include_once 'views/404.php';
    exit();
}

try {
    include_once 'views/' . $routes[$page];
} catch (Exception $e) {
    Logger::error("Error al cargar vista: {$page}", $e);
    http_response_code(500);
    include_once 'views/500.php';
}
