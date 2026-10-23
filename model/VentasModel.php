<?php

require_once(__DIR__ . '/../core/conexion.php');

/**
 * Modelo de ventas.
 *
 * Reglas de consumo y costo (ver db/migrations/003 y 004):
 *  - Reventa (id_tipo_producto = 1): descuenta unidades de inventario_sedes; costo = precio_compra_presentacion.
 *  - Locion (esencia + formula): gramos de la esencia por FIFO de lotes_esencia y 1 envase de la formula.
 *    Costo = consumo real de lotes + envase. Precio por esencia, formula y sede (precios_locion_sede).
 *  - Las esencias son insumos: no se venden directamente.
 *
 * Toda venta, abono o anulacion corre en una transaccion. Las filas de inventario se bloquean
 * con SELECT ... FOR UPDATE antes de validar stock, en el mismo orden siempre (evita sobreventa y deadlocks).
 */
class VentasModel extends conexion {

    private const TIPO_REVENTA = 1;
    /** Categoria virtual de las lociones calculadas (no existe en categoria_producto). */
    public const ID_CATEGORIA_LOCIONES = 0;

    // ============================================================
    // LECTURAS
    // ============================================================

    /**
     * Ventas con filtros opcionales: sede, estado, rango de fechas (Y-m-d) y texto (numero, cliente o vendedor).
     */
    public function obtenerVentas(?int $idSede = null, ?string $estado = null, ?string $desde = null, ?string $hasta = null, ?string $texto = null): array {
        $where = ["1 = 1"];
        $params = [];

        if ($idSede !== null && $idSede > 0) {
            $where[] = "v.id_sede = :id_sede";
            $params[':id_sede'] = $idSede;
        }
        if (!empty($estado)) {
            $where[] = "v.estado_venta = :estado";
            $params[':estado'] = $estado;
        }
        if (!empty($desde)) {
            $where[] = "DATE(v.fecha_venta) >= :desde";
            $params[':desde'] = $desde;
        }
        if (!empty($hasta)) {
            $where[] = "DATE(v.fecha_venta) <= :hasta";
            $params[':hasta'] = $hasta;
        }
        if (!empty($texto)) {
            $where[] = "(v.numero_venta LIKE :texto1 OR c.nombre_cliente LIKE :texto2 OR u.nombre_usuario LIKE :texto3)";
            $params[':texto1'] = '%' . $texto . '%';
            $params[':texto2'] = '%' . $texto . '%';
            $params[':texto3'] = '%' . $texto . '%';
        }

        $query = "SELECT
                    v.id_venta,
                    v.id_sede,
                    v.numero_venta,
                    v.id_cliente,
                    v.id_usuario,
                    v.subtotal_venta,
                    v.descuento_venta,
                    v.total_venta,
                    v.costo_total_venta,
                    v.estado_venta,
                    v.fecha_venta,
                    v.observacion_venta,
                    COALESCE(pv.total_pagado, 0) AS total_pagado,
                    GREATEST(0, v.total_venta - COALESCE(pv.total_pagado, 0)) AS saldo_pendiente,
                    s.nombre_sede,
                    c.nombre_cliente,
                    u.nombre_usuario
                  FROM ventas v
                  LEFT JOIN (
                        SELECT id_venta, SUM(monto_pago) AS total_pagado
                        FROM pagos_venta
                        GROUP BY id_venta
                  ) pv ON pv.id_venta = v.id_venta
                  LEFT JOIN sedes s ON v.id_sede = s.id_sede
                  LEFT JOIN cliente c ON v.id_cliente = c.id_cliente
                  LEFT JOIN usuario u ON v.id_usuario = u.id_usuario
                  WHERE " . implode(' AND ', $where) . "
                  ORDER BY v.id_venta DESC";

        return $this->select($query, $params);
    }

    public function obtenerResumenVentas(?int $idSede = null): array {
        $ventas = $this->obtenerVentas($idSede, null);
        $hoy = date('Y-m-d');

        $resumen = [
            'ventas_hoy' => 0,
            'cantidad_hoy' => 0,
            'total_recaudado' => 0,
            'total_facturado' => 0,
            'por_cobrar' => 0,
            'ventas_pendientes' => 0,
            'ganancia_estimada' => 0,
            'costo_total' => 0
        ];

        foreach ($ventas as $v) {
            if (($v['estado_venta'] ?? '') === 'anulada') {
                continue;
            }

            $total = (float)$v['total_venta'];
            $costo = (float)$v['costo_total_venta'];
            $pagado = (float)$v['total_pagado'];
            $saldo = max(0, $total - $pagado);
            $fecha = substr((string)$v['fecha_venta'], 0, 10);

            if ($fecha === $hoy) {
                $resumen['ventas_hoy'] += $total;
                $resumen['cantidad_hoy']++;
            }

            $resumen['total_facturado'] += $total;
            $resumen['total_recaudado'] += $pagado;
            $resumen['costo_total'] += $costo;
            $resumen['ganancia_estimada'] += ($total - $costo);

            if ($v['estado_venta'] === 'pendiente' && $saldo > 0) {
                $resumen['por_cobrar'] += $saldo;
                $resumen['ventas_pendientes']++;
            }
        }

        return $resumen;
    }

    public function obtenerVentaPorId(int $idVenta): ?array {
        $query = "SELECT
                    v.*,
                    COALESCE(pv.total_pagado, 0) AS total_pagado,
                    s.nombre_sede,
                    c.nombre_cliente,
                    u.nombre_usuario
                  FROM ventas v
                  LEFT JOIN (
                        SELECT id_venta, SUM(monto_pago) AS total_pagado
                        FROM pagos_venta
                        GROUP BY id_venta
                  ) pv ON pv.id_venta = v.id_venta
                  LEFT JOIN sedes s ON v.id_sede = s.id_sede
                  LEFT JOIN cliente c ON v.id_cliente = c.id_cliente
                  LEFT JOIN usuario u ON v.id_usuario = u.id_usuario
                  WHERE v.id_venta = :id_venta
                  LIMIT 1";

        $res = $this->select($query, [':id_venta' => $idVenta]);
        return !empty($res) ? $res[0] : null;
    }

    public function obtenerDetallesVenta(int $idVenta): array {
        // Reventa: nombre de la presentacion. Locion: esencia + formula.
        $query = "SELECT
                    dv.*,
                    COALESCE(pp.nombre_presentacion, f.nombre_formula) AS nombre_presentacion,
                    COALESCE(p.nombre_producto, pe.nombre_producto) AS nombre_producto,
                    COALESCE(cp.nombre_categoria, 'Lociones') AS nombre_categoria
                  FROM detalle_venta dv
                  LEFT JOIN productos_presentaciones pp ON dv.id_presentacion = pp.id_presentacion
                  LEFT JOIN productos p ON pp.id_producto = p.id_producto
                  LEFT JOIN categoria_producto cp ON p.id_categoria = cp.id_categoria
                  LEFT JOIN productos pe ON pe.id_producto = dv.id_producto_esencia
                  LEFT JOIN formulas f ON f.id_formula = dv.id_formula
                  WHERE dv.id_venta = :id_venta
                  ORDER BY dv.id_detalle_venta ASC";

        return $this->select($query, [':id_venta' => $idVenta]);
    }

    public function obtenerPagosVenta(int $idVenta): array {
        $query = "SELECT
                    pv.id_pago_venta,
                    pv.id_venta,
                    pv.id_metodo_pago,
                    pv.monto_pago,
                    pv.fecha_pago,
                    mp.nombre_metodo_pago,
                    u.nombre_usuario
                  FROM pagos_venta pv
                  LEFT JOIN metodos_pago mp ON pv.id_metodo_pago = mp.id_metodo_pago
                  LEFT JOIN usuario u ON pv.id_usuario = u.id_usuario
                  WHERE pv.id_venta = :id_venta
                  ORDER BY pv.id_pago_venta ASC";

        return $this->select($query, [':id_venta' => $idVenta]);
    }

    public function generarSiguienteNumeroVenta(int $idSede): string {
        $res = $this->select(
            "SELECT COUNT(*) AS total FROM ventas WHERE id_sede = :id_sede",
            [':id_sede' => $idSede]
        );
        $consecutivo = ((int)$res[0]['total']) + 1;
        return sprintf('VTA-%04d', $consecutivo);
    }

    public function obtenerClientes(): array {
        return $this->select(
            "SELECT id_cliente, nombre_cliente, identificacion_cliente, telefono_cliente, direccion_cliente
             FROM cliente
             ORDER BY nombre_cliente ASC"
        );
    }

    public function registrarCliente(array $data): int {
        $query = "INSERT INTO cliente (nombre_cliente, identificacion_cliente, telefono_cliente, direccion_cliente)
                  VALUES (:nombre, :identificacion, :telefono, :direccion)";
        return (int)$this->execute($query, [
            ':nombre' => $data['nombreCliente'],
            ':identificacion' => $data['identificacionCliente'] ?: null,
            ':telefono' => $data['telefonoCliente'] ?: null,
            ':direccion' => $data['direccionCliente'] ?: null
        ]);
    }

    public function obtenerMetodosPago(): array {
        return $this->select(
            "SELECT id_metodo_pago, nombre_metodo_pago FROM metodos_pago WHERE id_estado = 1 ORDER BY id_metodo_pago ASC"
        );
    }

    // ============================================================
    // CATALOGO DE VENTA (disponibilidad y costo por sede)
    // ============================================================

    /**
     * Catalogo de venta de la sede. Cada item tiene una clave:
     *  - P{idPresentacion}: reventa (id_tipo_producto = 1), vendida como presentacion.
     *  - L{idEsencia}-{idFormula}: locion calculada (esencia + formula). Su stock es lo que alcanza
     *    con la esencia en lotes y el envase en inventario (mismo calculo del informe de produccion).
     */
    public function obtenerProductosParaVenta(int $idSede): array {
        $productos = [];

        $reventas = $this->select(
            "SELECT p.id_producto, p.nombre_producto, p.id_categoria, cp.nombre_categoria,
                    pp.id_presentacion, pp.nombre_presentacion,
                    COALESCE(pp.precio_compra_presentacion, 0) AS precio_compra_presentacion,
                    COALESCE(pp.precio_venta_presentacion, 0) AS precio_venta_base,
                    COALESCE(d.porcentaje_descuento, 0) AS porcentaje_descuento,
                    COALESCE(iss.stock_sede, 0) AS stock_unidades
             FROM productos_presentaciones pp
             INNER JOIN productos p ON pp.id_producto = p.id_producto
             INNER JOIN categoria_producto cp ON p.id_categoria = cp.id_categoria
             LEFT JOIN inventario_sedes iss ON iss.id_presentacion = pp.id_presentacion AND iss.id_sede = :id_sede_stock
             LEFT JOIN descuentos d
                ON pp.id_descuento = d.id_descuento AND d.id_estado = 1
                AND CURDATE() BETWEEN d.fecha_inicio_descuento AND d.fecha_fin_descuento
             WHERE p.id_estado = 1 AND pp.id_estado = 1 AND pp.id_tipo_producto = :tipo_reventa
               AND cp.nombre_categoria NOT IN ('Insumos', 'Esencias')
             ORDER BY cp.nombre_categoria ASC, p.nombre_producto ASC, pp.nombre_presentacion ASC",
            [':id_sede_stock' => $idSede, ':tipo_reventa' => self::TIPO_REVENTA]
        );

        foreach ($reventas as $r) {
            $base = (float)$r['precio_venta_base'];
            $desc = (float)$r['porcentaje_descuento'];
            $productos[] = [
                'clave' => 'P' . $r['id_presentacion'],
                'id_presentacion' => (int)$r['id_presentacion'],
                'id_producto' => (int)$r['id_producto'],
                'id_categoria' => (int)$r['id_categoria'],
                'nombre_categoria' => $r['nombre_categoria'],
                'nombre_producto' => $r['nombre_producto'],
                'nombre_presentacion' => $r['nombre_presentacion'],
                'precio_venta_base' => $base,
                'porcentaje_descuento' => $desc,
                'precio_venta_final' => $desc > 0 ? round($base * (1 - $desc / 100), 2) : $base,
                'stock_disponible' => (int)$r['stock_unidades'],
                'costo_unitario_actual' => (float)$r['precio_compra_presentacion'],
                'unidad_venta' => 'und',
                'venta_bloqueada' => false,
                'motivo_bloqueo' => null
            ];
        }

        $lociones = $this->select(
            "SELECT e.id_producto AS id_producto_esencia, e.nombre_producto AS nombre_esencia, e.id_categoria,
                    f.id_formula, f.nombre_formula, f.cantidad_esencia, f.id_presentacion_envase,
                    COALESCE(pl.precio_venta, pf.precio_venta) AS precio_venta
             FROM productos e
             INNER JOIN categoria_producto ce ON ce.id_categoria = e.id_categoria AND ce.nombre_categoria = 'Esencias'
             CROSS JOIN formulas f
             LEFT JOIN precios_locion_sede pl
                ON pl.id_producto_esencia = e.id_producto AND pl.id_formula = f.id_formula AND pl.id_sede = :id_sede
             LEFT JOIN precios_formula_sede pf
                ON pf.id_formula = f.id_formula AND pf.id_sede = :id_sede_formula
             WHERE e.id_estado = 1 AND f.id_estado = 1 AND f.id_presentacion_envase IS NOT NULL
             ORDER BY e.nombre_producto ASC, f.nombre_formula ASC",
            [':id_sede' => $idSede, ':id_sede_formula' => $idSede]
        );

        foreach ($lociones as $l) {
            $cantidadEsencia = (float)$l['cantidad_esencia'];
            $gramos = $this->obtenerStockGramosProducto((int)$l['id_producto_esencia'], $idSede);
            $idEnvase = (int)$l['id_presentacion_envase'];
            $envase = $this->obtenerPresentacionVenta($idEnvase);
            $stockEnvase = $this->obtenerStockUnidades($idEnvase, $idSede);

            $precio = $l['precio_venta'] !== null ? (float)$l['precio_venta'] : 0.0;
            $costo = round($cantidadEsencia * $gramos['costo_promedio'] + (float)($envase['precio_compra_presentacion'] ?? 0), 2);
            $stockLocion = (int)min(floor($gramos['gramos'] / $cantidadEsencia), $stockEnvase);

            // Motivo concreto para mostrar en ventas cuando no hay stock
            $motivoSinStock = null;
            if ($stockLocion <= 0) {
                $sinFrasco = $stockEnvase <= 0;
                $sinEsencia = $gramos['gramos'] < $cantidadEsencia;
                $nombreFrasco = $envase['nombre_presentacion'] ?? 'del envase';
                $nombreEsencia = $l['nombre_esencia'];

                if ($sinFrasco && $sinEsencia) {
                    $motivoSinStock = "Sin frasco de {$nombreFrasco} y sin esencia {$nombreEsencia} en esta sede";
                } elseif ($sinFrasco) {
                    $motivoSinStock = "Sin frasco de {$nombreFrasco} en esta sede";
                } elseif ($gramos['gramos'] <= 0) {
                    $motivoSinStock = "Sin esencia {$nombreEsencia} en esta sede";
                } else {
                    $motivoSinStock = "Esencia {$nombreEsencia} insuficiente: quedan " . floor($gramos['gramos'])
                        . " g y cada loción usa {$cantidadEsencia} g";
                }
            }

            $productos[] = [
                'clave' => 'L' . $l['id_producto_esencia'] . '-' . $l['id_formula'],
                'id_presentacion' => null,
                'id_producto' => (int)$l['id_producto_esencia'],
                'id_categoria' => self::ID_CATEGORIA_LOCIONES,
                'nombre_categoria' => 'Lociones',
                'nombre_producto' => $l['nombre_esencia'],
                'nombre_presentacion' => $l['nombre_formula'],
                'precio_venta_base' => $precio,
                'porcentaje_descuento' => 0,
                'precio_venta_final' => $precio,
                'stock_disponible' => $stockLocion,
                'motivo_sin_stock' => $motivoSinStock,
                'costo_unitario_actual' => $costo,
                'unidad_venta' => 'und',
                'venta_bloqueada' => $l['precio_venta'] === null,
                'motivo_bloqueo' => $l['precio_venta'] === null ? 'Sin precio para esta sede' : null
            ];
        }

        return $productos;
    }

    /** Descompone la clave de un item de venta. */
    private function parsearClave(string $clave): array {
        if (preg_match('/^P(\d+)$/', $clave, $m)) {
            return ['tipo' => 'reventa', 'id_presentacion' => (int)$m[1]];
        }
        if (preg_match('/^L(\d+)-(\d+)$/', $clave, $m)) {
            return ['tipo' => 'locion', 'id_producto_esencia' => (int)$m[1], 'id_formula' => (int)$m[2]];
        }
        throw new \DomainException('Producto de venta inválido');
    }

    /** Precios de lociones (esencia + formula) de la sede. */
    public function obtenerPreciosLocion(int $idSede): array {
        return $this->select(
            "SELECT id_producto_esencia, id_formula, precio_venta FROM precios_locion_sede WHERE id_sede = :id_sede",
            [':id_sede' => $idSede]
        );
    }

    /** Precio por formula de la sede (aplica a las esencias sin precio propio). */
    public function obtenerPreciosFormula(int $idSede): array {
        return $this->select(
            "SELECT id_formula, precio_venta FROM precios_formula_sede WHERE id_sede = :id_sede",
            [':id_sede' => $idSede]
        );
    }

    public function guardarPrecioFormula(int $idSede, int $idFormula, float $precio): void {
        $this->execute(
            "INSERT INTO precios_formula_sede (id_formula, id_sede, precio_venta)
             VALUES (:formula, :sede, :precio)
             ON DUPLICATE KEY UPDATE precio_venta = VALUES(precio_venta)",
            [':formula' => $idFormula, ':sede' => $idSede, ':precio' => $precio]
        );
    }

    /** Esencias activas con su precio propio para la formula en la sede (precio_propio null = sin precio propio). */
    public function obtenerEsenciasPrecioFormula(int $idSede, int $idFormula): array {
        return $this->select(
            "SELECT e.id_producto, e.nombre_producto, pl.precio_venta AS precio_propio
             FROM productos e
             INNER JOIN categoria_producto ce ON ce.id_categoria = e.id_categoria AND ce.nombre_categoria = 'Esencias'
             LEFT JOIN precios_locion_sede pl
                ON pl.id_producto_esencia = e.id_producto AND pl.id_formula = :formula AND pl.id_sede = :sede
             WHERE e.id_estado = 1
             ORDER BY e.nombre_producto ASC",
            [':formula' => $idFormula, ':sede' => $idSede]
        );
    }

    /** Pone el precio de la formula en todas las esencias activas de la sede (reemplaza los precios propios). */
    public function aplicarPrecioFormulaATodas(int $idSede, int $idFormula, float $precio): void {
        $this->execute(
            "INSERT INTO precios_locion_sede (id_producto_esencia, id_formula, id_sede, precio_venta)
             SELECT e.id_producto, :formula, :sede, :precio
             FROM productos e
             INNER JOIN categoria_producto ce ON ce.id_categoria = e.id_categoria AND ce.nombre_categoria = 'Esencias'
             WHERE e.id_estado = 1
             ON DUPLICATE KEY UPDATE precio_venta = VALUES(precio_venta)",
            [':formula' => $idFormula, ':sede' => $idSede, ':precio' => $precio]
        );
    }

    public function guardarPrecioLocion(int $idSede, int $idProductoEsencia, int $idFormula, float $precio): void {
        $this->execute(
            "INSERT INTO precios_locion_sede (id_producto_esencia, id_formula, id_sede, precio_venta)
             VALUES (:esencia, :formula, :sede, :precio)
             ON DUPLICATE KEY UPDATE precio_venta = VALUES(precio_venta)",
            [':esencia' => $idProductoEsencia, ':formula' => $idFormula, ':sede' => $idSede, ':precio' => $precio]
        );
    }

    private function obtenerStockGramosProducto(int $idProducto, int $idSede): array {
        $res = $this->select(
            "SELECT
                COALESCE(SUM(l.gramos_disponibles), 0) AS gramos,
                CASE WHEN SUM(l.gramos_disponibles) > 0
                     THEN SUM(l.gramos_disponibles * l.costo_por_gramo) / SUM(l.gramos_disponibles)
                     ELSE 0
                END AS costo_promedio
             FROM lotes_esencia l
             INNER JOIN productos_presentaciones pp ON pp.id_presentacion = l.id_presentacion
             WHERE pp.id_producto = :id_producto AND l.id_sede = :id_sede",
            [':id_producto' => $idProducto, ':id_sede' => $idSede]
        );
        return [
            'gramos' => (float)$res[0]['gramos'],
            'costo_promedio' => round((float)$res[0]['costo_promedio'], 2)
        ];
    }

    private function obtenerStockUnidades(int $idPresentacion, int $idSede): int {
        $res = $this->select(
            "SELECT stock_sede FROM inventario_sedes WHERE id_presentacion = :id_pres AND id_sede = :id_sede",
            [':id_pres' => $idPresentacion, ':id_sede' => $idSede]
        );
        return (int)($res[0]['stock_sede'] ?? 0);
    }

    /** Stock de unidades leido con bloqueo: base para descontar o devolver dentro de la transaccion. */
    private function obtenerStockUnidadesBloqueado(int $idPresentacion, int $idSede): int {
        $res = $this->select(
            "SELECT stock_sede FROM inventario_sedes WHERE id_presentacion = :id_pres AND id_sede = :id_sede FOR UPDATE",
            [':id_pres' => $idPresentacion, ':id_sede' => $idSede]
        );
        return (int)($res[0]['stock_sede'] ?? 0);
    }

    private function obtenerPresentacionVenta(int $idPresentacion): ?array {
        $res = $this->select(
            "SELECT pp.id_presentacion, pp.id_producto, pp.nombre_presentacion, pp.id_tipo_producto,
                    pp.unidad_medida_productos_presentacion, p.nombre_producto,
                    COALESCE(pp.precio_compra_presentacion, 0) AS precio_compra_presentacion
             FROM productos_presentaciones pp
             INNER JOIN productos p ON p.id_producto = pp.id_producto
             WHERE pp.id_presentacion = :id_pres",
            [':id_pres' => $idPresentacion]
        );
        return $res[0] ?? null;
    }

    // ============================================================
    // PLANIFICACION Y BLOQUEO DE INVENTARIO
    // ============================================================

    /**
     * Consumos de una venta de $cantidad unidades del item (clave).
     * Reventa: unidades de la presentacion. Locion: gramos de la esencia (formula.cantidad_esencia) y 1 envase de la formula.
     */
    private function planificarConsumo(string $clave, int $cantidad): array {
        $item = $this->parsearClave($clave);

        if ($item['tipo'] === 'reventa') {
            if (!$this->obtenerPresentacionVenta($item['id_presentacion'])) {
                throw new \DomainException('El producto seleccionado no existe.');
            }
            return [['tipo' => 'unidad', 'id_presentacion' => $item['id_presentacion'], 'cantidad' => $cantidad]];
        }

        $formula = $this->select(
            "SELECT cantidad_esencia, id_presentacion_envase FROM formulas WHERE id_formula = :f AND id_estado = 1",
            [':f' => $item['id_formula']]
        )[0] ?? null;
        if (!$formula || empty($formula['id_presentacion_envase'])) {
            throw new \DomainException('La fórmula no existe o no tiene envase asignado.');
        }

        return [
            ['tipo' => 'gramo', 'id_producto' => $item['id_producto_esencia'], 'cantidad' => (float)$formula['cantidad_esencia'] * $cantidad],
            ['tipo' => 'unidad', 'id_presentacion' => (int)$formula['id_presentacion_envase'], 'cantidad' => $cantidad]
        ];
    }

    /**
     * Bloquea las filas de inventario que se van a tocar. Orden fijo: sedes, esencias, lotes.
     * Debe llamarse dentro de una transaccion.
     */
    private function bloquearInventario(int $idSede, array $consumos): void {
        $unidades = [];
        $productos = [];
        foreach ($consumos as $c) {
            if ($c['tipo'] === 'unidad') {
                $unidades[$c['id_presentacion']] = true;
            } else {
                $productos[$c['id_producto']] = true;
            }
        }

        if ($unidades) {
            $params = [':id_sede' => $idSede];
            $in = $this->crearParametrosIn(array_keys($unidades), 'u', $params);
            $this->select(
                "SELECT id_inventario_sede FROM inventario_sedes
                 WHERE id_sede = :id_sede AND id_presentacion IN ({$in})
                 ORDER BY id_presentacion FOR UPDATE",
                $params
            );
        }

        if ($productos) {
            $params = [':id_sede' => $idSede];
            $in = $this->crearParametrosIn(array_keys($productos), 'p', $params);
            $this->select(
                "SELECT ie.id_inventario_esencia
                 FROM inventario_esencias ie
                 INNER JOIN productos_presentaciones pp ON pp.id_presentacion = ie.id_presentacion
                 WHERE ie.id_sede = :id_sede AND pp.id_producto IN ({$in})
                 ORDER BY ie.id_presentacion FOR UPDATE",
                $params
            );

            $params = [':id_sede' => $idSede];
            $in = $this->crearParametrosIn(array_keys($productos), 'q', $params);
            $this->select(
                "SELECT l.id_lote
                 FROM lotes_esencia l
                 INNER JOIN productos_presentaciones pp ON pp.id_presentacion = l.id_presentacion
                 WHERE l.id_sede = :id_sede AND pp.id_producto IN ({$in})
                 ORDER BY l.id_lote FOR UPDATE",
                $params
            );
        }
    }

    private function crearParametrosIn(array $valores, string $prefijo, array &$params): string {
        $marcadores = [];
        foreach (array_values($valores) as $i => $valor) {
            $clave = ":{$prefijo}{$i}";
            $marcadores[] = $clave;
            $params[$clave] = $valor;
        }
        return implode(', ', $marcadores);
    }

    // ============================================================
    // DESCUENTO DE INVENTARIO (requiere filas ya bloqueadas)
    // ============================================================

    /** Ejecuta los consumos de un item de venta y devuelve su costo real. */
    private function descontarConsumos(int $idSede, int $idVenta, int $idUsuario, int $idTipoMov, array $consumos, string $obs): float {
        $costo = 0.0;
        foreach ($consumos as $c) {
            $costo += $c['tipo'] === 'gramo'
                ? $this->descontarGramosFIFO($idSede, $c['id_producto'], $c['cantidad'], $idVenta, $idUsuario, $idTipoMov, $obs)
                : $this->descontarUnidades($idSede, $c['id_presentacion'], $c['cantidad'], $idVenta, $idUsuario, $idTipoMov, $obs);
        }
        return round($costo, 2);
    }

    /** Consume gramos de los lotes del producto, del mas antiguo al mas nuevo. Devuelve el costo de los lotes consumidos. */
    private function descontarGramosFIFO(int $idSede, int $idProducto, float $gramos, int $idVenta, int $idUsuario, int $idTipoMov, string $obs): float {
        $lotes = $this->select(
            "SELECT l.id_lote, l.id_presentacion, l.gramos_disponibles, l.costo_por_gramo
             FROM lotes_esencia l
             INNER JOIN productos_presentaciones pp ON pp.id_presentacion = l.id_presentacion
             WHERE pp.id_producto = :id_producto AND l.id_sede = :id_sede AND l.gramos_disponibles > 0
             ORDER BY l.fecha_ingreso ASC, l.id_lote ASC
             FOR UPDATE",
            [':id_producto' => $idProducto, ':id_sede' => $idSede]
        );

        $pendiente = $gramos;
        $costo = 0.0;

        foreach ($lotes as $lote) {
            if ($pendiente <= 0.001) {
                break;
            }

            $tomar = min((float)$lote['gramos_disponibles'], $pendiente);
            $idPresLote = (int)$lote['id_presentacion'];

            $this->execute(
                "UPDATE lotes_esencia SET gramos_disponibles = gramos_disponibles - :tomar WHERE id_lote = :id_lote",
                [':tomar' => $tomar, ':id_lote' => (int)$lote['id_lote']]
            );
            $this->execute(
                "UPDATE inventario_esencias SET cantidad_gramos = cantidad_gramos - :tomar, fecha_actualizacion = NOW()
                 WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
                [':tomar' => $tomar, ':id_sede' => $idSede, ':id_pres' => $idPresLote]
            );

            $saldo = $this->obtenerSaldoGramos($idSede, $idPresLote);
            $this->insertarKardex($idSede, $idPresLote, $idTipoMov, 'gramo', -$tomar, $saldo, $idVenta, $idUsuario, $obs, (int)$lote['id_lote']);

            $costo += $tomar * (float)$lote['costo_por_gramo'];
            $pendiente -= $tomar;
        }

        if ($pendiente > 0.001) {
            throw new \DomainException("Stock de esencia insuficiente en la sede. Faltan {$pendiente} g.");
        }

        return $costo;
    }

    private function descontarUnidades(int $idSede, int $idPresentacion, int $cantidad, int $idVenta, int $idUsuario, int $idTipoMov, string $obs): float {
        $actual = $this->obtenerStockUnidadesBloqueado($idPresentacion, $idSede);
        if ($actual < $cantidad) {
            $pres = $this->obtenerPresentacionVenta($idPresentacion);
            $nombre = $pres ? "{$pres['nombre_producto']} - {$pres['nombre_presentacion']}" : "#{$idPresentacion}";
            throw new \DomainException("Stock insuficiente para {$nombre}. Disponible: {$actual}.");
        }

        $nuevo = $actual - $cantidad;
        $this->execute(
            "UPDATE inventario_sedes SET stock_sede = :nuevo WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
            [':nuevo' => $nuevo, ':id_sede' => $idSede, ':id_pres' => $idPresentacion]
        );
        $this->insertarKardex($idSede, $idPresentacion, $idTipoMov, 'unidad', -$cantidad, $nuevo, $idVenta, $idUsuario, $obs);

        $pres = $this->obtenerPresentacionVenta($idPresentacion);
        return $cantidad * (float)($pres['precio_compra_presentacion'] ?? 0);
    }

    private function obtenerSaldoGramos(int $idSede, int $idPresentacion): float {
        $res = $this->select(
            "SELECT cantidad_gramos FROM inventario_esencias WHERE id_sede = :id_sede AND id_presentacion = :id_pres FOR UPDATE",
            [':id_sede' => $idSede, ':id_pres' => $idPresentacion]
        );
        return (float)($res[0]['cantidad_gramos'] ?? 0);
    }

    private function insertarKardex(
        int $idSede,
        int $idPresentacion,
        int $idTipoMov,
        string $unidad,
        float $cantidad,
        float $saldoResultante,
        ?int $idVenta,
        ?int $idUsuario,
        string $obs,
        ?int $idLote = null
    ): void {
        $this->execute(
            "INSERT INTO movimientos_inventario (
                id_sede, id_presentacion, id_tipo_movimiento, unidad_movimiento,
                cantidad_movimiento, saldo_resultante, id_venta, id_usuario,
                observacion_movimiento, id_lote_esencia
            ) VALUES (
                :id_sede, :id_pres, :id_tipo, :unidad,
                :cantidad, :saldo, :id_venta, :id_usuario,
                :obs, :id_lote
            )",
            [
                ':id_sede' => $idSede,
                ':id_pres' => $idPresentacion,
                ':id_tipo' => $idTipoMov,
                ':unidad' => $unidad,
                ':cantidad' => $cantidad,
                ':saldo' => $saldoResultante,
                ':id_venta' => $idVenta,
                ':id_usuario' => $idUsuario,
                ':obs' => $obs,
                ':id_lote' => $idLote
            ]
        );
    }

    private function obtenerIdTipoMovimiento(string $nombre): int {
        $res = $this->select(
            "SELECT id_tipo_movimiento FROM tipos_movimiento WHERE nombre_movimiento = :nombre LIMIT 1",
            [':nombre' => $nombre]
        );
        if (empty($res)) {
            throw new \RuntimeException("Falta el tipo de movimiento {$nombre} en tipos_movimiento");
        }
        return (int)$res[0]['id_tipo_movimiento'];
    }

    // ============================================================
    // VENTAS
    // ============================================================

    public function registrarVentaCompleta(array $encabezado, array $detalles, ?array $pagoInicial = null): array {
        $this->beginTransaction();

        try {
            $idSede = (int)$encabezado['idSede'];
            $idUsuario = (int)$encabezado['idUsuario'];
            $totalVenta = (float)$encabezado['totalVenta'];
            $montoPagado = $pagoInicial ? (float)$pagoInicial['montoPago'] : 0.0;
            $estadoVenta = ($montoPagado >= $totalVenta && $totalVenta > 0) ? 'pagada' : 'pendiente';

            // 1. Planificar consumos y bloquear el inventario antes de validar nada
            $planes = [];
            $todosLosConsumos = [];
            foreach ($detalles as $item) {
                $plan = $this->planificarConsumo((string)$item['clave'], (int)$item['cantidad']);
                $planes[] = $plan;
                $todosLosConsumos = array_merge($todosLosConsumos, $plan);
            }
            $this->bloquearInventario($idSede, $todosLosConsumos);

            // 2. Validar contra el catalogo de la sede (ya con las filas bloqueadas)
            $catalogo = [];
            foreach ($this->obtenerProductosParaVenta($idSede) as $p) {
                $catalogo[$p['clave']] = $p;
            }
            foreach ($detalles as $item) {
                $clave = (string)$item['clave'];
                $cantidad = (int)$item['cantidad'];
                if (!isset($catalogo[$clave])) {
                    throw new \DomainException("El producto seleccionado no está activo o no existe.");
                }
                $prod = $catalogo[$clave];
                if ($prod['venta_bloqueada']) {
                    throw new \DomainException("{$prod['nombre_producto']} - {$prod['nombre_presentacion']}: {$prod['motivo_bloqueo']}.");
                }
                if ($cantidad > (float)$prod['stock_disponible']) {
                    throw new \DomainException("Stock insuficiente para {$prod['nombre_producto']} - {$prod['nombre_presentacion']}. Disponible: {$prod['stock_disponible']}.");
                }
            }

            // 3. Encabezado (el costo total se completa al final)
            $idVenta = (int)$this->execute(
                "INSERT INTO ventas (
                    id_sede, numero_venta, id_cliente, id_usuario,
                    subtotal_venta, descuento_venta, total_venta, costo_total_venta,
                    estado_venta, observacion_venta
                ) VALUES (
                    :id_sede, :numero_venta, :id_cliente, :id_usuario,
                    :subtotal_venta, :descuento_venta, :total_venta, 0,
                    :estado_venta, :observacion_venta
                )",
                [
                    ':id_sede' => $idSede,
                    ':numero_venta' => $encabezado['numeroVenta'],
                    ':id_cliente' => !empty($encabezado['idCliente']) ? (int)$encabezado['idCliente'] : null,
                    ':id_usuario' => $idUsuario,
                    ':subtotal_venta' => (float)$encabezado['subtotalVenta'],
                    ':descuento_venta' => (float)$encabezado['descuentoVenta'],
                    ':total_venta' => $totalVenta,
                    ':estado_venta' => $estadoVenta,
                    ':observacion_venta' => $encabezado['observacionVenta'] ?: null
                ]
            );

            // 4. Detalles, descuento de inventario y costo real de cada linea
            $idTipoVenta = $this->obtenerIdTipoMovimiento('VENTA');
            $costoTotalVenta = 0.0;

            foreach ($detalles as $i => $item) {
                $linea = $this->parsearClave((string)$item['clave']);
                $cantidad = (int)$item['cantidad'];
                $precioUnitario = (float)$item['precioUnitario'];
                $subtotal = round($precioUnitario * $cantidad, 2);

                $idDetalle = (int)$this->execute(
                    "INSERT INTO detalle_venta (
                        id_venta, id_presentacion, id_producto_esencia, id_formula,
                        cantidad_detalle_venta, precio_unitario_detalle_venta, subtotal_detalle_venta,
                        costo_unitario_detalle_venta, costo_total_detalle_venta
                    ) VALUES (:id_venta, :id_pres, :id_esencia, :id_formula, :cantidad, :precio, :subtotal, 0, 0)",
                    [
                        ':id_venta' => $idVenta,
                        ':id_pres' => $linea['id_presentacion'] ?? null,
                        ':id_esencia' => $linea['id_producto_esencia'] ?? null,
                        ':id_formula' => $linea['id_formula'] ?? null,
                        ':cantidad' => $cantidad,
                        ':precio' => $precioUnitario,
                        ':subtotal' => $subtotal
                    ]
                );

                $costoItem = $this->descontarConsumos(
                    $idSede, $idVenta, $idUsuario, $idTipoVenta, $planes[$i],
                    "Venta {$encabezado['numeroVenta']}"
                );

                $this->execute(
                    "UPDATE detalle_venta
                     SET costo_unitario_detalle_venta = :cu, costo_total_detalle_venta = :ct
                     WHERE id_detalle_venta = :id",
                    [
                        ':cu' => round($costoItem / $cantidad, 2),
                        ':ct' => $costoItem,
                        ':id' => $idDetalle
                    ]
                );
                $costoTotalVenta += $costoItem;
            }

            $this->execute(
                "UPDATE ventas SET costo_total_venta = :costo WHERE id_venta = :id",
                [':costo' => round($costoTotalVenta, 2), ':id' => $idVenta]
            );

            // 5. Pago inicial (contado o primer abono)
            if ($pagoInicial && $montoPagado > 0) {
                $this->registrarPagoYCaja(
                    $idVenta,
                    $idSede,
                    (int)$pagoInicial['idMetodoPago'],
                    $montoPagado,
                    $idUsuario,
                    "Pago de venta {$encabezado['numeroVenta']}"
                );
            }

            $this->commit();
            return ['id_venta' => $idVenta, 'estado_venta' => $estadoVenta];

        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    private function registrarPagoYCaja(int $idVenta, int $idSede, int $idMetodoPago, float $monto, int $idUsuario, string $obs): void {
        $idPagoVenta = (int)$this->execute(
            "INSERT INTO pagos_venta (id_venta, id_metodo_pago, monto_pago, id_usuario)
             VALUES (:id_venta, :id_metodo, :monto, :id_usuario)",
            [
                ':id_venta' => $idVenta,
                ':id_metodo' => $idMetodoPago,
                ':monto' => $monto,
                ':id_usuario' => $idUsuario
            ]
        );

        $this->execute(
            "INSERT INTO caja_movimientos (
                id_sede, tipo_caja, concepto_caja, monto_caja,
                id_metodo_pago, id_pago_venta, id_venta, id_usuario, observacion_caja
            ) VALUES (
                :id_sede, 'ingreso', 'pago_venta', :monto,
                :id_metodo, :id_pago_venta, :id_venta, :id_usuario, :obs
            )",
            [
                ':id_sede' => $idSede,
                ':monto' => $monto,
                ':id_metodo' => $idMetodoPago,
                ':id_pago_venta' => $idPagoVenta,
                ':id_venta' => $idVenta,
                ':id_usuario' => $idUsuario,
                ':obs' => $obs
            ]
        );
    }

    public function registrarAbonoVenta(int $idVenta, int $idMetodoPago, float $montoPago, int $idUsuario): array {
        $this->beginTransaction();

        try {
            // Bloquea la venta para que dos abonos simultaneos no superen el saldo
            $this->select("SELECT id_venta FROM ventas WHERE id_venta = :id FOR UPDATE", [':id' => $idVenta]);

            $venta = $this->obtenerVentaPorId($idVenta);
            if (!$venta) {
                throw new \DomainException("La venta no existe.");
            }
            if ($venta['estado_venta'] === 'anulada') {
                throw new \DomainException("No se puede abonar a una venta anulada.");
            }
            if ($venta['estado_venta'] === 'pagada') {
                throw new \DomainException("Esta venta ya se encuentra pagada en su totalidad.");
            }

            $total = (float)$venta['total_venta'];
            $pagadoActual = (float)$venta['total_pagado'];
            $saldoPendiente = max(0, $total - $pagadoActual);

            if ($montoPago > $saldoPendiente + 0.01) {
                throw new \DomainException("El abono supera el saldo pendiente de la venta ($ " . number_format($saldoPendiente, 0, ',', '.') . ").");
            }

            $this->registrarPagoYCaja(
                $idVenta,
                (int)$venta['id_sede'],
                $idMetodoPago,
                $montoPago,
                $idUsuario,
                "Abono a venta {$venta['numero_venta']}"
            );

            $nuevoPagado = $pagadoActual + $montoPago;
            $nuevoEstado = $nuevoPagado >= $total - 0.01 ? 'pagada' : 'pendiente';

            if ($nuevoEstado === 'pagada') {
                $this->execute("UPDATE ventas SET estado_venta = 'pagada' WHERE id_venta = :id", [':id' => $idVenta]);
            }

            $this->commit();
            return ['id_venta' => $idVenta, 'estado_venta' => $nuevoEstado];

        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    public function anularVenta(int $idVenta, int $idUsuario): bool {
        $this->beginTransaction();

        try {
            // Bloquea la venta para que no se anule dos veces
            $this->select("SELECT id_venta FROM ventas WHERE id_venta = :id FOR UPDATE", [':id' => $idVenta]);

            $venta = $this->obtenerVentaPorId($idVenta);
            if (!$venta) {
                throw new \DomainException("La venta no existe.");
            }
            if ($venta['estado_venta'] === 'anulada') {
                throw new \DomainException("La venta ya se encuentra anulada.");
            }

            $idSede = (int)$venta['id_sede'];
            $idTipoVenta = $this->obtenerIdTipoMovimiento('VENTA');
            $idTipoAnulacion = $this->obtenerIdTipoMovimiento('ANULACION_VENTA');
            $obs = "Anulación venta {$venta['numero_venta']}";

            // Movimientos originales de la venta: se revierten exactamente
            $movimientos = $this->select(
                "SELECT m.id_presentacion, m.unidad_movimiento, m.cantidad_movimiento, m.id_lote_esencia, pp.id_producto
                 FROM movimientos_inventario m
                 INNER JOIN productos_presentaciones pp ON pp.id_presentacion = m.id_presentacion
                 WHERE m.id_venta = :id AND m.id_tipo_movimiento = :tipo
                 ORDER BY m.id_movimientos_inventario ASC",
                [':id' => $idVenta, ':tipo' => $idTipoVenta]
            );

            $consumos = [];
            foreach ($movimientos as $m) {
                $consumos[] = $m['unidad_movimiento'] === 'gramo'
                    ? ['tipo' => 'gramo', 'id_producto' => (int)$m['id_producto'], 'cantidad' => 0]
                    : ['tipo' => 'unidad', 'id_presentacion' => (int)$m['id_presentacion'], 'cantidad' => 0];
            }
            $this->bloquearInventario($idSede, $consumos);

            foreach ($movimientos as $m) {
                $cantidad = abs((float)$m['cantidad_movimiento']);
                $idPres = (int)$m['id_presentacion'];

                if ($m['unidad_movimiento'] === 'gramo') {
                    if (!empty($m['id_lote_esencia'])) {
                        $this->execute(
                            "UPDATE lotes_esencia SET gramos_disponibles = gramos_disponibles + :c WHERE id_lote = :id",
                            [':c' => $cantidad, ':id' => (int)$m['id_lote_esencia']]
                        );
                    }
                    $this->execute(
                        "UPDATE inventario_esencias SET cantidad_gramos = cantidad_gramos + :c, fecha_actualizacion = NOW()
                         WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
                        [':c' => $cantidad, ':id_sede' => $idSede, ':id_pres' => $idPres]
                    );
                    $saldo = $this->obtenerSaldoGramos($idSede, $idPres);
                    $this->insertarKardex($idSede, $idPres, $idTipoAnulacion, 'gramo', $cantidad, $saldo, $idVenta, $idUsuario, $obs, $m['id_lote_esencia'] ? (int)$m['id_lote_esencia'] : null);
                } else {
                    $nuevo = $this->obtenerStockUnidadesBloqueado($idPres, $idSede) + (int)$cantidad;
                    $this->execute(
                        "UPDATE inventario_sedes SET stock_sede = :nuevo WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
                        [':nuevo' => $nuevo, ':id_sede' => $idSede, ':id_pres' => $idPres]
                    );
                    $this->insertarKardex($idSede, $idPres, $idTipoAnulacion, 'unidad', $cantidad, $nuevo, $idVenta, $idUsuario, $obs);
                }
            }

            // Reversa de caja por lo que se haya cobrado
            $totalPagado = (float)$venta['total_pagado'];
            if ($totalPagado > 0) {
                $this->execute(
                    "INSERT INTO caja_movimientos (id_sede, tipo_caja, concepto_caja, monto_caja, id_venta, id_usuario, observacion_caja)
                     VALUES (:id_sede, 'egreso', 'anulacion_venta', :monto, :id_venta, :id_usuario, :obs)",
                    [
                        ':id_sede' => $idSede,
                        ':monto' => $totalPagado,
                        ':id_venta' => $idVenta,
                        ':id_usuario' => $idUsuario,
                        ':obs' => "Reversión por anulación de venta {$venta['numero_venta']}"
                    ]
                );
            }

            $this->execute("UPDATE ventas SET estado_venta = 'anulada' WHERE id_venta = :id", [':id' => $idVenta]);

            $this->commit();
            return true;

        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $e;
        }
    }
}
