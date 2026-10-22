<?php

require_once(__DIR__ . '/../core/conexion.php');

/**
 * Consultas del balance mensual por sede. Todas filtran por mes (YYYY-MM).
 * Las ventas anuladas no cuentan en ventas, costo, ganancia ni recaudo.
 */
class BalanceModel extends conexion {

    /** Sedes activas, o solo la indicada. */
    public function obtenerSedes(?int $idSede = null): array {
        $where = "id_estado = 1";
        $params = [];
        if ($idSede) {
            $where .= " AND id_sede = :id_sede";
            $params[':id_sede'] = $idSede;
        }
        return $this->select("SELECT id_sede, nombre_sede FROM sedes WHERE {$where} ORDER BY nombre_sede", $params);
    }

    /** Facturado, costo y cantidad de ventas del mes por sede (sin anuladas). */
    public function ventasDelMes(string $mes, ?int $idSede = null): array {
        [$filtroSede, $params] = $this->filtroSede($idSede);
        $params[':mes'] = $mes;
        return $this->select(
            "SELECT v.id_sede,
                    COUNT(*) AS cantidad_ventas,
                    COALESCE(SUM(v.total_venta), 0) AS facturado,
                    COALESCE(SUM(v.costo_total_venta), 0) AS costo_ventas
             FROM ventas v
             WHERE v.estado_venta <> 'anulada'
               AND DATE_FORMAT(v.fecha_venta, '%Y-%m') = :mes {$filtroSede}
             GROUP BY v.id_sede",
            $params
        );
    }

    /** Dinero cobrado del mes (pagos de ventas no anuladas), por sede. Incluye abonos de ventas de otros meses. */
    public function recaudoDelMes(string $mes, ?int $idSede = null): array {
        [$filtroSede, $params] = $this->filtroSede($idSede, 'v');
        $params[':mes'] = $mes;
        return $this->select(
            "SELECT v.id_sede, COALESCE(SUM(pv.monto_pago), 0) AS recaudado
             FROM pagos_venta pv
             INNER JOIN ventas v ON v.id_venta = pv.id_venta
             WHERE v.estado_venta <> 'anulada'
               AND DATE_FORMAT(pv.fecha_pago, '%Y-%m') = :mes {$filtroSede}
             GROUP BY v.id_sede",
            $params
        );
    }

    /** Saldo por cobrar de las ventas del mes (facturado - pagado), por sede. */
    public function porCobrarDelMes(string $mes, ?int $idSede = null): array {
        [$filtroSede, $params] = $this->filtroSede($idSede, 'v');
        $params[':mes'] = $mes;
        return $this->select(
            "SELECT v.id_sede,
                    COALESCE(SUM(GREATEST(0, v.total_venta - COALESCE(p.pagado, 0))), 0) AS por_cobrar
             FROM ventas v
             LEFT JOIN (SELECT id_venta, SUM(monto_pago) AS pagado FROM pagos_venta GROUP BY id_venta) p
                    ON p.id_venta = v.id_venta
             WHERE v.estado_venta <> 'anulada'
               AND DATE_FORMAT(v.fecha_venta, '%Y-%m') = :mes {$filtroSede}
             GROUP BY v.id_sede",
            $params
        );
    }

    /** Gastos del mes por sede. */
    public function gastosDelMes(string $mes, ?int $idSede = null): array {
        [$filtroSede, $params] = $this->filtroSede($idSede);
        $params[':mes'] = $mes;
        return $this->select(
            "SELECT id_sede, COALESCE(SUM(monto_gasto), 0) AS gastos
             FROM gastos
             WHERE DATE_FORMAT(fecha_gasto, '%Y-%m') = :mes {$filtroSede}
             GROUP BY id_sede",
            $params
        );
    }

    /** Gastos del mes agrupados por categoria (para el detalle de la sede o de todas). */
    public function gastosPorCategoria(string $mes, ?int $idSede = null): array {
        [$filtroSede, $params] = $this->filtroSede($idSede);
        $params[':mes'] = $mes;
        return $this->select(
            "SELECT categoria_gasto, COALESCE(SUM(monto_gasto), 0) AS total
             FROM gastos
             WHERE DATE_FORMAT(fecha_gasto, '%Y-%m') = :mes {$filtroSede}
             GROUP BY categoria_gasto
             ORDER BY total DESC",
            $params
        );
    }

    /** Compras del mes por sede (lo que se reinvirtio en mercancia). */
    public function comprasDelMes(string $mes, ?int $idSede = null): array {
        [$filtroSede, $params] = $this->filtroSede($idSede);
        $params[':mes'] = $mes;
        return $this->select(
            "SELECT id_sede, COALESCE(SUM(total_compra), 0) AS compras
             FROM compras
             WHERE DATE_FORMAT(fecha_compra, '%Y-%m') = :mes {$filtroSede}
             GROUP BY id_sede",
            $params
        );
    }

    /**
     * Filtro de sede. El alias indica la tabla que tiene la columna id_sede.
     * Devuelve [sql, params].
     */
    private function filtroSede(?int $idSede, string $alias = ''): array {
        $col = $alias ? "{$alias}.id_sede" : "id_sede";
        if (!$idSede) {
            return ['', []];
        }
        return ["AND {$col} = :id_sede", [':id_sede' => $idSede]];
    }
}
