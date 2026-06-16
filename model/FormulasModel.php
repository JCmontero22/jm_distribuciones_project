<?php

    require_once('../core/conexion.php');

    class FormulasModel extends conexion
    {

        public function existeFormula(string $nombre) : bool {
            $query = "SELECT COUNT(*) AS total FROM formulas WHERE nombre_formula = :nombre AND id_estado = 1";
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
                            f.id_formula AS id_formula,
                            f.nombre_formula,
                            f.cantidad_esencia,
                            inf.nombre_insumo,
                            inf.tamanio_insumo,
                            tc.nombre_concentracion
                        FROM formulas f
                        LEFT JOIN insumo_formulas inf ON inf.id_insumo_formula = f.id_insumo_formula
                        LEFT JOIN tipo_concentracion tc ON tc.id_tipo_concentracion = f.id_tipo_concentracion
                        WHERE f.id_estado = 1";

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

        public function obtenerFormulaPorId(int $id): array {
            $query = "SELECT id_formula, nombre_formula, id_tipo_concentracion, cantidad_esencia, id_insumo_formula FROM formulas WHERE id_estado = 1 AND id_formula = :id";
            return $this->select($query, [':id' => $id]);
        }

        public function actualizarFormula(array $data): bool {
            $query = "UPDATE formulas SET
                        nombre_formula = :nombre,
                        cantidad_esencia = :cantidad_esencia,
                        id_insumo_formula = :id_insumo_formula,
                        id_tipo_concentracion = :id_tipo_concentracion
                      WHERE id_formula = :id";

            $params = [
                ':nombre'                => $data['nombre_formula'],
                ':cantidad_esencia'      => $data['cantidad_esencia'],
                ':id_insumo_formula'     => $data['id_insumo_formula'],
                ':id_tipo_concentracion' => $data['id_tipo_concentracion'],
                ':id'                    => $data['id_formula'],
            ];
            return $this->execute($query, $params);
        }

        public function eliminarFormula(int $idFormula): bool {
            $query = "UPDATE formulas SET id_estado = 2 WHERE id_formula = :id";
            return $this->execute($query, [':id' => $idFormula]);
        }
    }
