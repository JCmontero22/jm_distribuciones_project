<?php

    require_once('../core/conexion.php');

    class FormulasModel extends conexion
    {

        public function existeFormula(string $nombre) : bool {
            $query = "SELECT COUNT(*) AS total FROM formulas WHERE nombre_formula = :nombre";
            $result = $this->select($query, [':nombre' => $nombre]);
            return !empty($result) && $result[0]['total'] > 0;
        }

        public function registrarFormula(array $data): mixed {
            $query = "INSERT INTO formulas (
                nombre_formula,
                cantidad_esencia,
                id_insumo_formula,
                id_tipo_concentracion
            ) VALUES (:nombre, :cantidad_esencia, :id_insumo_formula, :id_tipo_concentracion)";

            $params = [
                ':nombre'                => $data['nombre_formula'],
                ':cantidad_esencia'      => $data['cantidad_esencia'],
                ':id_insumo_formula'     => $data['id_insumo_formula'],
                ':id_tipo_concentracion' => $data['id_tipo_concentracion'],
            ];
            return $this->execute($query, $params);
        }

        public function obtenerFormulas(): array {
            $query = "SELECT
                            f.id_fomulas AS id_formula,
                            f.nombre_formula,
                            f.cantidad_esencia,
                            inf.nombre_insumo,
                            inf.tamanio_insumo,
                            tc.nombre_concentracion
                        FROM formulas f
                        LEFT JOIN insumo_formulas inf ON inf.id_insumo_formula = f.id_insumo_formula
                        LEFT JOIN tipo_concentracion tc ON tc.id_tipo_concentracion = f.id_tipo_concentracion";

            return $this->select($query);
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
    }
