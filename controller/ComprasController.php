<?php

require_once('../core/response.php');
require_once('../helper/utils.php');
require_once('../core/Logger.php');
require_once('../core/CustomExceptions.php');
require_once('../services/ComprasService.php');

class ComprasController {
    private ComprasService $servicio;

    public function __construct(ComprasService $servicio) {
        $this->servicio = $servicio;
    }

    public function listar(): array {
        try {
            $compras = $this->servicio->obtenerCompras();
            return response::success($compras, 'Compras obtenidas exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al listar compras", $e, $_REQUEST);
            return response::error('Error al obtener compras');
        }
    }

    public function registrar(array $request): array {
        try {
            if (!utils::validateRequiredFields(['idProveedor', 'totalCompra', 'numeroFacturaCompra', 'detalles'], $request)) {
                return response::error('Todos los campos son obligatorios');
            }

            $params = [
                'idProveedor' => utils::sanitizeInput($request['idProveedor']),
                'totalCompra' => utils::sanitizeInput($request['totalCompra']),
                'numeroFacturaCompra' => utils::sanitizeInput($request['numeroFacturaCompra'])
            ];

            $detalles = json_decode($request['detalles'], true);
            if (!is_array($detalles) || empty($detalles)) {
                return response::error('Detalles inválidos');
            }

            $data = $this->servicio->registrarCompraConDetalles($params, $detalles);
            return response::success($data, 'Compra y detalles registrados exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar compra", $e, $_REQUEST);
            return response::error('Error al registrar la compra');
        }
    }

    public function detalle(array $request): array {
        try {
            $idCompra = (int)($request['idCompra'] ?? 0);
            if (!$idCompra) {
                return response::error('ID de compra requerido');
            }
            $compra = $this->servicio->obtenerCompraPorId($idCompra);
            $detalles = $this->servicio->obtenerDetalleCompra($idCompra);
            return response::success(['compra' => $compra, 'detalles' => $detalles], 'Detalle obtenido');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener detalle de compra", $e, $_REQUEST);
            return response::error('Error al obtener detalle');
        }
    }

    public function actualizar(array $request): array {
        try {
            $idCompra = (int)($request['idCompra'] ?? 0);
            if (!$idCompra) {
                return response::error('ID de compra requerido');
            }
            if (!utils::validateRequiredFields(['idProveedor', 'totalCompra', 'numeroFacturaCompra', 'detalles'], $request)) {
                return response::error('Todos los campos son obligatorios');
            }
            $params = [
                'idProveedor' => utils::sanitizeInput($request['idProveedor']),
                'totalCompra' => utils::sanitizeInput($request['totalCompra']),
                'numeroFacturaCompra' => utils::sanitizeInput($request['numeroFacturaCompra'])
            ];
            $detalles = json_decode($request['detalles'], true);
            if (!is_array($detalles) || empty($detalles)) {
                return response::error('Detalles inválidos');
            }
            $data = $this->servicio->actualizarCompraConDetalles($idCompra, $params, $detalles);
            return response::success($data, 'Compra actualizada exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar compra", $e, $_REQUEST);
            return response::error('Error al actualizar la compra');
        }
    }

    public function eliminar(array $request): array {
        try {
            $idCompra = (int)($request['idCompra'] ?? 0);
            if (!$idCompra) {
                return response::error('ID de compra requerido');
            }
            $this->servicio->eliminarCompra($idCompra);
            return response::success(null, 'Compra eliminada exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al eliminar compra", $e, $_REQUEST);
            return response::error('Error al eliminar la compra');
        }
    }
}
