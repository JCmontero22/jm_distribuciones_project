<?php

    require_once('../services/ProveedorService.php');
    require_once('../core/response.php');
    require_once('../core/Logger.php');
    
    class ProveedorController {
        private $proveedorService;

        public function __construct(ProveedorService $proveedorService) {
            $this->proveedorService = $proveedorService;
        }

        public function obtenerProveedores(){
            try {
                $data = $this->proveedorService->obtenerProveedores();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Proveedores", $e);
                return response::error('Error al obtener el listado de proveedores');
            }
        }
    }
    

    