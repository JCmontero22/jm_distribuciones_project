<?php

require_once('../core/response.php');
require_once('../helper/utils.php');
require_once('../core/Logger.php');
require_once('../core/CustomExceptions.php');
require_once('../services/FormulasService.php');

class FormulasController {
    private FormulasService $servicio;

    public function __construct(FormulasService $servicio) {
        $this->servicio = $servicio;
    }

    public function registrar(array $request): array {
        try {
            if (!utils::validateRequiredFields(['nombreFormula', 'cantidadEsencia', 'insumo', 'concentracion'], $request)) {
                return response::error('Todos los campos son obligatorios');
            }

            $data = [
                'nombre_formula'        => utils::sanitizeInput($request['nombreFormula']),
                'cantidad_esencia'      => utils::sanitizeInput($request['cantidadEsencia']),
                'id_insumo_formula'     => utils::sanitizeInput($request['insumo']),
                'id_tipo_concentracion' => utils::sanitizeInput($request['concentracion']),
            ];

            $result = $this->servicio->registrarFormulas($data);
            return response::success($result, 'Fórmula registrada exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar fórmula", $e, $_REQUEST);
            return response::error('Error al registrar la fórmula');
        }
    }

    public function listarPresentaciones(array $request): array {
        try {
            $categoria = $request['categoria'] ?? null;

            if (!$categoria) {
                return response::error('Debe especificar la categoría');
            }

            $data = $this->servicio->obtenerPresentacionesPorCategoria($categoria);
            return response::success($data);

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al listar presentaciones", $e, $_REQUEST);
            return response::error('Error al obtener presentaciones');
        }
    }

    public function listar(array $request): array {
        try {
            $data = $this->servicio->obtenerFormulas();
            return response::success($data);

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al listar fórmulas", $e, $_REQUEST);
            return response::error('Error al obtener el listado de fórmulas');
        }
    }
}
