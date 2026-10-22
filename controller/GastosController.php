<?php

require_once(__DIR__ . '/../core/response.php');
require_once(__DIR__ . '/../helper/utils.php');
require_once(__DIR__ . '/../core/Logger.php');
require_once(__DIR__ . '/../core/Auth.php');
require_once(__DIR__ . '/../services/GastosService.php');

class GastosController {
    private GastosService $servicio;

    public function __construct(GastosService $servicio) {
        $this->servicio = $servicio;
    }

    public function registrar(array $request): array {
        try {
            if (!utils::validateRequiredFields(['idSede', 'categoriaGasto', 'montoGasto', 'fechaGasto'], $request)) {
                return response::error('La sede, la categoría, el monto y la fecha son obligatorios');
            }

            $idUsuario = Auth::userId();
            if (!$idUsuario) {
                return response::error('Sesión no válida. Inicia sesión nuevamente.');
            }

            $data = [
                'idSede' => Auth::sedeUsuario() ?? (int)$request['idSede'],
                'categoriaGasto' => utils::sanitizeInput($request['categoriaGasto']),
                'descripcionGasto' => isset($request['descripcionGasto']) ? utils::sanitizeInput($request['descripcionGasto']) : null,
                'montoGasto' => (float)$request['montoGasto'],
                'idMetodoPago' => !empty($request['idMetodoPago']) ? (int)$request['idMetodoPago'] : null,
                'idUsuario' => (int)$idUsuario,
                'fechaGasto' => utils::sanitizeInput($request['fechaGasto'])
            ];

            $resultado = $this->servicio->registrarGasto($data);
            return response::success($resultado, 'Gasto registrado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al registrar gasto', $e, $request);
            return response::error('No se pudo registrar el gasto');
        }
    }

    public function listar(array $request): array {
        try {
            $idSede = Auth::sedeUsuario() ?? (int)($request['idSede'] ?? 0);
            $desde = utils::sanitizeInput($request['desde'] ?? date('Y-m-01'));
            $hasta = utils::sanitizeInput($request['hasta'] ?? date('Y-m-d'));

            $gastos = $this->servicio->listarGastos($idSede, $desde, $hasta);
            return response::success($gastos, 'Gastos obtenidos');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al listar gastos', $e, $request);
            return response::error('Error al obtener los gastos');
        }
    }
}
