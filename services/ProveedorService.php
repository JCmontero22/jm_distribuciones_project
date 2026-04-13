<?php 

    require_once('../model/ProveedorModel.php');

    class ProveedorService{
        private $proveedorModel;

        public function __construct(ProveedorModel $proveedorModel) {
            $this->proveedorModel = $proveedorModel;
        }

        public function obtenerProveedoresSelect() :array{
            return $this->proveedorModel->obtenerProveedoresSelect();
        }

        public function registrarProveedor($data) {
            return $this->proveedorModel->registrarProveedor($data);
        }
        
    }
    