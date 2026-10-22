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

    public function obtenerListadoYResumen(?int $idSede = null, ?string $estado = null, ?string $desde = null, ?string $hasta = null, ?string $texto = null): array {
        return [
            'ventas' => $this->ventasModel->obtenerVentas($idSede, $estado, $desde, $hasta, $texto),
            'resumen' => $this->ventasModel->obtenerResumenVentas($idSede)
        ];
    }

    /** Categorias que no se venden: son insumos o no tienen productos de venta. */
    private const CATEGORIAS_NO_VENDIBLES = ['Insumos', 'Esencias', 'Lociones Preparadas'];

    /**
     * Catalogo de venta. Sin sede (idSede = 0) no se cargan categorias ni productos.
     * $sedeFija: sede asignada al usuario (vendedor). Si existe, solo se muestra esa sede.
     */
    public function obtenerCatalogosVenta(int $idSede = 0, ?int $sedeFija = null): array {
        $sedes = $this->sedesModel->obtenerSedes();
        if ($sedeFija) {
            $sedes = array_values(array_filter($sedes, fn($s) => (int)$s['id_sede'] === $sedeFija));
            $idSede = $sedeFija;
        }

        $data = [
            'sedes' => $sedes,
            'sedeFija' => $sedeFija,
            'clientes' => $this->ventasModel->obtenerClientes(),
            'metodosPago' => $this->ventasModel->obtenerMetodosPago(),
            'categorias' => [],
            'productos' => [],
            'siguienteNumero' => null
        ];

        if ($idSede > 0) {
            $data['categorias'] = $this->categoriasVendibles();
            $data['productos'] = $this->ventasModel->obtenerProductosParaVenta($idSede);
            $data['siguienteNumero'] = $this->ventasModel->generarSiguienteNumeroVenta($idSede);
        }

        return $data;
    }

    private function categoriasVendibles(): array {
        return array_values(array_filter(
            $this->categoriaModel->obtenerTodos(),
            fn($c) => ($c['id_estado'] ?? 1) == 1
                && !in_array($c['nombre_categoria'], self::CATEGORIAS_NO_VENDIBLES, true)
        ));
    }

    /** Lanza error si la venta no es de la sede del vendedor. */
    public function validarSedeVenta(int $idVenta, ?int $sedeUsuario): void {
        if (!$sedeUsuario) {
            return;
        }
        $venta = $this->ventasModel->obtenerVentaPorId($idVenta);
        if (!$venta) {
            throw new \DomainException('La venta no existe.');
        }
        if ((int)$venta['id_sede'] !== $sedeUsuario) {
            throw new \DomainException('Esta venta pertenece a otra sede.');
        }
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

    public function obtenerPreciosLocion(int $idSede): array {
        if ($idSede <= 0) {
            throw new \DomainException('Sede inválida');
        }
        return $this->ventasModel->obtenerPreciosLocion($idSede);
    }

    public function guardarPrecioLocion(int $idSede, int $idProductoEsencia, int $idFormula, float $precio): void {
        if ($idSede <= 0 || $idProductoEsencia <= 0 || $idFormula <= 0) {
            throw new \DomainException('Datos de precio inválidos');
        }
        if ($precio <= 0) {
            throw new \DomainException('El precio debe ser mayor a 0');
        }
        $this->ventasModel->guardarPrecioLocion($idSede, $idProductoEsencia, $idFormula, $precio);
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

