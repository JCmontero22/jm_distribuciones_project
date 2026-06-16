<?php
require_once('../config/config.php');

require_once(__DIR__ . '/../core/response.php');
require_once(__DIR__ . '/../core/ServiceContainer.php');
require_once(__DIR__ . '/../controller/PromocionesController.php');

$accion = isset($_POST['accion']) ? $_POST['accion'] : (isset($_GET['accion']) ? $_GET['accion'] : null);

$servicio = ServiceContainer::getPromocionesService();
$promocionController = new PromocionesController($servicio);

switch ($accion) {
    case 'registrarBannerPromocion':
        $resultado = $promocionController->registrarBannerPromocion($_POST, $_FILES);
        echo json_encode($resultado);
        break;

    case 'obtenerBanners':
        $resultado = $promocionController->obtenerBanners();
        echo json_encode($resultado);
        break;

    case 'obtenerBannerID':
        $resultado = $promocionController->obtenerBannerID($_GET);
        echo json_encode($resultado);
    break;

    case 'actualizarBanner':
        $resultado = $promocionController->actualizarBannerPromocion((int)($_POST['id_banner_promocion'] ?? 0), $_POST, $_FILES);
        echo json_encode($resultado);
        break;

    case 'eliminarBanner':
        $resultado = $promocionController->eliminarBanner((int)($_POST['id_banner_promocion'] ?? 0));
        echo json_encode($resultado);
        break;


    default:
        echo json_encode(response::error('Acción no válida'));
}
