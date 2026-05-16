<?php

    require_once('../core/response.php');
    require_once('../helper/utils.php');
    require_once('../core/Logger.php');
    require_once('../services/FormulasService.php');

    class FormulaController
    {
        private FormulaService $servicio;

        public function __construct(FormulaService $servicio)
        {
            $this->servicio = $servicio;
        }

        public function registrar(array $request): array {
            try {
                $nombre = $request['nombreFormula'] ?? null;
                $idPresentacionFinal = $request['idPresentacionFinal'] ?? null;
                $insumosJson = $request['insumos'] ?? null;

                if (!utils::validateRequiredFields(['nombreFormula', 'idPresentacionFinal', 'insumos'], $request)) {
                    return response::error('Todos los campos son obligatorios');
                }

                $insumos = json_decode($insumosJson, true);
                if (!is_array($insumos) || empty($insumos)) {
                    return response::error('Debe agregar al menos un insumo');
                }

                $data = [
                    'nombre' => utils::sanitizeInput($nombre),
                    'id_presentacion_final' => (int)$idPresentacionFinal,
                    'insumos' => array_map(function($insumo) {
                        return [
                            'id_presentacion_insumo' => (int)$insumo['id_presentacion_insumo'],
                            'cantidad_requerida' => (float)$insumo['cantidad_requerida']
                        ];
                    }, $insumos)
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
                $categoria = $request['categoria'] ?? null;

                if (!$categoria) {
                    return response::error('Debe especificar la categoría');
                }

                $data = $this->servicio->obtenerFormulas();
                return response::success($data);

            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Fórmulas", $e, $request);
                return response::error('Error al obtener el listado de fórmulas');
            }
        }
    }

