<?php

$projectRoot = __DIR__ . '/..';
require_once($projectRoot . '/model/CapacidadProduccionModel.php');

class CapacidadProduccionService {

    private CapacidadProduccionModel $modelo;

    public function __construct(CapacidadProduccionModel $modelo) {
        $this->modelo = $modelo;
    }

    /**
     * Calcula la capacidad de producción basada en stocks de esencias
     * Retorna un array con esencias y sus capacidades de producción
     */
    public function calcularCapacidad(): array {
        $esenciasConStock = $this->modelo->obtenerEsenciasConStock();

        if (empty($esenciasConStock)) {
            return [
                'success' => true,
                'esencias' => []
            ];
        }

        $resultado = [
            'success' => true,
            'esencias' => []
        ];

        foreach ($esenciasConStock as $esencia) {
            $formulasQueusan = $this->modelo->obtenerFormulasPorEsencia($esencia['id_producto']);

            if (empty($formulasQueusan)) {
                continue;
            }

            $capacidadPorFormula = [];

            foreach ($formulasQueusan as $formula) {
                $presentacionesFormula = $this->modelo->obtenerPresentacionesPorFormula($formula['id_formula']);

                foreach ($presentacionesFormula as $presentacion) {
                    $cantidadQuePuedoProducir = (int) floor($esencia['cantidad_gramos'] / $formula['cantidad_esencia']);

                    $capacidadPorFormula[] = [
                        'id_presentacion' => $presentacion['id_presentacion'],
                        'nombre_locion' => $presentacion['nombre_producto'] . ' - ' . $presentacion['nombre_presentacion'],
                        'gramos_necesarios' => (float) $formula['cantidad_esencia'],
                        'cantidad_puede_producir' => $cantidadQuePuedoProducir,
                        'precio_venta' => (float) $presentacion['precio_venta_presentacion'],
                        'precio_costo' => (float) $presentacion['precio_compra_presentacion']
                    ];
                }
            }

            if (!empty($capacidadPorFormula)) {
                $resultado['esencias'][] = [
                    'id_producto' => $esencia['id_producto'],
                    'nombre_esencia' => $esencia['nombre_producto'],
                    'nombre_presentacion' => 'Agregado',
                    'cantidad_gramos' => (float) $esencia['cantidad_gramos'],
                    'costo_por_gramo' => (float) $esencia['costo_por_gramo'],
                    'capacidad_por_formula' => $capacidadPorFormula
                ];
            }
        }

        return $resultado;
    }
}
