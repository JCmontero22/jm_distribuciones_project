<?php

require_once(__DIR__ . '/../core/conexion.php');

class GastosModel extends conexion {

    /**
     * Registra el gasto y su egreso en caja en una sola transaccion.
     */
    public function registrarGasto(array $data): int {
        $this->beginTransaction();

        try {
            $idGasto = (int)$this->execute(
                "INSERT INTO gastos (
                    id_sede, categoria_gasto, descripcion_gasto, monto_gasto,
                    id_metodo_pago, id_usuario, fecha_gasto
                ) VALUES (
                    :id_sede, :categoria, :descripcion, :monto,
                    :id_metodo, :id_usuario, :fecha
                )",
                [
                    ':id_sede' => $data['idSede'],
                    ':categoria' => $data['categoriaGasto'],
                    ':descripcion' => $data['descripcionGasto'],
                    ':monto' => $data['montoGasto'],
                    ':id_metodo' => $data['idMetodoPago'],
                    ':id_usuario' => $data['idUsuario'],
                    ':fecha' => $data['fechaGasto']
                ]
            );

            $this->execute(
                "INSERT INTO caja_movimientos (
                    id_sede, tipo_caja, concepto_caja, monto_caja,
                    id_metodo_pago, id_gasto, id_usuario, observacion_caja
                ) VALUES (
                    :id_sede, 'egreso', 'gasto', :monto,
                    :id_metodo, :id_gasto, :id_usuario, :obs
                )",
                [
                    ':id_sede' => $data['idSede'],
                    ':monto' => $data['montoGasto'],
                    ':id_metodo' => $data['idMetodoPago'],
                    ':id_gasto' => $idGasto,
                    ':id_usuario' => $data['idUsuario'],
                    ':obs' => "Gasto: {$data['categoriaGasto']}"
                ]
            );

            $this->commit();
            return $idGasto;

        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    public function obtenerGastos(int $idSede, string $desde, string $hasta): array {
        return $this->select(
            "SELECT
                g.id_gasto,
                g.id_sede,
                s.nombre_sede,
                g.categoria_gasto,
                g.descripcion_gasto,
                g.monto_gasto,
                g.id_metodo_pago,
                mp.nombre_metodo_pago,
                g.id_usuario,
                u.nombre_usuario,
                g.fecha_gasto
             FROM gastos g
             INNER JOIN sedes s ON s.id_sede = g.id_sede
             LEFT JOIN metodos_pago mp ON mp.id_metodo_pago = g.id_metodo_pago
             INNER JOIN usuario u ON u.id_usuario = g.id_usuario
             WHERE g.id_sede = :id_sede AND g.fecha_gasto BETWEEN :desde AND :hasta
             ORDER BY g.fecha_gasto DESC, g.id_gasto DESC",
            [':id_sede' => $idSede, ':desde' => $desde, ':hasta' => $hasta]
        );
    }
}
