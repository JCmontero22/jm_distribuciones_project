<?php

$projectRoot = __DIR__ . '/..';
require_once($projectRoot . '/core/response.php');
require_once($projectRoot . '/core/Logger.php');
require_once($projectRoot . '/services/CapacidadProduccionService.php');

class CapacidadProduccionController {

    private CapacidadProduccionService $servicio;

    public function __construct(CapacidadProduccionService $servicio) {
        $this->servicio = $servicio;
    }

    /**
     * Obtiene el reporte de capacidad de producción
     */
    public function obtenerReporte(): array {
        try {
            $capacidad = $this->servicio->calcularCapacidad();
            return response::success($capacidad['esencias'], 'Reporte de capacidad obtenido');
        } catch (\Exception $e) {
            Logger::error("Error al obtener reporte de capacidad", $e, []);
            return response::error('Error al obtener el reporte de capacidad');
        }
    }
}
