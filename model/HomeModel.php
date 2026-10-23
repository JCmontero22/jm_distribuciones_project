<?php

    require_once(__DIR__ . '/../core/conexion.php');

    class HomeModel extends conexion {

        // El esquema no guarda stock mínimo: estos umbrales definen cuándo un producto está bajo.
        const UMBRAL_UNIDADES = 5;
        const UMBRAL_GRAMOS   = 100;

        // Agrega "AND <alias>.id_sede = :sede" cuando hay sede; sin sede (administrador) muestra todas.
        private function filtroSede(string $alias, ?int $idSede, array &$params): string {
            if ($idSede === null) {
                return '';
            }
            $params[':sede_' . $alias] = $idSede;
            return " AND {$alias}.id_sede = :sede_{$alias}";
        }

        public function obtenerVentasDeHoy(?int $idSede): array {
            $params = [];
            $filtro = $this->filtroSede('v', $idSede, $params);
            $query = "SELECT COUNT(*) AS cantidad, COALESCE(SUM(v.total_venta), 0) AS total
                      FROM ventas v
                      WHERE DATE(v.fecha_venta) = CURDATE() AND v.estado_venta <> 'anulada' $filtro";
            return $this->select($query, $params)[0] ?? ['cantidad' => 0, 'total' => 0];
        }

        public function obtenerVentasRecientesDeHoy(?int $idSede): array {
            $params = [];
            $filtro = $this->filtroSede('v', $idSede, $params);
            $query = "SELECT v.numero_venta, COALESCE(c.nombre_cliente, 'Sin cliente') AS cliente,
                             s.nombre_sede, v.total_venta, v.estado_venta
                      FROM ventas v
                      INNER JOIN sedes s ON s.id_sede = v.id_sede
                      LEFT JOIN cliente c ON c.id_cliente = v.id_cliente
                      WHERE DATE(v.fecha_venta) = CURDATE() $filtro
                      ORDER BY v.fecha_venta DESC, v.id_venta DESC
                      LIMIT 8";
            return $this->select($query, $params);
        }

        public function obtenerRecaudoDeHoy(?int $idSede): float {
            $params = [];
            $filtro = $this->filtroSede('v', $idSede, $params);
            $query = "SELECT COALESCE(SUM(pv.monto_pago), 0) AS total
                      FROM pagos_venta pv
                      INNER JOIN ventas v ON v.id_venta = pv.id_venta
                      WHERE DATE(pv.fecha_pago) = CURDATE() AND v.estado_venta <> 'anulada' $filtro";
            return (float)($this->select($query, $params)[0]['total'] ?? 0);
        }

        public function obtenerCuentasPorCobrar(?int $idSede): array {
            $params = [];
            $filtro = $this->filtroSede('v', $idSede, $params);
            // Misma regla que VentasModel::obtenerResumenVentas: saldo nunca negativo y solo cuentan saldos > 0
            $query = "SELECT COUNT(*) AS cantidad, COALESCE(SUM(s.saldo), 0) AS saldo
                      FROM (
                          SELECT GREATEST(0, v.total_venta - COALESCE(p.pagado, 0)) AS saldo
                          FROM ventas v
                          LEFT JOIN (
                              SELECT id_venta, SUM(monto_pago) AS pagado
                              FROM pagos_venta
                              GROUP BY id_venta
                          ) p ON p.id_venta = v.id_venta
                          WHERE v.estado_venta = 'pendiente' $filtro
                      ) s
                      WHERE s.saldo > 0";
            return $this->select($query, $params)[0] ?? ['cantidad' => 0, 'saldo' => 0];
        }

        public function contarEsenciasEnStock(?int $idSede): int {
            $params = [];
            $filtro = $this->filtroSede('ie', $idSede, $params);
            $query = "SELECT COUNT(DISTINCT ie.id_presentacion) AS cantidad
                      FROM inventario_esencias ie
                      WHERE ie.cantidad_gramos > 0 $filtro";
            return (int)($this->select($query, $params)[0]['cantidad'] ?? 0);
        }

        public function obtenerStockBajoUnidades(?int $idSede): array {
            $params = [':umbral' => self::UMBRAL_UNIDADES];
            $filtro = $this->filtroSede('iss', $idSede, $params);
            $query = "SELECT pr.nombre_producto, pp.nombre_presentacion, s.nombre_sede, iss.stock_sede AS stock
                      FROM inventario_sedes iss
                      INNER JOIN productos_presentaciones pp ON pp.id_presentacion = iss.id_presentacion AND pp.id_estado = 1
                      INNER JOIN productos pr ON pr.id_producto = pp.id_producto AND pr.id_estado = 1
                      INNER JOIN categoria_producto c ON c.id_categoria = pr.id_categoria AND c.nombre_categoria <> 'Esencias'
                      INNER JOIN sedes s ON s.id_sede = iss.id_sede
                      WHERE iss.stock_sede <= :umbral $filtro
                      ORDER BY iss.stock_sede ASC, pr.nombre_producto ASC
                      LIMIT 50";
            return $this->select($query, $params);
        }

        public function obtenerStockBajoEsencias(?int $idSede): array {
            $params = [':umbral' => self::UMBRAL_GRAMOS];
            $filtro = $this->filtroSede('ie', $idSede, $params);
            $query = "SELECT pr.nombre_producto, pp.nombre_presentacion, s.nombre_sede, ie.cantidad_gramos AS stock
                      FROM inventario_esencias ie
                      INNER JOIN productos_presentaciones pp ON pp.id_presentacion = ie.id_presentacion AND pp.id_estado = 1
                      INNER JOIN productos pr ON pr.id_producto = pp.id_producto AND pr.id_estado = 1
                      INNER JOIN categoria_producto c ON c.id_categoria = pr.id_categoria AND c.nombre_categoria = 'Esencias'
                      INNER JOIN sedes s ON s.id_sede = ie.id_sede
                      WHERE ie.cantidad_gramos <= :umbral $filtro
                      ORDER BY ie.cantidad_gramos ASC, pr.nombre_producto ASC
                      LIMIT 50";
            return $this->select($query, $params);
        }
    }
