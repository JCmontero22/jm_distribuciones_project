<?php

$projectRoot = __DIR__ . '/..';
require_once($projectRoot . '/core/conexion.php');

class CapacidadProduccionModel extends conexion {

    public function obtenerEsenciasParaInforme(): array {
        $query = "SELECT
                    p.id_producto,
                    p.nombre_producto,
                    SUM(COALESCE(ie.cantidad_gramos, 0)) AS total_gramos
                  FROM productos p
                  INNER JOIN productos_presentaciones pp ON p.id_producto = pp.id_producto
                  INNER JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                  LEFT JOIN inventario_esencias ie ON pp.id_presentacion = ie.id_presentacion
                  WHERE c.nombre_categoria = 'Esencias'
                  GROUP BY p.id_producto, p.nombre_producto
                  HAVING total_gramos > 0
                  ORDER BY p.nombre_producto";

        return $this->select($query);
    }

    public function obtenerFormulasConInsumo(): array {
        $query = "SELECT
                    f.id_formula AS id_formula,
                    f.nombre_formula,
                    f.cantidad_esencia,
                    inf.id_insumo_formula,
                    inf.nombre_insumo,
                    inf.tamanio_insumo,
                    tc.id_tipo_concentracion,
                    tc.nombre_concentracion
                  FROM formulas f
                  LEFT JOIN insumo_formulas inf ON f.id_insumo_formula = inf.id_insumo_formula
                  LEFT JOIN tipo_concentracion tc ON f.id_tipo_concentracion = tc.id_tipo_concentracion
                  WHERE (f.id_estado = 1 OR f.id_estado IS NULL)
                  ORDER BY inf.tamanio_insumo, tc.nombre_concentracion";

        return $this->select($query);
    }
}
