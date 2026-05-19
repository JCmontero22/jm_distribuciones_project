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
            $query = "INSERT INTO formula_presentacion (
                nombre_formula,
                cantidad_esencia,
                id_presentacion_insumo
            ) VALUES (:nombre, :cantidad_esencia, :id_presentacion_insumo)";

            $params = [
                ':nombre' => $data['nombre_formula'],
                ':cantidad_esencia' => $data['cantidad_esencia'],
                ':id_presentacion_insumo' => $data['id_presentacion_insumo'],
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
                            f.id_formula,
                            f.nombre_formula,
                            f.cantidad_esencia,
                            pp.nombre_presentacion
                        FROM formula_presentacion f
                        INNER JOIN productos_presentaciones pp ON pp.id_presentacion = f.id_presentacion_insumo";
                        
            return $this->select($query);
        }
    }
    