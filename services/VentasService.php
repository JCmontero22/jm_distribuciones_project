<?php

require_once(__DIR__ . '/../model/VentasModel.php');
require_once(__DIR__ . '/../model/SedesModel.php');
require_once(__DIR__ . '/../model/CategoriaModel.php');
require_once(__DIR__ . '/../core/Logger.php');

class VentasService {
    private VentasModel $ventasModel;
    private SedesModel $sedesModel;
    private CategoriaModel $categoriaModel;

    public function __construct(
        VentasModel $ventasModel,
        SedesModel $sedesModel,
        CategoriaModel $categoriaModel
    ) {
        $this->ventasModel = $ventasModel;
        $this->sedesModel = $sedesModel;
        $this->categoriaModel = $categoriaModel;
    }

    public function obtenerListadoYResumen(?int $idSede = null, ?string $estado = null): array {
        return [
            'ventas' => $this->ventasModel->obtenerVentas($idSede, $estado),
            'resumen' => $this->ventasModel->obtenerResumenVentas($idSede)
        ];
    }

    public function obtenerCatalogosVenta(int $idSede = 1): array {
        $sedes = $this->sedesModel->obtenerTodos();
        if ($idSede <= 0 && !empty($sedes)) {
            $idSede = (int)$sedes[0]['id_sede'];
        }

        return [
            'sedes' => $sedes,
            'categorias' => $this->categoriaModel->obtenerTodos(),
            'clientes' => $this->ventasModel->obtenerClientes(),
            'metodosPago' => $this->ventasModel->obtenerMetodosPago(),
            'productos' => $this->ventasModel->obtenerProductosParaVenta($idSede),
            'siguienteNumero' => $this->ventasModel->generarSiguienteNumeroVenta($idSede)
        ];
    }

    public function obtenerProductosYConsecutivoPorSede(int $idSede): array {
        if ($idSede <= 0) {
            throw new \DomainException('Sede inválida');
        }
        return [
            'productos' => $this->ventasModel->obtenerProductosParaVenta($idSede),
            'siguienteNumero' => $this->ventasModel->generarSiguienteNumeroVenta($idSede)
        ];
    }

    public function obtenerDetalleCompletoVenta(int $idVenta): array {
        if ($idVenta <= 0) {
            throw new \DomainException('ID de venta inválido');
        }
        $venta = $this->ventasModel->obtenerVentaPorId($idVenta);
        if (!$venta) {
            throw new \DomainException('La venta solicitada no existe');
        }

        return [
            'venta' => $venta,
            'detalles' => $this->ventasModel->obtenerDetallesVenta($idVenta),
            'pagos' => $this->ventasModel->obtenerPagosVenta($idVenta)
        ];
    }

    public function registrarVenta(array $encabezado, array $detalles, ?array $pagoInicial = null): array {
        if (empty($detalles)) {
            throw new \DomainException('Debes agregar al menos un producto a la venta');
        }

        $subtotalCalculado = 0.0;
        foreach ($detalles as $item) {
            $cant = (int)($item['cantidad'] ?? 0);
            $precio = (float)($item['precioUnitario'] ?? 0);
            if ($cant <= 0 || $precio <= 0) {
                throw new \DomainException('Las cantidades y precios unitarios deben ser mayores a 0');
            }
            $subtotalCalculado += ($cant * $precio);
        }

        $descuento = max(0.0, (float)($encabezado['descuentoVenta'] ?? 0));
        if ($descuento > $subtotalCalculado) {
            throw new \DomainException('El descuento no puede superar el subtotal de la venta');
        }

        $totalCalculado = max(0.0, $subtotalCalculado - $descuento);
        $encabezado['subtotalVenta'] = $subtotalCalculado;
        $encabezado['descuentoVenta'] = $descuento;
        $encabezado['totalVenta'] = $totalCalculado;

        if ($pagoInicial !== null && $pagoInicial['montoPago'] > $totalCalculado + 0.01) {
            throw new \DomainException('El pago inicial no puede superar el total de la venta');
        }

        return $this->ventasModel->registrarVentaCompleta($encabezado, $detalles, $pagoInicial);
    }

    public function registrarAbono(int $idVenta, int $idMetodoPago, float $montoPago, int $idUsuario): array {
        if ($idVenta <= 0 || $idMetodoPago <= 0 || $montoPago <= 0) {
            throw new \DomainException('Datos de abono inválidos');
        }
        return $this->ventasModel->registrarAbonoVenta($idVenta, $idMetodoPago, $montoPago, $idUsuario);
    }

    public function anularVenta(int $idVenta, int $idUsuario): bool {
        if ($idVenta <= 0) {
            throw new \DomainException('ID de venta inválido');
        }
        return $this->ventasModel->anularVenta($idVenta, $idUsuario);
    }

    public function registrarCliente(array $data): array {
        if (empty($data['nombreCliente'])) {
            throw new \DomainException('El nombre del cliente es obligatorio');
        }
        $idCliente = $this->ventasModel->registrarCliente($data);
        return [
            'id_cliente' => $idCliente,
            'clientes' => $this->ventasModel->obtenerClientes()
        ];
    }
}

