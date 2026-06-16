<?php

    require_once('../Infrastructure/FileStorageService.php');
    require_once('../model/FormulasModel.php');
    require_once('../core/Logger.php');

    class FormulasService {
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
            $nombre          = $data['nombre_formula'] ?? null;
            $cantidadEsencia = $data['cantidad_esencia'] ?? null;
            $insumo          = $data['id_insumo_formula'] ?? null;
            $concentracion   = $data['id_tipo_concentracion'] ?? null;

            if (!$nombre || !$cantidadEsencia || !$insumo || !$concentracion) {
                throw new DomainException('Datos de fórmula incompletos');
            }

            $params = [
                'nombre_formula'        => $nombre,
                'id_insumo_formula'     => $insumo,
                'id_tipo_concentracion' => $concentracion,
                'cantidad_esencia'      => $cantidadEsencia,
            ];
            $insertados[] = $this->modelo->registrarFormula($params);
            

            return [
                'nombre_formula' => $nombre,
                'insumos_registrados' => count($insertados)
            ];
        }

        public function obtenerFormulaPorId(int $id): array {
            return $this->modelo->obtenerFormulaPorId($id);
        }

        public function actualizarFormula(array $data): bool {
            $idFormula       = $data['id_formula'] ?? null;
            $nombre          = $data['nombreFormula'] ?? null;
            $cantidadEsencia = $data['cantidadEsencia'] ?? null;
            $insumo          = $data['insumo'] ?? null;
            $concentracion   = $data['concentracion'] ?? null;

            if (!$idFormula || !$nombre || !$cantidadEsencia || !$insumo || !$concentracion) {
                throw new DomainException('Datos de fórmula incompletos');
            }

            $params = [
                'id_formula'             => $idFormula,
                'nombre_formula'         => $nombre,
                'cantidad_esencia'       => $cantidadEsencia,
                'id_insumo_formula'      => $insumo,
                'id_tipo_concentracion'  => $concentracion,
            ];

            return $this->modelo->actualizarFormula($params);
        }

        public function eliminarFormula(int $idFormula): bool {
            if (!$idFormula) {
                throw new DomainException('ID de fórmula es requerido');
            }
            return $this->modelo->eliminarFormula($idFormula);
        }
    }

