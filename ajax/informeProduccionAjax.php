<?php
require_once('../config/config.php');

header('Content-Type: application/json');

$projectRoot = __DIR__ . '/..';
require_once($projectRoot . '/core/response.php');
require_once($projectRoot . '/model/CapacidadProduccionModel.php');

$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;

$model = new CapacidadProduccionModel();

switch ($accion) {
    case 'obtenerDatosInforme':
        try {
            $esencias = $model->obtenerEsenciasParaInforme();
            $formulas = $model->obtenerFormulasConInsumo();

            $capacidades = [];
            foreach ($formulas as $formula) {
                $idFormula     = $formula['id_formula'];
                $gramosFormula = (float) $formula['cantidad_esencia'];

                $capacidades[$idFormula] = [];
                foreach ($esencias as $esencia) {
                    $totalGramos = (float) $esencia['total_gramos'];
                    $capacidades[$idFormula][$esencia['id_producto']] = $gramosFormula > 0
                        ? (int) floor($totalGramos / $gramosFormula)
                        : 0;
                }
            }

            echo json_encode(response::success([
                'esencias'    => $esencias,
                'formulas'    => $formulas,
                'capacidades' => $capacidades
            ]));
        } catch (Exception $e) {
            echo json_encode(response::error('Error al obtener datos del informe: ' . $e->getMessage()));
        }
        break;

    default:
        echo json_encode(response::error('Acción no válida'));
}
