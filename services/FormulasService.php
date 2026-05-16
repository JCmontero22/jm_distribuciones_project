<?php

    require_once('../Infrastructure/FileStorageService.php');
    require_once('../model/FormulasModel.php');
    require_once('../core/Logger.php');

    class FormulaService {
        private FormulasModel $modelo;
        
        public function __construct(FormulasModel $modelo) {
            $this->modelo = $modelo;
            
        }

        public function registrarFormula(array $data, array $files): mixed {
            $nombreImagen = null;

            try {
                if ($this->modelo->existeFormula($data['nombre'])) {
                    throw new DomainException('La fórmula ya existe');
                }


                $idFormula = $this->modelo->registrarFormula($data);

                return [
                    'id_formula' => $idFormula,
                    'nombre' => $data['nombre'],
                    'codigo' => $data['codigo']
                ];

            } catch (\Exception $e) {
                throw $e;
            }
        }

        public function obtenerFormulas(): array {
            return $this->modelo->obtenerFormulas();
        }

        public function obtenerPresentacionesPorCategoria(string $categoria): array {
            return $this->modelo->obtenerPresentacionesPorCategoria($categoria);
        }

        public function registrarFormulas(array $data): array {
            $nombre = $data['nombre'] ?? null;
            $idPresentacionFinal = $data['id_presentacion_final'] ?? null;
            $insumos = $data['insumos'] ?? [];

            if (!$nombre || !$idPresentacionFinal || empty($insumos)) {
                throw new DomainException('Datos de fórmula incompletos');
            }

            $insertados = [];
            foreach ($insumos as $insumo) {
                $params = [
                    'nombre' => $nombre,
                    'id_presentacion_final' => $idPresentacionFinal,
                    'id_presentacion_insumo' => $insumo['id_presentacion_insumo'],
                    'cantidad_requerida' => $insumo['cantidad_requerida'],
                ];
                $insertados[] = $this->modelo->registrarFormula($params);
            }

            return [
                'nombre_formula' => $nombre,
                'insumos_registrados' => count($insertados)
            ];
        }
    }

