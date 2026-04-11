<?php 

    require_once('../services/PresentacionProductoService.php');
    require_once('../core/response.php');
    require_once('../helper/utils.php');
    require_once('../core/Logger.php');
    
    class PresentacionProductoController {
        
        private $servicio;

        public function __construct(PresentacionProductoService $servicio) {
            $this->servicio = $servicio;
        }

        public function registroPresentacionProducto(array $request) {
            try {

                 if (!utils::validateRequiredFields(['nombrePresentacion', 'codigoPresentacion', 'tipoProducto'], $request)) {
                    return response::error('Todos los campos son obligatorios');
                }             
                
                $params = [
                    'idProducto' => $request['idProducto'],
                    'nombre'    => utils::sanitizeInput($request['nombrePresentacion']),
                    'codigo'    => utils::sanitizeInput($request['codigoPresentacion']),
                    'tipo'      => utils::sanitizeInput($request['tipoProducto']),
                ];
                    

                $response = $this->servicio->registroPresentacionProducto($params, $_FILES);
                
                if (empty($response)) {
                    return response::error('No se pudo registrar la presentación del producto');
                }    
                return response::success($response, 'Presentación del producto registrada exitosamente');
                
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Presentación de Producto", $e);
                return response::error('Error al registrar la presentación del producto');
            }
            
        }
    }
    