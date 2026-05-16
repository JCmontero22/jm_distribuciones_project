<?php

    require_once('../core/conexion.php');

    class FormulasModel extends conexion
    {

        public function existeFormula(string $nombre) : bool {
            $query = "SELECT COUNT(*) AS total  FROM formulas WHERE nombre_formula = :nombre";

            $params = [':nombre' => $nombre];

            $result = $this->select($query, $params);

            return !empty($result) && $result[0]['total'] > 0;
        }

        public function registrarFormula(array $data): mixed {
            $query = "INSERT INTO formulas (
                nombre_formula,
                id_presentacion_final,
                id_presentacion_insumo,
                cantidad_requerida
            ) VALUES (:nombre, :id_presentacion_final, :id_presentacion_insumo, :cantidad_requerida)";

            $params = [
                ':nombre' => $data['nombre'],
                ':id_presentacion_final' => $data['id_presentacion_final'],
                ':id_presentacion_insumo' => $data['id_presentacion_insumo'],
                ':cantidad_requerida' => $data['cantidad_requerida'],
            ];
            return $this->execute($query, $params);
        }

        public function obtenerPresentacionesPorCategoria(string $categoria): array {
            $query = "SELECT pp.id_presentacion, pp.nombre_presentacion, p.nombre_producto
                      FROM productos_presentaciones pp
                      JOIN productos p ON p.id_producto = pp.id_producto
                      JOIN categoria_producto cp ON cp.id_categoria = p.id_categoria
                      WHERE cp.nombre_categoria = :categoria
                      ORDER BY p.nombre_producto, pp.nombre_presentacion";
            return $this->select($query, [':categoria' => $categoria]);
        }

        public function obtenerFormulas(): array {
            $query = "SELECT
                            f.nombre_formula,
                            pp.nombre_presentacion AS presentacion_final,
                            GROUP_CONCAT(CONCAT(pp2.nombre_presentacion, ' (', f.cantidad_requerida, ')') SEPARATOR ', ') AS insumos_utilizados
                        FROM formulas f
                        LEFT JOIN productos_presentaciones pp ON pp.id_presentacion = f.id_presentacion_final
                        LEFT JOIN productos_presentaciones pp2 ON pp2.id_presentacion = f.id_presentacion_insumo
                        GROUP BY f.nombre_formula, pp.nombre_presentacion
                        ORDER BY f.nombre_formula ASC";
            return $this->select($query);
        }
    }
    