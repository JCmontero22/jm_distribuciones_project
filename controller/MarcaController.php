<?php 

    require_once('../services/MarcaServicio.php');
    require_once('../core/response.php');
    require_once('../helper/utils.php');
    require_once('../core/Logger.php');

    class MarcaController{
    
        private $servicio;
        
        public function __construct(MarcaService $servicio) {
            $this->servicio = $servicio;
        }

        public function listadoMarcas(){
            try {
                $response = $this->servicio->listadoMarcas();
                
                if (empty($response)) {
                    return response::error('No se encontraron marcas');
                }    
                return response::success($response, 'Marcas obtenidas exitosamente');
                
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Marcas", $e);
                return response::error('Error al obtener las marcas');
            }
            
        }

    }
    