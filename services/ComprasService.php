<?php 

    require_once('../model/ComprasModel.php');
    require_once('../core/Logger.php');

    class ComprasService {

        private ComprasModel $model;

        public function __construct(ComprasModel $model) {
            $this->model = $model;
        }

        public function registrarCompra(array $data): mixed {
            $idCompra = $this->model->registrarCompra($data);
            return [
                'id_compras' => $idCompra,
            ];
        }

        public function registrarDetalleCompra(int $idCompra, array $detalle): mixed {
            $this->model->registrarDetalleCompra($idCompra, $detalle);
            return true;
        }

        public function obtenerCompras(): array {
            return $this->model->obtenerCompras();
        }

    }