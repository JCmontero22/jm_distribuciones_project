<?php 

    require_once('../model/ProveedorModel.php');

    class ProveedorService{
        private ProveedorModel $proveedorModel;

        public function __construct(ProveedorModel $proveedorModel) {
            $this->proveedorModel = $proveedorModel;
        }

        public function obtenerProveedoresSelect(): array {
            return $this->proveedorModel->obtenerProveedoresSelect();
        }

        public function registrarProveedor(array $data): mixed {
            return $this->proveedorModel->registrarProveedor($data);
        }

    }
    