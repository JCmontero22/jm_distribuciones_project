<?php 

    require_once(__DIR__ . '/../core/conexion.php');

    class DescuentosModel extends conexion
    {
        public function registrarDescuento(array $data): mixed {
            $query = "INSERT INTO descuentos (
                            nombre_descuento,
                            porcentaje_descuento,
                            fecha_inicio,
                            fecha_fin
                        )VALUES (
                            :nombreDescuento,
                            :porcentajeDescuento,
                            :fechaInicio,
                            :fechaFin
                        )";
            $params = [
                ':nombreDescuento' => $data['nombreDescuento'],
                ':porcentajeDescuento' => $data['porcentajeDescuento'],
                ':fechaInicio' => $data['fechaInicio'],
                ':fechaFin' => $data['fechaFin']
            ];
            return $this->execute($query, $params);
        }

        public function obtenerDescuentos(): array {
            $query = "SELECT * FROM descuentos";
            return $this->select($query);
        }

        public function obtenerDescuentoPorID(int $idDescuento): ?array {
            $query = "SELECT * FROM descuentos WHERE id_descuento = :idDescuento";
            $params = [':idDescuento' => $idDescuento];
            $result = $this->select($query, $params);
            return !empty($result) ? $result[0] : null;
        }

        public function asignarDescuentoAPresentaciones(int $idDescuento, array $idsProductos): bool {
            $placeholders = [];
            $params = [':idDescuento' => $idDescuento];

            foreach ($idsProductos as $idx => $id) {
                $key = ":id_{$idx}";
                $placeholders[] = $key;
                $params[$key] = $id;
            }

            $placeholderStr = implode(',', $placeholders);
            $query = "UPDATE productos_presentaciones
                      SET id_descuento = :idDescuento
                      WHERE id_presentacion IN ($placeholderStr)";

            return $this->execute($query, $params);
        }

        public function removerDescuentoDePresentaciones(array $idsProductos): bool {
            $placeholders = [];
            $params = [];

            foreach ($idsProductos as $idx => $id) {
                $key = ":id_{$idx}";
                $placeholders[] = $key;
                $params[$key] = $id;
            }

            $placeholderStr = implode(',', $placeholders);
            $query = "UPDATE productos_presentaciones
                      SET id_descuento = NULL
                      WHERE id_presentacion IN ($placeholderStr)";

            return $this->execute($query, $params);
        }

    }
    