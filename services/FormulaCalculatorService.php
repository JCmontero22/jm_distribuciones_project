<?php

require_once('../model/CapacidadProduccionModel.php');

class FormulaCalculatorService {

    private CapacidadProduccionModel $capacidadModel;

    public function __construct() {
        $this->capacidadModel = new CapacidadProduccionModel();
    }

    /**
     * Cuando se compra una esencia, recalcula el costo de producción
     * de TODAS las lociones preparadas que usan esa esencia
     * Ahora usa el costo PROMEDIO PONDERADO de todas las presentaciones del mismo producto
     */
    public function recalcularCostoProduccionPorEsencia(int $idProductoEsencia, int $idSede): void {
        // 1. Obtener TODAS las fórmulas que usan esta esencia (por producto)
        $formulasAfectadas = $this->capacidadModel->obtenerFormulasPorEsencia($idProductoEsencia);

        if (empty($formulasAfectadas)) {
            return;
        }

        // 2. Obtener el costo promedio ponderado de esta esencia en la sede
        $costoPorGramoPromedio = $this->capacidadModel->obtenerCostoPromedioEsenciaPorProducto($idProductoEsencia, $idSede);

        if ($costoPorGramoPromedio === null) {
            error_log("FormulaCalculatorService: No se pudo obtener costo promedio para producto $idProductoEsencia en sede $idSede");
            return;
        }

        // 3. Para cada fórmula, obtener las presentaciones que la usan
        foreach ($formulasAfectadas as $formula) {
            $presentacionesFormula = $this->capacidadModel->obtenerPresentacionesPorFormula($formula['id_formula']);

            if (empty($presentacionesFormula)) {
                continue;
            }

            // 4. Calcular nuevo costo de producción para cada presentación usando el costo promedio
            foreach ($presentacionesFormula as $presentacion) {
                $costoProduccion = $this->calcularCostoFormula($formula, $costoPorGramoPromedio);
                $this->actualizarPrecioCosto($presentacion['id_presentacion'], $costoProduccion);
            }
        }
    }

    /**
     * Calcula el costo total de producción de una fórmula usando el costo promedio ponderado
     */
    private function calcularCostoFormula(array $formula, float $costoPorGramoEsencia): float {
        // La fórmula requiere X gramos de esencia
        // Costo = X gramos × costo_por_gramo_promedio
        return (float) $formula['cantidad_esencia'] * $costoPorGramoEsencia;
    }

    /**
     * Actualiza el precio de costo de una presentación
     */
    private function actualizarPrecioCosto(int $idPresentacion, float $nuevoPrecio): void {
        $this->capacidadModel->actualizarPrecioCostoPresentacion($idPresentacion, $nuevoPrecio);
    }
}
