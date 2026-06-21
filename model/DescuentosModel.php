<?php 

    require_once(__DIR__ . '/../core/conexion.php');

    class DescuentosModel extends conexion
    {
        public function registrarDescuento(array $data): mixed {
            $query = "INSERT INTO descuentos (
                            nombre_descuento,
                            porcentaje_descuento,
                            fecha_inicio_descuento,
                            fecha_fin_descuento
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
            $query = "SELECT * FROM descuentos WHERE id_estado = 1";
            return $this->select($query);
        }

        public function obtenerDescuentoPorID(int $idDescuento): ?array {
            $query = "SELECT * FROM descuentos WHERE id_descuento = :idDescuento AND id_estado = 1";
            $params = [':idDescuento' => $idDescuento];
            $result = $this->select($query, $params);
            return !empty($result) ? $result[0] : null;
        }

        public function actualizarDescuento(int $idDescuento, array $data): bool {
            $query = "UPDATE descuentos SET
                        nombre_descuento = :nombreDescuento,
                        porcentaje_descuento = :porcentajeDescuento,
                        fecha_inicio_descuento = :fechaInicio,
                        fecha_fin_descuento = :fechaFin
                      WHERE id_descuento = :idDescuento";
            $params = [
                ':nombreDescuento' => $data['nombreDescuento'],
                ':porcentajeDescuento' => $data['porcentajeDescuento'],
                ':fechaInicio' => $data['fechaInicio'],
                ':fechaFin' => $data['fechaFin'],
                ':idDescuento' => $idDescuento
            ];
            return $this->execute($query, $params);
        }

        public function eliminarDescuento(int $idDescuento): bool {
            $query = "UPDATE descuentos SET id_estado = 2 WHERE id_descuento = :idDescuento";
            $params = [':idDescuento' => $idDescuento];
            return $this->execute($query, $params);
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
    
