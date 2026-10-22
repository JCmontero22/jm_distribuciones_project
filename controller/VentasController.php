<?php

require_once(__DIR__ . '/../core/response.php');
require_once(__DIR__ . '/../helper/utils.php');
require_once(__DIR__ . '/../core/Logger.php');
require_once(__DIR__ . '/../core/Auth.php');
require_once(__DIR__ . '/../services/VentasService.php');

class VentasController {
    private VentasService $servicio;

    public function __construct(VentasService $servicio) {
        $this->servicio = $servicio;
    }

    /** Usuario de la sesion. Nunca se asume un usuario por defecto: sin sesion no hay venta. */
    private function usuarioActual(): int {
        $idUsuario = Auth::userId();
        if (!$idUsuario) {
            throw new \DomainException('Sesión no válida. Inicia sesión nuevamente.');
        }
        return (int)$idUsuario;
    }

    /** Sede efectiva: la del vendedor (sesion) o la que elige el administrador. */
    private function sedeEfectiva(array $request): int {
        return Auth::sedeUsuario() ?? (int)($request['idSede'] ?? 0);
    }

    public function listar(array $request): array {
        try {
            $idSede = Auth::sedeUsuario() ?? (!empty($request['idSede']) ? (int)$request['idSede'] : null);
            $estado = !empty($request['estado']) ? utils::sanitizeInput($request['estado']) : null;
            $desde = $this->fechaValida($request['desde'] ?? null);
            $hasta = $this->fechaValida($request['hasta'] ?? null);
            $texto = !empty($request['texto']) ? utils::sanitizeInput($request['texto']) : null;

            $data = $this->servicio->obtenerListadoYResumen($idSede, $estado, $desde, $hasta, $texto);
            return response::success($data, 'Ventas obtenidas exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al listar ventas', $e, $request);
            return response::error('Error al obtener el listado de ventas');
        }
    }

    /** Devuelve la fecha si tiene formato Y-m-d valido; si no, null. */
    private function fechaValida(?string $fecha): ?string {
        return ($fecha && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) ? $fecha : null;
    }

    public function catalogosVenta(array $request): array {
        try {
            $sedeFija = Auth::sedeUsuario();
            $data = $this->servicio->obtenerCatalogosVenta($this->sedeEfectiva($request), $sedeFija);
            return response::success($data, 'Catálogos de venta obtenidos');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al obtener catálogos de venta', $e, $request);
            return response::error('Error al cargar los catálogos de venta');
        }
    }

    public function productosPorSede(array $request): array {
        try {
            $data = $this->servicio->obtenerProductosYConsecutivoPorSede($this->sedeEfectiva($request));
            return response::success($data, 'Productos de sede obtenidos');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al obtener productos por sede para venta', $e, $request);
            return response::error('Error al cargar productos de la sede');
        }
    }

    public function detalle(array $request): array {
        try {
            $idVenta = (int)($request['idVenta'] ?? 0);
            $this->servicio->validarSedeVenta($idVenta, Auth::sedeUsuario());
            $data = $this->servicio->obtenerDetalleCompletoVenta($idVenta);
            return response::success($data, 'Detalle de venta obtenido');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al obtener detalle de venta', $e, $request);
            return response::error('Error al obtener el detalle de la venta');
        }
    }

    public function registrar(array $request): array {
        try {
            $idSede = $this->sedeEfectiva($request);
            if ($idSede <= 0) {
                return response::error('Selecciona la sede de la venta');
            }
            if (!utils::validateRequiredFields(['numeroVenta', 'detalles'], $request)) {
                return response::error('El número de venta y los productos son obligatorios');
            }

            $detalles = json_decode($request['detalles'], true);
            if (!is_array($detalles) || empty($detalles)) {
                return response::error('El carrito de venta está vacío o es inválido');
            }

            $idUsuario = $this->usuarioActual();
            $encabezado = [
                'idSede' => $idSede,
                'numeroVenta' => utils::sanitizeInput($request['numeroVenta']),
                'idCliente' => !empty($request['idCliente']) ? (int)$request['idCliente'] : null,
                'idUsuario' => $idUsuario,
                'subtotalVenta' => (float)($request['subtotalVenta'] ?? 0),
                'descuentoVenta' => (float)($request['descuentoVenta'] ?? 0),
                'totalVenta' => (float)($request['totalVenta'] ?? 0),
                'observacionVenta' => isset($request['observacionVenta']) ? utils::sanitizeInput($request['observacionVenta']) : null
            ];

            $montoInicial = (float)($request['montoPagoInicial'] ?? 0);
            $idMetodoPago = !empty($request['idMetodoPago']) ? (int)$request['idMetodoPago'] : 0;
            $pagoInicial = null;

            if ($montoInicial > 0 && $idMetodoPago > 0) {
                $pagoInicial = [
                    'idMetodoPago' => $idMetodoPago,
                    'montoPago' => $montoInicial
                ];
            }

            $data = $this->servicio->registrarVenta($encabezado, $detalles, $pagoInicial);
            return response::success($data, 'Venta registrada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al registrar venta', $e, $request);
            return response::error('No se pudo registrar la venta. Verifica que el número de venta no esté duplicado.');
        }
    }

    public function registrarAbono(array $request): array {
        try {
            if (!utils::validateRequiredFields(['idVenta', 'idMetodoPago', 'montoPago'], $request)) {
                return response::error('Todos los campos del abono son obligatorios');
            }

            $idVenta = (int)$request['idVenta'];
            $idMetodoPago = (int)$request['idMetodoPago'];
            $montoPago = (float)$request['montoPago'];
            $idUsuario = $this->usuarioActual();
            $this->servicio->validarSedeVenta($idVenta, Auth::sedeUsuario());

            $data = $this->servicio->registrarAbono($idVenta, $idMetodoPago, $montoPago, $idUsuario);
            return response::success($data, 'Abono registrado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al registrar abono de venta', $e, $request);
            return response::error('Error al registrar el abono');
        }
    }

    public function anular(array $request): array {
        try {
            $idVenta = (int)($request['idVenta'] ?? 0);
            $idUsuario = $this->usuarioActual();

            $this->servicio->validarSedeVenta($idVenta, Auth::sedeUsuario());
            $this->servicio->anularVenta($idVenta, $idUsuario);
            return response::success(null, 'Venta anulada y stock restaurado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al anular venta', $e, $request);
            return response::error('Error al anular la venta');
        }
    }

    public function preciosLocion(array $request): array {
        try {
            $data = $this->servicio->obtenerPreciosLocion($this->sedeEfectiva($request));
            return response::success($data, 'Precios de lociones obtenidos');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al obtener precios de lociones', $e, $request);
            return response::error('Error al obtener los precios');
        }
    }

    public function guardarPrecioLocion(array $request): array {
        try {
            if (Auth::sedeUsuario()) {
                return response::error('Solo el administrador puede cambiar los precios');
            }
            if (!utils::validateRequiredFields(['idSede', 'idProductoEsencia', 'idFormula', 'precio'], $request)) {
                return response::error('Sede, esencia, fórmula y precio son obligatorios');
            }
            $this->servicio->guardarPrecioLocion(
                (int)$request['idSede'],
                (int)$request['idProductoEsencia'],
                (int)$request['idFormula'],
                (float)$request['precio']
            );
            return response::success(null, 'Precio guardado');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al guardar precio de loción', $e, $request);
            return response::error('Error al guardar el precio');
        }
    }

    public function registrarCliente(array $request): array {
        try {
            if (!utils::validateRequiredFields(['nombreCliente'], $request)) {
                return response::error('El nombre del cliente es obligatorio');
            }

            $params = [
                'nombreCliente' => utils::sanitizeInput($request['nombreCliente']),
                'identificacionCliente' => isset($request['identificacionCliente']) ? utils::sanitizeInput($request['identificacionCliente']) : '',
                'telefonoCliente' => isset($request['telefonoCliente']) ? utils::sanitizeInput($request['telefonoCliente']) : '',
                'direccionCliente' => isset($request['direccionCliente']) ? utils::sanitizeInput($request['direccionCliente']) : ''
            ];

            $data = $this->servicio->registrarCliente($params);
            return response::success($data, 'Cliente registrado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al registrar cliente desde ventas', $e, $request);
            return response::error('Error al registrar el cliente');
        }
    }
}

