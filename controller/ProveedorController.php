<?php

    require_once('../services/ProveedorService.php');
    require_once('../core/response.php');
    require_once('../core/Logger.php');
    require_once('../helper/utils.php');
    
    class ProveedorController {
        private $proveedorService;

        public function __construct(ProveedorService $proveedorService) {
            $this->proveedorService = $proveedorService;
        }

        public function obtenerProveedoresSelect(){
            try {
                $data = $this->proveedorService->obtenerProveedoresSelect();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Proveedores", $e);
                return response::error('Error al obtener el listado de proveedores');
            }
        }

        public function obtenerProveedoresTabla() {
            try {
                $respuesta = $this->proveedorService->obtenerProveedoresSelect(); // Reutilizamos el mismo método
                $data = [];
                foreach ($respuesta as $proveedor) {
                    $data[] = [
                        'id_proveedor' => $proveedor['id_proveedor'],
                        'nombre_proveedor' => $proveedor['nombre_proveedor'],
                        'contacto_proveedor' => $proveedor['contacto_proveedor'],
                        'telefono_proveedor' => $proveedor['telefono_proveedor']
                    ];
                }
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Proveedores para Tabla", $e);
                return response::error('Error al obtener el listado de proveedores para la tabla');
            }
        }

        public function registrarProveedor($data) {
            try {
                if (!utils::validateRequiredFields(['nombre', 'contacto', 'telefono'], $data)) {
                    return response::error('Todos los campos son obligatorios');
                }    
                
                $nuevoProveedor = [
                    'nombre' => strtoupper($data['nombre']),
                    'contacto' => strtoupper($data['contacto']),
                    'telefono' => $data['telefono']
                ];

                $resultado = $this->proveedorService->registrarProveedor($nuevoProveedor);
                return response::success($resultado, 'Proveedor registrado exitosamente');
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Registro de Proveedores", $e);
                return response::error('Error al registrar el proveedor');
            }
            
        }
    }
    

    