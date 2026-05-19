<?php

    require_once('../core/response.php');
    require_once('../helper/utils.php');
    require_once('../core/Logger.php');
    require_once('../services/FormulasService.php');

    class FormulasController
    {
        private FormulasService $servicio;

        public function __construct(FormulasService $servicio)
        {
            $this->servicio = $servicio;
        }

        public function registrar(array $request): array {
            try {
                $nombre = $request['nombreFormula'] ?? null;
                $cantidadEsencia = $request['cantidadEsencia'] ?? null;
                $insumo = $request['insumo'] ?? null;

                if (!utils::validateRequiredFields(['nombreFormula', 'cantidadEsencia', 'insumo'], $request)) {
                    return response::error('Todos los campos son obligatorios');
                }

                $data = [
                    'nombre_formula' => utils::sanitizeInput($nombre),
                    'cantidad_esencia' => utils::sanitizeInput($cantidadEsencia),       
                    'id_presentacion_insumo' => utils::sanitizeInput($insumo),
                ];

                $result = $this->servicio->registrarFormulas($data);
                return response::success($result, 'Fórmula registrada exitosamente');

            } catch (\DomainException $e) {
                return response::error($e->getMessage());
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Registro de Fórmulas", $e, $request);
                return response::error('Error al registrar la fórmula: ' . $e->getMessage());
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

            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Presentaciones", $e, $request);
                return response::error('Error al obtener presentaciones');
            }
        }

        /* public function registrarPresentaciones(array $presentaciones, array $files = []): array {
            try {
                
                if (empty($presentaciones)) {
                    return response::error('No se han proporcionado presentaciones para registrar');
                }

                $data = $this->servicio->registrarFormula($presentaciones, $files);
                return response::success($data, 'Presentaciones registradas exitosamente');

            } catch (\DomainException $e) {
                return response::error($e->getMessage());
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Registro de Presentaciones", $e, $presentaciones);
                return response::error('Error al registrar las presentaciones: ' . $e->getMessage());
            }
        } */

        public function listar(array $request): array {
            try {
                
                $data = $this->servicio->obtenerFormulas();
                return response::success($data);

            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Fórmulas", $e, $request);
                return response::error('Error al obtener el listado de fórmulas');
            }
        }
    }

