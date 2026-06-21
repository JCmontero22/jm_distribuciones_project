<?php
require_once('../config/config.php');

require_once(__DIR__ . '/../core/response.php');
require_once(__DIR__ . '/../core/ServiceContainer.php');
require_once(__DIR__ . '/../controller/DescuentosController.php');

$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : null);

$servicio = ServiceContainer::getDescuentosService();
$descuentoController = new DescuentosController($servicio);

switch ($accion) {
    case 'registrarDescuento':
        $resultado = $descuentoController->registrarDescuento($_POST);
        echo json_encode($resultado);
        break;

    case 'obtenerDescuentos':
        $resultado = $descuentoController->obtenerDescuentos();
        echo json_encode($resultado);
        break;

    case 'obtenerDescuentoPorID':
        $resultado = $descuentoController->obtenerDescuentoPorID($_GET);
        echo json_encode($resultado);
        break;

    case 'actualizarDescuento':
        $resultado = $descuentoController->actualizarDescuento($_POST);
        echo json_encode($resultado);
        break;

    case 'aplicarAProductosEspecificos':
        $resultado = $descuentoController->aplicarDescuentoAProductosEspecificos($_POST);
        echo json_encode($resultado);
        break;

    case 'aplicarAMarca':
        $resultado = $descuentoController->aplicarDescuentoAMarca($_POST);
        echo json_encode($resultado);
        break;

    case 'aplicarAProductoPorGenero':
        $resultado = $descuentoController->aplicarDescuentoAProductoPorGenero($_POST);
        echo json_encode($resultado);
        break;

    case 'aplicarAGenero':
        $resultado = $descuentoController->aplicarDescuentoAGenero($_POST);
        echo json_encode($resultado);
        break;

    case 'aplicarATodos':
        $resultado = $descuentoController->aplicarDescuentoATodos($_POST);
        echo json_encode($resultado);
        break;

    case 'removerDescuento':
        $resultado = $descuentoController->removerDescuento($_POST);
        echo json_encode($resultado);
        break;

    case 'eliminarDescuento':
        $resultado = $descuentoController->eliminarDescuento($_POST);
        echo json_encode($resultado);
        break;

    default:
        echo json_encode(response::error('Acción no válida'));
}
