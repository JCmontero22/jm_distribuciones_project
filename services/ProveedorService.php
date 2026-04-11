<?php 

    require_once('../model/ProveedorModel.php');

    class ProveedorService{
        private $proveedorModel;

        public function __construct(ProveedorModel $proveedorModel) {
            $this->proveedorModel = $proveedorModel;
        }

        public function obtenerProveedores() :array{
            return $this->proveedorModel->proveedores();
        }
    }
    