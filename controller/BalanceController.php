<?php

require_once(__DIR__ . '/../core/response.php');
require_once(__DIR__ . '/../core/Logger.php');
require_once(__DIR__ . '/../services/BalanceService.php');

class BalanceController {
    private BalanceService $servicio;

    public function __construct(BalanceService $servicio) {
        $this->servicio = $servicio;
    }

    public function balance(array $request): array {
        try {
            $mes = $request['mes'] ?? date('Y-m');
            $idSede = !empty($request['idSede']) ? (int)$request['idSede'] : null;
            $data = $this->servicio->balanceMensual($mes, $idSede);
            return response::success($data, 'Balance obtenido');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error('Error al calcular balance mensual', $e, $request);
            return response::error('Error al calcular el balance');
        }
    }
}
