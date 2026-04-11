<?php 

    require_once('../services/CategoriaService.php');
    require_once('../core/response.php');
    require_once('../helper/utils.php');
    require_once('../core/Logger.php');
    
    class GeneroController {
        
        private $servicio;

        public function __construct(GeneroService $servicio) {
            $this->servicio = $servicio;
        }

        public function listaGeneros() {
            try {
                $response = $this->servicio->listaGeneros();
                
                if (empty($response)) {
                    return response::error('No se encontraron géneros');
                }    
                return response::success($response, 'Géneros obtenidos exitosamente');
                
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Géneros", $e);
                return response::error('Error al obtener los géneros');
            }
            
        }
    }
    