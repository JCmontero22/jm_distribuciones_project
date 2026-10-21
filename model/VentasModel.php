<?php

require_once(__DIR__ . '/../core/conexion.php');

class VentasModel extends conexion {

    private array $tableCache = [];
    private array $columnCache = [];

    private function tablaExiste(string $tabla): bool {
        if (!isset($this->tableCache[$tabla])) {
            try {
                $res = $this->select("SHOW TABLES LIKE :tabla", [':tabla' => $tabla]);
                $this->tableCache[$tabla] = !empty($res);
            } catch (\Exception $e) {
                $this->tableCache[$tabla] = false;
            }
        }
        return $this->tableCache[$tabla];
    }

    private function columnaExiste(string $tabla, string $columna): bool {
        $key = "{$tabla}.{$columna}";
        if (!isset($this->columnCache[$key])) {
            try {
                $res = $this->select("SHOW COLUMNS FROM `{$tabla}` LIKE :col", [':col' => $columna]);
                $this->columnCache[$key] = !empty($res);
            } catch (\Exception $e) {
                $this->columnCache[$key] = false;
            }
        }
        return $this->columnCache[$key];
    }

    public function obtenerVentas(?int $idSede = null, ?string $estado = null): array {
        if (!$this->tablaExiste('ventas')) {
            return [];
        }

        $tienePagosVenta = $this->tablaExiste('pagos_venta');
        $joinPagos = $tienePagosVenta
            ? "LEFT JOIN (
                    SELECT id_venta, SUM(monto_pago) AS total_pagado
                    FROM pagos_venta
                    GROUP BY id_venta
               ) pv ON pv.id_venta = v.id_venta"
            : "";

        $selectPagado = $tienePagosVenta
            ? "COALESCE(pv.total_pagado, 0) AS total_pagado,
               GREATEST(0, v.total_venta - COALESCE(pv.total_pagado, 0)) AS saldo_pendiente,"
            : "CASE WHEN v.estado_venta = 'pagada' THEN v.total_venta ELSE 0 END AS total_pagado,
               CASE WHEN v.estado_venta = 'pendiente' THEN v.total_venta ELSE 0 END AS saldo_pendiente,";

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

        $whereSql = implode(' AND ', $where);

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
                    {$selectPagado}
                    s.nombre_sede,
                    c.nombre_cliente,
                    u.nombre_usuario
                  FROM ventas v
                  LEFT JOIN sedes s ON v.id_sede = s.id_sede
                  LEFT JOIN cliente c ON v.id_cliente = c.id_cliente
                  LEFT JOIN usuario u ON v.id_usuario = u.id_usuario
                  {$joinPagos}
                  WHERE {$whereSql}
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

            $total = (float)($v['total_venta'] ?? 0);
            $costo = (float)($v['costo_total_venta'] ?? 0);
            $pagado = (float)($v['total_pagado'] ?? 0);
            $saldo = max(0, $total - $pagado);
            $fecha = substr((string)($v['fecha_venta'] ?? ''), 0, 10);

            if ($fecha === $hoy) {
                $resumen['ventas_hoy'] += $total;
                $resumen['cantidad_hoy']++;
            }

            $resumen['total_facturado'] += $total;
            $resumen['total_recaudado'] += $pagado;
            $resumen['costo_total'] += $costo;
            $resumen['ganancia_estimada'] += ($total - $costo);

            if (($v['estado_venta'] ?? '') === 'pendiente' && $saldo > 0) {
                $resumen['por_cobrar'] += $saldo;
                $resumen['ventas_pendientes']++;
            }
        }

        return $resumen;
    }

    public function obtenerVentaPorId(int $idVenta): ?array {
        $tienePagosVenta = $this->tablaExiste('pagos_venta');
        $joinPagos = $tienePagosVenta
            ? "LEFT JOIN (
                    SELECT id_venta, SUM(monto_pago) AS total_pagado
                    FROM pagos_venta
                    GROUP BY id_venta
               ) pv ON pv.id_venta = v.id_venta"
            : "";

        $selectPagado = $tienePagosVenta
            ? "COALESCE(pv.total_pagado, 0) AS total_pagado"
            : "CASE WHEN v.estado_venta = 'pagada' THEN v.total_venta ELSE 0 END AS total_pagado";

        $query = "SELECT
                    v.*,
                    {$selectPagado},
                    s.nombre_sede,
                    c.nombre_cliente,
                    u.nombre_usuario
                  FROM ventas v
                  LEFT JOIN sedes s ON v.id_sede = s.id_sede
                  LEFT JOIN cliente c ON v.id_cliente = c.id_cliente
                  LEFT JOIN usuario u ON v.id_usuario = u.id_usuario
                  {$joinPagos}
                  WHERE v.id_venta = :id_venta
                  LIMIT 1";

        $res = $this->select($query, [':id_venta' => $idVenta]);
        return !empty($res) ? $res[0] : null;
    }

    public function obtenerDetallesVenta(int $idVenta): array {
        $query = "SELECT
                    dv.*,
                    pp.nombre_presentacion,
                    p.nombre_producto,
                    cp.nombre_categoria
                  FROM detalle_venta dv
                  LEFT JOIN productos_presentaciones pp ON dv.id_presentacion = pp.id_presentacion
                  LEFT JOIN productos p ON pp.id_producto = p.id_producto
                  LEFT JOIN categoria_producto cp ON p.id_categoria = cp.id_categoria
                  WHERE dv.id_venta = :id_venta
                  ORDER BY dv.id_detalle_venta ASC";

        return $this->select($query, [':id_venta' => $idVenta]);
    }

    public function obtenerPagosVenta(int $idVenta): array {
        if (!$this->tablaExiste('pagos_venta')) {
            return [];
        }

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
        if (!$this->tablaExiste('ventas')) {
            return 'VTA-0001';
        }

        $res = $this->select(
            "SELECT COUNT(*) AS total FROM ventas WHERE id_sede = :id_sede",
            [':id_sede' => $idSede]
        );
        $consecutivo = ((int)($res[0]['total'] ?? 0)) + 1;
        return sprintf('VTA-%04d', $consecutivo);
    }

    public function obtenerClientes(): array {
        if (!$this->tablaExiste('cliente')) {
            return [];
        }
        $colDir = $this->columnaExiste('cliente', 'direccion_cliente') ? 'direccion_cliente' : 'doreccion_cliente';
        $query = "SELECT
                    id_cliente,
                    nombre_cliente,
                    identificacion_cliente,
                    telefono_cliente,
                    {$colDir} AS direccion_cliente
                  FROM cliente
                  ORDER BY nombre_cliente ASC";
        return $this->select($query);
    }

    public function registrarCliente(array $data): int {
        $colDir = $this->columnaExiste('cliente', 'direccion_cliente') ? 'direccion_cliente' : 'doreccion_cliente';
        $tieneEstado = $this->columnaExiste('cliente', 'id_estado');

        $campos = "nombre_cliente, identificacion_cliente, telefono_cliente, {$colDir}";
        $valores = ":nombre, :identificacion, :telefono, :direccion";
        if ($tieneEstado) {
            $campos .= ", id_estado";
            $valores .= ", 1";
        }

        $query = "INSERT INTO cliente ({$campos}) VALUES ({$valores})";
        return (int)$this->execute($query, [
            ':nombre' => $data['nombreCliente'],
            ':identificacion' => $data['identificacionCliente'] ?: null,
            ':telefono' => $data['telefonoCliente'] ?: null,
            ':direccion' => $data['direccionCliente'] ?: null
        ]);
    }

    public function obtenerMetodosPago(): array {
        if (!$this->tablaExiste('metodos_pago')) {
            return [];
        }
        $tieneEstado = $this->columnaExiste('metodos_pago', 'id_estado');
        $where = $tieneEstado ? "WHERE id_estado = 1" : "";
        return $this->select("SELECT id_metodo_pago, nombre_metodo_pago FROM metodos_pago {$where} ORDER BY id_metodo_pago ASC");
    }

    public function obtenerProductosParaVenta(int $idSede): array {
        $tienePrecioSede = $this->tablaExiste('precios_presentacion_sede');
        $joinPrecioSede = $tienePrecioSede
            ? "LEFT JOIN precios_presentacion_sede pps ON pps.id_presentacion = pp.id_presentacion AND pps.id_sede = :id_sede_precio"
            : "";
        $selectPrecioBase = $tienePrecioSede
            ? "COALESCE(pps.precio_venta, pp.precio_venta_presentacion, 0) AS precio_venta_base,"
            : "COALESCE(pp.precio_venta_presentacion, 0) AS precio_venta_base,";

        $query = "SELECT
                    p.id_producto,
                    p.nombre_producto,
                    p.id_categoria,
                    cp.nombre_categoria,
                    pp.id_presentacion,
                    pp.nombre_presentacion,
                    pp.id_tipo_producto,
                    pp.unidad_medida_productos_presentacion,
                    COALESCE(pp.precio_compra_presentacion, 0) AS precio_compra_presentacion,
                    {$selectPrecioBase}
                    COALESCE(d.porcentaje_descuento, 0) AS porcentaje_descuento,
                    COALESCE(iss.stock_sede, 0) AS stock_unidades,
                    COALESCE(ie.cantidad_gramos, 0) AS stock_gramos,
                    COALESCE(ie.costo_por_gramo, 0) AS costo_por_gramo
                  FROM productos_presentaciones pp
                  INNER JOIN productos p ON pp.id_producto = p.id_producto
                  INNER JOIN categoria_producto cp ON p.id_categoria = cp.id_categoria
                  LEFT JOIN inventario_sedes iss ON iss.id_presentacion = pp.id_presentacion AND iss.id_sede = :id_sede_stock
                  LEFT JOIN inventario_esencias ie ON ie.id_presentacion = pp.id_presentacion AND ie.id_sede = :id_sede_esencia
                  {$joinPrecioSede}
                  LEFT JOIN descuentos d
                    ON pp.id_descuento = d.id_descuento
                    AND d.id_estado = 1
                    AND CURDATE() BETWEEN d.fecha_inicio_descuento AND d.fecha_fin_descuento
                  WHERE p.id_estado = 1 AND pp.id_estado = 1
                  ORDER BY cp.nombre_categoria ASC, p.nombre_producto ASC, pp.nombre_presentacion ASC";

        $params = [
            ':id_sede_stock' => $idSede,
            ':id_sede_esencia' => $idSede
        ];
        if ($tienePrecioSede) {
            $params[':id_sede_precio'] = $idSede;
        }

        $productos = $this->select($query, $params);
        $tieneReceta = $this->tablaExiste('receta_presentacion');

        foreach ($productos as &$prod) {
            $idPres = (int)$prod['id_presentacion'];
            $precioBase = (float)$prod['precio_venta_base'];
            $desc = (float)$prod['porcentaje_descuento'];
            $prod['precio_venta_final'] = $desc > 0
                ? round($precioBase * (1 - ($desc / 100)), 2)
                : $precioBase;

            // Evaluar receta si existe para productos preparados (id_tipo_producto = 3 o con receta asociada)
            if ($tieneReceta) {
                $receta = $this->obtenerRecetaPresentacion($idPres);
                if (!empty($receta)) {
                    $calc = $this->calcularDisponibilidadYCostoPorReceta($receta, $idSede);
                    $prod['stock_disponible'] = $calc['unidades_disponibles'];
                    $prod['costo_unitario_actual'] = $calc['costo_unitario'];
                    $prod['unidad_venta'] = 'und';
                    $prod['usa_receta'] = true;
                    continue;
                }
            }

            $prod['usa_receta'] = false;
            if (($prod['unidad_medida_productos_presentacion'] ?? '') === 'gramo') {
                $prod['stock_disponible'] = (float)$prod['stock_gramos'];
                $prod['costo_unitario_actual'] = (float)$prod['costo_por_gramo'];
                $prod['unidad_venta'] = 'g';
            } else {
                $prod['stock_disponible'] = (int)$prod['stock_unidades'];
                $prod['costo_unitario_actual'] = (float)$prod['precio_compra_presentacion'];
                $prod['unidad_venta'] = 'und';
            }
        }

        return $productos;
    }

    private function obtenerRecetaPresentacion(int $idPresentacion): array {
        $query = "SELECT
                    id_receta,
                    id_presentacion,
                    unidad_componente,
                    id_producto_componente,
                    id_presentacion_componente,
                    cantidad_componente
                  FROM receta_presentacion
                  WHERE id_presentacion = :id_presentacion";
        return $this->select($query, [':id_presentacion' => $idPresentacion]);
    }

    private function calcularDisponibilidadYCostoPorReceta(array $receta, int $idSede): array {
        $maxUnidades = null;
        $costoTotalReceta = 0.0;

        foreach ($receta as $comp) {
            $req = (float)$comp['cantidad_componente'];
            if ($req <= 0) continue;

            if ($comp['unidad_componente'] === 'gramo' && !empty($comp['id_producto_componente'])) {
                $res = $this->select(
                    "SELECT
                        COALESCE(SUM(ie.cantidad_gramos), 0) AS total_gramos,
                        CASE
                            WHEN SUM(COALESCE(ie.cantidad_gramos, 0)) > 0
                            THEN SUM(ie.cantidad_gramos * ie.costo_por_gramo) / SUM(ie.cantidad_gramos)
                            ELSE 0
                        END AS costo_gramo
                     FROM productos_presentaciones pp
                     LEFT JOIN inventario_esencias ie
                       ON ie.id_presentacion = pp.id_presentacion
                       AND ie.id_sede = :id_sede
                     WHERE pp.id_producto = :id_producto",
                    [
                        ':id_sede' => $idSede,
                        ':id_producto' => (int)$comp['id_producto_componente']
                    ]
                );
                $disponibleGramos = (float)($res[0]['total_gramos'] ?? 0);
                $costoGramo = (float)($res[0]['costo_gramo'] ?? 0);
                $posibles = (int)floor($disponibleGramos / $req);
                $costoTotalReceta += ($req * $costoGramo);
            } else {
                $res = $this->select(
                    "SELECT
                        COALESCE(iss.stock_sede, 0) AS stock_sede,
                        COALESCE(pp.precio_compra_presentacion, 0) AS costo_unidad
                     FROM productos_presentaciones pp
                     LEFT JOIN inventario_sedes iss
                       ON iss.id_presentacion = pp.id_presentacion
                       AND iss.id_sede = :id_sede
                     WHERE pp.id_presentacion = :id_presentacion
                     LIMIT 1",
                    [
                        ':id_sede' => $idSede,
                        ':id_presentacion' => (int)$comp['id_presentacion_componente']
                    ]
                );
                $disponibleUnidades = (float)($res[0]['stock_sede'] ?? 0);
                $costoUnidad = (float)($res[0]['costo_unidad'] ?? 0);
                $posibles = (int)floor($disponibleUnidades / $req);
                $costoTotalReceta += ($req * $costoUnidad);
            }

            $maxUnidades = ($maxUnidades === null) ? $posibles : min($maxUnidades, $posibles);
        }

        return [
            'unidades_disponibles' => $maxUnidades ?? 0,
            'costo_unitario' => round($costoTotalReceta, 2)
        ];
    }

    private function obtenerIdTipoMovimiento(string $nombre): ?int {
        if (!$this->tablaExiste('tipos_movimiento')) {
            return null;
        }
        $res = $this->select(
            "SELECT id_tipo_movimiento FROM tipos_movimiento WHERE nombre_movimiento = :nombre LIMIT 1",
            [':nombre' => $nombre]
        );
        return !empty($res) ? (int)$res[0]['id_tipo_movimiento'] : null;
    }

    public function registrarVentaCompleta(array $encabezado, array $detalles, ?array $pagoInicial = null): array {
        $this->beginTransaction();

        try {
            $idSede = (int)$encabezado['idSede'];
            $idUsuario = (int)$encabezado['idUsuario'];
            $subtotalVenta = (float)$encabezado['subtotalVenta'];
            $descuentoVenta = (float)$encabezado['descuentoVenta'];
            $totalVenta = (float)$encabezado['totalVenta'];
            $montoPagado = $pagoInicial ? (float)$pagoInicial['montoPago'] : 0.0;

            $estadoVenta = ($montoPagado >= $totalVenta && $totalVenta > 0) ? 'pagada' : 'pendiente';

            // Mapear catálogo de productos de la sede para validar stock y costos
            $catalogoSede = [];
            foreach ($this->obtenerProductosParaVenta($idSede) as $p) {
                $catalogoSede[(int)$p['id_presentacion']] = $p;
            }

            $costoTotalVenta = 0.0;
            $detallesPreparados = [];

            foreach ($detalles as $item) {
                $idPres = (int)$item['idPresentacion'];
                $cantidad = (int)$item['cantidad'];
                $precioUnitario = (float)$item['precioUnitario'];

                if (!isset($catalogoSede[$idPres])) {
                    throw new \DomainException("El producto seleccionado no está activo o no existe.");
                }

                $prodInfo = $catalogoSede[$idPres];
                if ($cantidad > (float)$prodInfo['stock_disponible']) {
                    throw new \DomainException("Stock insuficiente para {$prodInfo['nombre_producto']} - {$prodInfo['nombre_presentacion']}. Disponible: {$prodInfo['stock_disponible']}.");
                }

                $costoUnitario = (float)$prodInfo['costo_unitario_actual'];
                $costoTotalItem = round($costoUnitario * $cantidad, 2);
                $subtotalItem = round($precioUnitario * $cantidad, 2);

                $costoTotalVenta += $costoTotalItem;
                $detallesPreparados[] = [
                    'idPresentacion' => $idPres,
                    'cantidad' => $cantidad,
                    'precioUnitario' => $precioUnitario,
                    'subtotal' => $subtotalItem,
                    'costoUnitario' => $costoUnitario,
                    'costoTotal' => $costoTotalItem,
                    'prodInfo' => $prodInfo
                ];
            }

            // 1. Insertar encabezado en ventas
            $tieneMetodoEnVentas = $this->columnaExiste('ventas', 'id_metodo_pago');
            if ($tieneMetodoEnVentas) {
                $idVenta = (int)$this->execute(
                    "INSERT INTO ventas (
                        id_sede, numero_venta, id_cliente, id_usuario, id_metodo_pago,
                        subtotal_venta, descuento_venta, total_venta, costo_total_venta,
                        estado_venta, observacion_venta
                    ) VALUES (
                        :id_sede, :numero_venta, :id_cliente, :id_usuario, :id_metodo_pago,
                        :subtotal_venta, :descuento_venta, :total_venta, :costo_total_venta,
                        :estado_venta, :observacion_venta
                    )",
                    [
                        ':id_sede' => $idSede,
                        ':numero_venta' => $encabezado['numeroVenta'],
                        ':id_cliente' => !empty($encabezado['idCliente']) ? (int)$encabezado['idCliente'] : null,
                        ':id_usuario' => $idUsuario,
                        ':id_metodo_pago' => !empty($pagoInicial['idMetodoPago']) ? (int)$pagoInicial['idMetodoPago'] : 1,
                        ':subtotal_venta' => $subtotalVenta,
                        ':descuento_venta' => $descuentoVenta,
                        ':total_venta' => $totalVenta,
                        ':costo_total_venta' => $costoTotalVenta,
                        ':estado_venta' => $estadoVenta,
                        ':observacion_venta' => $encabezado['observacionVenta'] ?: null
                    ]
                );
            } else {
                $idVenta = (int)$this->execute(
                    "INSERT INTO ventas (
                        id_sede, numero_venta, id_cliente, id_usuario,
                        subtotal_venta, descuento_venta, total_venta, costo_total_venta,
                        estado_venta, observacion_venta
                    ) VALUES (
                        :id_sede, :numero_venta, :id_cliente, :id_usuario,
                        :subtotal_venta, :descuento_venta, :total_venta, :costo_total_venta,
                        :estado_venta, :observacion_venta
                    )",
                    [
                        ':id_sede' => $idSede,
                        ':numero_venta' => $encabezado['numeroVenta'],
                        ':id_cliente' => !empty($encabezado['idCliente']) ? (int)$encabezado['idCliente'] : null,
                        ':id_usuario' => $idUsuario,
                        ':subtotal_venta' => $subtotalVenta,
                        ':descuento_venta' => $descuentoVenta,
                        ':total_venta' => $totalVenta,
                        ':costo_total_venta' => $costoTotalVenta,
                        ':estado_venta' => $estadoVenta,
                        ':observacion_venta' => $encabezado['observacionVenta'] ?: null
                    ]
                );
            }

            $idTipoMovVenta = $this->obtenerIdTipoMovimiento('VENTA');

            // 2. Insertar detalles y descontar stock + registrar kardex
            foreach ($detallesPreparados as $det) {
                $this->execute(
                    "INSERT INTO detalle_venta (
                        id_venta, id_presentacion, cantidad_detalle_venta,
                        precio_unitario_detalle_venta, subtotal_detalle_venta,
                        costo_unitario_detalle_venta, costo_total_detalle_venta
                    ) VALUES (
                        :id_venta, :id_presentacion, :cantidad,
                        :precio_unitario, :subtotal,
                        :costo_unitario, :costo_total
                    )",
                    [
                        ':id_venta' => $idVenta,
                        ':id_presentacion' => $det['idPresentacion'],
                        ':cantidad' => $det['cantidad'],
                        ':precio_unitario' => $det['precioUnitario'],
                        ':subtotal' => $det['subtotal'],
                        ':costo_unitario' => $det['costoUnitario'],
                        ':costo_total' => $det['costoTotal']
                    ]
                );

                $this->descontarStockItemVenta(
                    $idSede,
                    $idVenta,
                    $idUsuario,
                    $idTipoMovVenta,
                    $det['idPresentacion'],
                    $det['cantidad'],
                    $det['prodInfo']
                );
            }

            // 3. Registrar pago inicial y movimiento de caja si aplica
            if ($pagoInicial && $montoPagado > 0 && !empty($pagoInicial['idMetodoPago'])) {
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

        } catch (\Exception $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    private function descontarStockItemVenta(
        int $idSede,
        int $idVenta,
        int $idUsuario,
        ?int $idTipoMov,
        int $idPresentacion,
        int $cantidad,
        array $prodInfo
    ): void {
        if (!empty($prodInfo['usa_receta']) && $this->tablaExiste('receta_presentacion')) {
            $receta = $this->obtenerRecetaPresentacion($idPresentacion);
            foreach ($receta as $comp) {
                $totalRequerido = (float)$comp['cantidad_componente'] * $cantidad;
                if ($comp['unidad_componente'] === 'gramo' && !empty($comp['id_producto_componente'])) {
                    $this->descontarGramosPorProductoFIFO(
                        $idSede,
                        (int)$comp['id_producto_componente'],
                        $totalRequerido,
                        $idVenta,
                        $idUsuario,
                        $idTipoMov,
                        "Consumo receta venta #{$idVenta}"
                    );
                } elseif ($comp['unidad_componente'] === 'unidad' && !empty($comp['id_presentacion_componente'])) {
                    $this->descontarUnidadesPresentacion(
                        $idSede,
                        (int)$comp['id_presentacion_componente'],
                        (int)ceil($totalRequerido),
                        $idVenta,
                        $idUsuario,
                        $idTipoMov,
                        "Consumo envase receta venta #{$idVenta}"
                    );
                }
            }
            return;
        }

        if (($prodInfo['unidad_medida_productos_presentacion'] ?? '') === 'gramo') {
            $this->descontarGramosPresentacion(
                $idSede,
                $idPresentacion,
                (float)$cantidad,
                $idVenta,
                $idUsuario,
                $idTipoMov,
                "Venta directa #{$idVenta}"
            );
        } else {
            $this->descontarUnidadesPresentacion(
                $idSede,
                $idPresentacion,
                $cantidad,
                $idVenta,
                $idUsuario,
                $idTipoMov,
                "Venta directa #{$idVenta}"
            );
        }
    }

    private function descontarUnidadesPresentacion(
        int $idSede,
        int $idPresentacion,
        int $cantidad,
        int $idVenta,
        int $idUsuario,
        ?int $idTipoMov,
        string $obs
    ): void {
        $rows = $this->select(
            "SELECT stock_sede FROM inventario_sedes WHERE id_sede = :id_sede AND id_presentacion = :id_pres LIMIT 1",
            [':id_sede' => $idSede, ':id_pres' => $idPresentacion]
        );
        $actual = (int)($rows[0]['stock_sede'] ?? 0);
        if ($actual < $cantidad) {
            throw new \DomainException("Stock de unidades insuficiente en la sede para la presentación #{$idPresentacion}.");
        }
        $nuevo = $actual - $cantidad;

        $this->execute(
            "UPDATE inventario_sedes SET stock_sede = :nuevo WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
            [':nuevo' => $nuevo, ':id_sede' => $idSede, ':id_pres' => $idPresentacion]
        );

        $this->insertarKardex($idSede, $idPresentacion, $idTipoMov, 'unidad', -$cantidad, $nuevo, $idVenta, $idUsuario, $obs);
    }

    private function descontarGramosPresentacion(
        int $idSede,
        int $idPresentacion,
        float $gramos,
        int $idVenta,
        int $idUsuario,
        ?int $idTipoMov,
        string $obs
    ): void {
        $rows = $this->select(
            "SELECT cantidad_gramos FROM inventario_esencias WHERE id_sede = :id_sede AND id_presentacion = :id_pres LIMIT 1",
            [':id_sede' => $idSede, ':id_pres' => $idPresentacion]
        );
        $actual = (float)($rows[0]['cantidad_gramos'] ?? 0);
        if ($actual < $gramos) {
            throw new \DomainException("Stock de gramos insuficiente en la sede para la esencia #{$idPresentacion}.");
        }
        $nuevo = $actual - $gramos;

        $this->execute(
            "UPDATE inventario_esencias SET cantidad_gramos = :nuevo, fecha_actualizacion = NOW() WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
            [':nuevo' => $nuevo, ':id_sede' => $idSede, ':id_pres' => $idPresentacion]
        );

        $this->insertarKardex($idSede, $idPresentacion, $idTipoMov, 'gramo', -$gramos, $nuevo, $idVenta, $idUsuario, $obs);
    }

    private function descontarGramosPorProductoFIFO(
        int $idSede,
        int $idProducto,
        float $gramosRequeridos,
        int $idVenta,
        int $idUsuario,
        ?int $idTipoMov,
        string $obs
    ): void {
        $lotes = $this->select(
            "SELECT ie.id_presentacion, ie.cantidad_gramos
             FROM inventario_esencias ie
             INNER JOIN productos_presentaciones pp ON ie.id_presentacion = pp.id_presentacion
             WHERE pp.id_producto = :id_producto AND ie.id_sede = :id_sede AND ie.cantidad_gramos > 0
             ORDER BY ie.id_presentacion ASC",
            [':id_producto' => $idProducto, ':id_sede' => $idSede]
        );

        $pendiente = $gramosRequeridos;
        foreach ($lotes as $lote) {
            if ($pendiente <= 0) break;
            $disp = (float)$lote['cantidad_gramos'];
            $tomar = min($disp, $pendiente);
            $nuevo = $disp - $tomar;
            $idPresLote = (int)$lote['id_presentacion'];

            $this->execute(
                "UPDATE inventario_esencias SET cantidad_gramos = :nuevo, fecha_actualizacion = NOW()
                 WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
                [':nuevo' => $nuevo, ':id_sede' => $idSede, ':id_pres' => $idPresLote]
            );

            $this->insertarKardex($idSede, $idPresLote, $idTipoMov, 'gramo', -$tomar, $nuevo, $idVenta, $idUsuario, $obs);
            $pendiente -= $tomar;
        }

        if ($pendiente > 0.001) {
            throw new \DomainException("No hay suficientes gramos de esencia en la sede para preparar la receta.");
        }
    }

    private function insertarKardex(
        int $idSede,
        int $idPresentacion,
        ?int $idTipoMov,
        string $unidad,
        float $cantidad,
        float $saldoResultante,
        int $idVenta,
        int $idUsuario,
        string $obs
    ): void {
        if (!$idTipoMov || !$this->tablaExiste('movimientos_inventario')) {
            return;
        }

        $this->execute(
            "INSERT INTO movimientos_inventario (
                id_sede, id_presentacion, id_tipo_movimiento,
                unidad_movimiento, cantidad_movimiento, saldo_resultante,
                id_venta, id_usuario, observacion_movimiento
            ) VALUES (
                :id_sede, :id_pres, :id_tipo,
                :unidad, :cantidad, :saldo,
                :id_venta, :id_usuario, :obs
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
                ':obs' => $obs
            ]
        );
    }

    private function registrarPagoYCaja(
        int $idVenta,
        int $idSede,
        int $idMetodoPago,
        float $monto,
        int $idUsuario,
        string $obs
    ): int {
        $idPagoVenta = 0;

        if ($this->tablaExiste('pagos_venta')) {
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
        }

        if ($this->tablaExiste('caja_movimientos')) {
            if ($this->columnaExiste('caja_movimientos', 'id_pago_venta')) {
                $this->execute(
                    "INSERT INTO caja_movimientos (
                        id_sede, tipo_caja, concepto_caja, monto_caja,
                        id_metodo_pago, id_pago_venta, id_usuario, observacion_caja
                    ) VALUES (
                        :id_sede, 'ingreso', 'pago_venta', :monto,
                        :id_metodo, :id_pago_venta, :id_usuario, :obs
                    )",
                    [
                        ':id_sede' => $idSede,
                        ':monto' => $monto,
                        ':id_metodo' => $idMetodoPago,
                        ':id_pago_venta' => $idPagoVenta ?: null,
                        ':id_usuario' => $idUsuario,
                        ':obs' => $obs
                    ]
                );
            } else {
                $this->execute(
                    "INSERT INTO caja_movimientos (
                        id_sede, tipo_caja, concepto_caja, monto_caja,
                        id_metodo_pago, id_venta, id_usuario, observacion_caja
                    ) VALUES (
                        :id_sede, 'ingreso', 'venta', :monto,
                        :id_metodo, :id_venta, :id_usuario, :obs
                    )",
                    [
                        ':id_sede' => $idSede,
                        ':monto' => $monto,
                        ':id_metodo' => $idMetodoPago,
                        ':id_venta' => $idVenta,
                        ':id_usuario' => $idUsuario,
                        ':obs' => $obs
                    ]
                );
            }
        }

        return $idPagoVenta;
    }

    public function registrarAbonoVenta(int $idVenta, int $idMetodoPago, float $montoPago, int $idUsuario): array {
        $this->beginTransaction();

        try {
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
            $nuevoEstado = ($nuevoPagado >= $total - 0.01) ? 'pagada' : 'pendiente';

            if ($nuevoEstado === 'pagada') {
                $this->execute(
                    "UPDATE ventas SET estado_venta = 'pagada' WHERE id_venta = :id_venta",
                    [':id_venta' => $idVenta]
                );
            }

            $this->commit();
            return ['id_venta' => $idVenta, 'estado_venta' => $nuevoEstado];

        } catch (\Exception $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $e;
        }
    }

    public function anularVenta(int $idVenta, int $idUsuario): bool {
        $this->beginTransaction();

        try {
            $venta = $this->obtenerVentaPorId($idVenta);
            if (!$venta) {
                throw new \DomainException("La venta no existe.");
            }
            if ($venta['estado_venta'] === 'anulada') {
                throw new \DomainException("La venta ya se encuentra anulada.");
            }

            $idSede = (int)$venta['id_sede'];
            $idTipoAnulacion = $this->obtenerIdTipoMovimiento('ANULACION_VENTA');

            // Si existen movimientos en kardex para esta venta, revertir exactamente esos movimientos
            $movimientos = $this->tablaExiste('movimientos_inventario')
                ? $this->select("SELECT * FROM movimientos_inventario WHERE id_venta = :id_venta", [':id_venta' => $idVenta])
                : [];

            if (!empty($movimientos)) {
                foreach ($movimientos as $mov) {
                    $idPres = (int)$mov['id_presentacion'];
                    $cantRevertir = abs((float)$mov['cantidad_movimiento']);

                    if ($mov['unidad_movimiento'] === 'gramo') {
                        $rows = $this->select(
                            "SELECT cantidad_gramos FROM inventario_esencias WHERE id_sede = :id_sede AND id_presentacion = :id_pres LIMIT 1",
                            [':id_sede' => $idSede, ':id_pres' => $idPres]
                        );
                        $nuevo = ((float)($rows[0]['cantidad_gramos'] ?? 0)) + $cantRevertir;
                        $this->execute(
                            "UPDATE inventario_esencias SET cantidad_gramos = :nuevo, fecha_actualizacion = NOW()
                             WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
                            [':nuevo' => $nuevo, ':id_sede' => $idSede, ':id_pres' => $idPres]
                        );
                        $this->insertarKardex($idSede, $idPres, $idTipoAnulacion, 'gramo', $cantRevertir, $nuevo, $idVenta, $idUsuario, "Anulación venta {$venta['numero_venta']}");
                    } else {
                        $rows = $this->select(
                            "SELECT stock_sede FROM inventario_sedes WHERE id_sede = :id_sede AND id_presentacion = :id_pres LIMIT 1",
                            [':id_sede' => $idSede, ':id_pres' => $idPres]
                        );
                        $nuevo = ((int)($rows[0]['stock_sede'] ?? 0)) + (int)$cantRevertir;
                        $this->execute(
                            "UPDATE inventario_sedes SET stock_sede = :nuevo WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
                            [':nuevo' => $nuevo, ':id_sede' => $idSede, ':id_pres' => $idPres]
                        );
                        $this->insertarKardex($idSede, $idPres, $idTipoAnulacion, 'unidad', $cantRevertir, $nuevo, $idVenta, $idUsuario, "Anulación venta {$venta['numero_venta']}");
                    }
                }
            } else {
                // Fallback si no había filas en movimientos_inventario
                $detalles = $this->obtenerDetallesVenta($idVenta);
                foreach ($detalles as $det) {
                    $idPres = (int)$det['id_presentacion'];
                    $cant = (int)$det['cantidad_detalle_venta'];
                    $this->execute(
                        "UPDATE inventario_sedes SET stock_sede = stock_sede + :cant WHERE id_sede = :id_sede AND id_presentacion = :id_pres",
                        [':cant' => $cant, ':id_sede' => $idSede, ':id_pres' => $idPres]
                    );
                }
            }

            // Revertir ingreso en caja si hubo pagos registrados
            $totalPagado = (float)($venta['total_pagado'] ?? 0);
            if ($totalPagado > 0 && $this->tablaExiste('caja_movimientos')) {
                $this->execute(
                    "INSERT INTO caja_movimientos (
                        id_sede, tipo_caja, concepto_caja, monto_caja,
                        id_usuario, observacion_caja
                    ) VALUES (
                        :id_sede, 'egreso', 'anulacion_venta', :monto,
                        :id_usuario, :obs
                    )",
                    [
                        ':id_sede' => $idSede,
                        ':monto' => $totalPagado,
                        ':id_usuario' => $idUsuario,
                        ':obs' => "Reversión por anulación de venta {$venta['numero_venta']}"
                    ]
                );
            }

            $this->execute(
                "UPDATE ventas SET estado_venta = 'anulada' WHERE id_venta = :id_venta",
                [':id_venta' => $idVenta]
            );

            $this->commit();
            return true;

        } catch (\Exception $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            throw $e;
        }
    }
}

