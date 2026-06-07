<?php

require_once('../services/ProveedorService.php');
require_once('../core/response.php');
require_once('../core/Logger.php');
require_once('../core/CustomExceptions.php');
require_once('../helper/utils.php');

class ProveedorController {
    private ProveedorService $proveedorService;

    public function __construct(ProveedorService $proveedorService) {
        $this->proveedorService = $proveedorService;
    }

    public function obtenerProveedoresSelect(): array {
        try {
            $data = $this->proveedorService->obtenerProveedoresSelect();
            return response::success($data);

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al listar proveedores", $e, $_REQUEST);
            return response::error('Error al obtener el listado de proveedores');
        }
    }

    public function obtenerProveedoresTabla(): array {
        try {
            $respuesta = $this->proveedorService->obtenerProveedoresSelect();
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

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al listar proveedores para tabla", $e, $_REQUEST);
            return response::error('Error al obtener el listado de proveedores');
        }
    }

    public function registrarProveedor(array $data): array {
        try {
            if (!utils::validateRequiredFields(['nombre', 'contacto', 'telefono'], $data)) {
                return response::error('Todos los campos son obligatorios');
            }

            $nuevoProveedor = [
                'nombre' => strtoupper(utils::sanitizeInput($data['nombre'])),
                'contacto' => strtoupper(utils::sanitizeInput($data['contacto'])),
                'telefono' => utils::sanitizeInput($data['telefono'])
            ];

            $resultado = $this->proveedorService->registrarProveedor($nuevoProveedor);
            return response::success($resultado, 'Proveedor registrado exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar proveedor", $e, $_REQUEST);
            return response::error('Error al registrar el proveedor');
        }
    }
}
