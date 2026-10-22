<?php

$projectRoot = __DIR__ . '/..';
require_once($projectRoot . '/core/conexion.php');

class CapacidadProduccionModel extends conexion {

    /**
     * Gramos disponibles de cada esencia en la sede indicada.
     */

    public function obtenerEsenciasParaInforme(int $idSede): array {

        $idSede = isset($idSede) && !empty($idSede) ? (int)$idSede : $_SESSION['usuario']['id_sede'];

        $query = "SELECT
                    p.id_producto,
                    p.nombre_producto,
                    SUM(COALESCE(ie.cantidad_gramos, 0)) AS total_gramos
                  FROM productos p
                  INNER JOIN productos_presentaciones pp ON p.id_producto = pp.id_producto
                  INNER JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                  LEFT JOIN inventario_esencias ie
                    ON pp.id_presentacion = ie.id_presentacion AND ie.id_sede = :id_sede
                  WHERE c.nombre_categoria = 'Esencias'
                  GROUP BY p.id_producto, p.nombre_producto
                  HAVING total_gramos > 0
                  ORDER BY p.nombre_producto";

        return $this->select($query, [':id_sede' => $idSede]);
    }

    public function obtenerFormulasConInsumo(): array {
        $query = "SELECT
                    f.id_formula AS id_formula,
                    f.nombre_formula,
                    f.cantidad_esencia,
                    inf.id_insumo_formula,
                    COALESCE(inf.nombre_insumo, CONCAT(p.nombre_producto, ' ', pp.nombre_presentacion)) AS nombre_insumo,
                    COALESCE(inf.tamanio_insumo, pp.nombre_presentacion) AS tamanio_insumo,
                    tc.id_tipo_concentracion,
                    tc.nombre_concentracion
                  FROM formulas f
                  LEFT JOIN insumo_formulas inf ON f.id_insumo_formula = inf.id_insumo_formula
                  LEFT JOIN productos_presentaciones pp ON pp.id_presentacion = f.id_presentacion_envase
                  LEFT JOIN productos p ON p.id_producto = pp.id_producto
                  LEFT JOIN tipo_concentracion tc ON f.id_tipo_concentracion = tc.id_tipo_concentracion
                  WHERE (f.id_estado = 1 OR f.id_estado IS NULL)
                  ORDER BY inf.tamanio_insumo, tc.nombre_concentracion";

        return $this->select($query);
    }
}
