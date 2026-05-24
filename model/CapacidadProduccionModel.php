<?php

$projectRoot = __DIR__ . '/..';
require_once($projectRoot . '/core/conexion.php');

class CapacidadProduccionModel extends conexion {

    /**
     * Obtiene todas las esencias con su stock total en gramos, agrupadas por producto
     * Calcula el costo promedio ponderado entre diferentes presentaciones
     */
    public function obtenerEsenciasConStock(): array {
        $query = "SELECT
                    p.id_producto,
                    p.nombre_producto,
                    ie.id_sede,
                    SUM(ie.cantidad_gramos) AS cantidad_gramos,
                    SUM(ie.cantidad_gramos * ie.costo_por_gramo) / SUM(ie.cantidad_gramos) AS costo_por_gramo
                  FROM inventario_esencias ie
                  INNER JOIN productos_presentaciones pp ON ie.id_presentacion = pp.id_presentacion
                  INNER JOIN productos p ON pp.id_producto = p.id_producto
                  WHERE ie.cantidad_gramos > 0
                  GROUP BY p.id_producto, p.nombre_producto, ie.id_sede
                  ORDER BY p.nombre_producto";

        return $this->select($query);
    }

    /**
     * Obtiene todas las fórmulas que usan CUALQUIER presentación del mismo producto esencia
     */
    public function obtenerFormulasPorEsencia(int $idProducto): array {
        $query = "SELECT
                    f.id_formula,
                    f.nombre_formula,
                    f.cantidad_esencia,
                    f.id_presentacion_insumo
                  FROM formula_presentacion f
                  INNER JOIN productos_presentaciones pp ON f.id_presentacion_insumo = pp.id_presentacion
                  WHERE pp.id_producto = :id_producto";

        $params = [':id_producto' => $idProducto];
        return $this->select($query, $params);
    }

    /**
     * Obtiene todas las presentaciones (lociones preparadas) que usan una fórmula
     */
    public function obtenerPresentacionesPorFormula(int $idFormula): array {
        $query = "SELECT
                    pp.id_presentacion,
                    pp.nombre_presentacion,
                    pp.precio_venta_presentacion,
                    pp.precio_compra_presentacion,
                    p.nombre_producto
                  FROM productos_presentaciones pp
                  INNER JOIN productos p ON pp.id_producto = p.id_producto
                  WHERE pp.id_formula = :id_formula
                  AND pp.es_preparado_presentacion_producto = 1";

        $params = [':id_formula' => $idFormula];
        return $this->select($query, $params);
    }

    /**
     * Obtiene stock de esencia específica en una sede
     */
    public function obtenerStockEsencia(int $idPresentacion, int $idSede): ?array {
        $query = "SELECT * FROM inventario_esencias
                  WHERE id_presentacion = :id_presentacion
                  AND id_sede = :id_sede";

        $params = [
            ':id_presentacion' => $idPresentacion,
            ':id_sede' => $idSede
        ];

        $result = $this->select($query, $params);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Obtiene todas las fórmulas que existen
     */
    public function obtenerTodasLasFormulas(): array {
        $query = "SELECT
                    f.id_formula,
                    f.nombre_formula,
                    f.cantidad_esencia,
                    f.id_presentacion_insumo,
                    pp.nombre_presentacion,
                    pp.precio_compra_presentacion
                  FROM formula_presentacion f
                  LEFT JOIN productos_presentaciones pp ON pp.id_presentacion = f.id_presentacion_insumo";

        return $this->select($query);
    }

    /**
     * Obtiene el costo promedio ponderado de una esencia en una sede
     */
    public function obtenerCostoPromedioEsenciaPorProducto(int $idProducto, int $idSede): ?float {
        $query = "SELECT
                    SUM(ie.cantidad_gramos * ie.costo_por_gramo) / SUM(ie.cantidad_gramos) AS costo_promedio
                  FROM inventario_esencias ie
                  INNER JOIN productos_presentaciones pp ON ie.id_presentacion = pp.id_presentacion
                  WHERE pp.id_producto = :id_producto
                  AND ie.id_sede = :id_sede
                  AND ie.cantidad_gramos > 0";

        $params = [
            ':id_producto' => $idProducto,
            ':id_sede' => $idSede
        ];

        $result = $this->select($query, $params);
        return !empty($result) && !is_null($result[0]['costo_promedio']) ? (float)$result[0]['costo_promedio'] : null;
    }

    /**
     * Actualiza el precio de costo de una presentación
     */
    public function actualizarPrecioCostoPresentacion(int $idPresentacion, float $nuevoPrecio): bool {
        $query = "UPDATE productos_presentaciones
                  SET precio_compra_presentacion = :nuevo_precio
                  WHERE id_presentacion = :id_presentacion";

        $params = [
            ':nuevo_precio' => $nuevoPrecio,
            ':id_presentacion' => $idPresentacion
        ];

        $this->execute($query, $params);
        return true;
    }
}
