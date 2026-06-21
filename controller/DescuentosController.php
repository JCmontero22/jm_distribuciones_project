<?php

require_once(__DIR__ . '/../services/DescuentosService.php');
require_once(__DIR__ . '/../core/response.php');
require_once(__DIR__ . '/../core/Logger.php');
require_once(__DIR__ . '/../helper/utils.php');

class DescuentosController {
    private DescuentosService $descuentosService;

    public function __construct(DescuentosService $descuentosService) {
        $this->descuentosService = $descuentosService;
    }

    public function registrarDescuento(array $data): array {
        try {
            if (!utils::validateRequiredFields(['nombreDescuento', 'porcentajeDescuento', 'fechaInicio', 'fechaFin'], $data)) {
                return response::error('Todos los campos son obligatorios');
            }

            $idDescuento = $this->descuentosService->registrarDescuento($data);
            return response::success(['id_descuento' => $idDescuento], 'Descuento registrado exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar descuento", $e, $data);
            return response::error('Error al registrar el descuento');
        }
    }

    public function obtenerDescuentos(): array {
        try {
            $descuentos = $this->descuentosService->obtenerDescuentos();
            return response::success($descuentos, 'Descuentos obtenidos exitosamente');

        } catch (\Exception $e) {
            Logger::error("Error al obtener descuentos", $e, []);
            return response::error('Error al obtener los descuentos');
        }
    }

    public function obtenerDescuentoPorID(array $request): array {
        try {
            $idDescuento = (int)($request['idDescuento'] ?? 0);
            if (!$idDescuento) {
                return response::error('ID de descuento requerido');
            }

            $descuento = $this->descuentosService->obtenerDescuentoPorID($idDescuento);
            if (!$descuento) {
                return response::error('Descuento no encontrado');
            }

            return response::success($descuento, 'Descuento obtenido exitosamente');

        } catch (\Exception $e) {
            Logger::error("Error al obtener descuento por ID", $e, $request);
            return response::error('Error al obtener el descuento');
        }
    }

    public function actualizarDescuento(array $data): array {
        $idDescuento = (int)($data['id_descuento'] ?? 0);
        if (!$idDescuento) {
            return response::error('ID de descuento requerido');
        }
        try {
            if (!utils::validateRequiredFields(['nombreDescuento', 'porcentajeDescuento', 'fechaInicio', 'fechaFin'], $data)) {
                return response::error('Todos los campos son obligatorios');
            }
            $this->descuentosService->actualizarDescuento($idDescuento, $data);
            return response::success([], 'Descuento actualizado exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar descuento", $e, ['idDescuento' => $idDescuento, 'data' => $data]);
            return response::error('Error al actualizar el descuento');
        }
    }   

    public function aplicarDescuentoAProductosEspecificos(array $data): array {
        try {
            if (empty($data['idDescuento']) || empty($data['idsProductos'])) {
                return response::error('ID de descuento e IDs de productos requeridos');
            }

            $idDescuento = (int)$data['idDescuento'];
            $idsProductos = is_array($data['idsProductos']) ? $data['idsProductos'] : json_decode($data['idsProductos'], true);
            $idsProductos = array_map('intval', $idsProductos);

            $this->descuentosService->aplicarDescuentoAProductosEspecificos($idDescuento, $idsProductos);
            return response::success([], 'Descuento aplicado a productos exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al aplicar descuento a productos específicos", $e, $data);
            return response::error('Error al aplicar el descuento');
        }
    }

    public function aplicarDescuentoAMarca(array $data): array {
        try {
            if (empty($data['idDescuento']) || empty($data['idMarca'])) {
                return response::error('ID de descuento e ID de marca requeridos');
            }

            $idDescuento = (int)$data['idDescuento'];
            $idMarca = (int)$data['idMarca'];

            $this->descuentosService->aplicarDescuentoAMarca($idDescuento, $idMarca);
            return response::success([], 'Descuento aplicado a marca exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al aplicar descuento a marca", $e, $data);
            return response::error('Error al aplicar el descuento');
        }
    }

    public function aplicarDescuentoAProductoPorGenero(array $data): array {
        try {
            if (empty($data['idDescuento']) || empty($data['idProducto']) || empty($data['idGenero'])) {
                return response::error('ID de descuento, ID de producto e ID de género requeridos');
            }

            $idDescuento = (int)$data['idDescuento'];
            $idProducto = (int)$data['idProducto'];
            $idGenero = (int)$data['idGenero'];

            $this->descuentosService->aplicarDescuentoAProductoPorGenero($idDescuento, $idProducto, $idGenero);
            return response::success([], 'Descuento aplicado a género del producto exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al aplicar descuento a producto por género", $e, $data);
            return response::error('Error al aplicar el descuento');
        }
    }

    public function aplicarDescuentoAGenero(array $data): array {
        try {
            if (empty($data['idDescuento']) || empty($data['idGenero'])) {
                return response::error('ID de descuento e ID de género requeridos');
            }

            $idDescuento = (int)$data['idDescuento'];
            $idGenero = (int)$data['idGenero'];

            $this->descuentosService->aplicarDescuentoAGenero($idDescuento, $idGenero);
            return response::success([], 'Descuento aplicado a género exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al aplicar descuento a género", $e, $data);
            return response::error('Error al aplicar el descuento');
        }
    }

    public function aplicarDescuentoATodos(array $data): array {
        try {
            if (empty($data['idDescuento'])) {
                return response::error('ID de descuento requerido');
            }

            $idDescuento = (int)$data['idDescuento'];

            $this->descuentosService->aplicarDescuentoATodos($idDescuento);
            return response::success([], 'Descuento aplicado a todos los productos exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al aplicar descuento a todos los productos", $e, $data);
            return response::error('Error al aplicar el descuento');
        }
    }

    public function removerDescuento(array $data): array {
        try {
            if (empty($data['idDescuento'])) {
                return response::error('ID de descuento requerido');
            }

            $idDescuento = (int)$data['idDescuento'];

            $this->descuentosService->removerDescuento($idDescuento);
            return response::success([], 'Descuento removido exitosamente');

        } catch (\Exception $e) {
            Logger::error("Error al remover descuento", $e, $data);
            return response::error('Error al remover el descuento');
        }
    }

    public function eliminarDescuento(array $data): array {
        try {
            if (empty($data['idDescuento'])) {
                return response::error('ID de descuento requerido');
            }

            $idDescuento = (int)$data['idDescuento'];

            $this->descuentosService->eliminarDescuento($idDescuento);
            return response::success([], 'Descuento eliminado exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al eliminar descuento", $e, $data);
            return response::error('Error al eliminar el descuento');
        }
    }
}
