<?php 

    require_once('../services/CategoriaServices.php');
    require_once('../core/response.php');
    require_once('../helper/utils.php');
    require_once('../core/Logger.php');
    
    class CategoriaController {
        
        private $servicio;

        public function __construct(CategoriaServices $servicio) {
            $this->servicio = $servicio;
        }

        public function listaCategorias() {
            try {
                $response = $this->servicio->listaCategorias();
                
                if (empty($response)) {
                    return response::error('No se encontraron categorías');
                }    
                return response::success($response, 'Categorías obtenidas exitosamente');
                
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Categorías", $e);
                return response::error('Error al obtener las categorías');
            }
            
        }
    }
    