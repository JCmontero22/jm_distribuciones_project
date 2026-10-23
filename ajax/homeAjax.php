<?php
require_once('../config/config.php');
require_once(__DIR__ . '/../core/Auth.php');
Auth::requireApi('homeAjax');

header('Content-Type: application/json');

$projectRoot = __DIR__ . '/..';
require_once($projectRoot . '/core/response.php');
require_once($projectRoot . '/model/HomeModel.php');

$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;

$model = new HomeModel();

switch ($accion) {
    case 'obtenerResumen':
        try {
            // Vendedor: solo ve su sede. Administrador (sin sede asignada): ve todas.
            $idSede = Auth::sedeUsuario();

            $ventasHoy   = $model->obtenerVentasDeHoy($idSede);
            $cuentas     = $model->obtenerCuentasPorCobrar($idSede);

            echo json_encode(response::success([
                'ventasHoy'        => (int)$ventasHoy['cantidad'],
                'totalVentasHoy'   => (float)$ventasHoy['total'],
                'recaudoHoy'       => $model->obtenerRecaudoDeHoy($idSede),
                'ventasPendientes' => (int)$cuentas['cantidad'],
                'porCobrar'        => (float)$cuentas['saldo'],
                'esenciasEnStock'  => $model->contarEsenciasEnStock($idSede),
                'ventasRecientes' => $model->obtenerVentasRecientesDeHoy($idSede),
                'stockBajo'        => [
                    'unidades' => $model->obtenerStockBajoUnidades($idSede),
                    'esencias' => $model->obtenerStockBajoEsencias($idSede),
                ],
                'umbrales'         => [
                    'unidades' => HomeModel::UMBRAL_UNIDADES,
                    'gramos'   => HomeModel::UMBRAL_GRAMOS,
                ],
            ]));
        } catch (Exception $e) {
            Logger::error("Error al obtener resumen del inicio", $e);
            echo json_encode(response::error('Error al obtener el resumen'));
        }
        break;

    default:
        echo json_encode(response::error('Acción no válida'));
}
