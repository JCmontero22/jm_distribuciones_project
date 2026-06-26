<?php

require_once('../core/response.php');
require_once('../core/Logger.php');
require_once('../core/CustomExceptions.php');

class CatalogoController {
    private CatalogoService $servicio;

    public function __construct(CatalogoService $servicio) {
        $this->servicio = $servicio;
    }

    public function obtenerMarcas() {
        try {
            $data = $this->servicio->obtenerMarcas();
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener marcas", $e, $_REQUEST);
            return response::error('Error al obtener marcas');
        }
    }

    public function obtenerGeneros() {
        try {
            $data = $this->servicio->obtenerGeneros();
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener géneros", $e, $_REQUEST);
            return response::error('Error al obtener géneros');
        }
    }

    public function obtenerCategorias() {
        try {
            $data = $this->servicio->obtenerCategorias();
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener categorías", $e, $_REQUEST);
            return response::error('Error al obtener categorías');
        }
    }

    public function obtenerPresentaciones() {
        try {
            $data = $this->servicio->obtenerPresentaciones();
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener presentaciones", $e, $_REQUEST);
            return response::error('Error al obtener presentaciones');
        }
    }

    public function obtenerCatalogosCompletos() {
        try {
            $data = $this->servicio->obtenerCatalogosCompletos();
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener catálogos completos", $e, $_REQUEST);
            return response::error('Error al obtener catálogos completos');
        }
    }

    public function obtenerTiposProductos() {
        try {
            $data = $this->servicio->obtenerTiposProductos();
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener tipos de productos", $e, $_REQUEST);
            return response::error('Error al obtener tipos de productos');
        }
    }

    public function obtenerSedes() {
        try {
            $data = $this->servicio->obtenerSedes();
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener sedes", $e, $_REQUEST);
            return response::error('Error al obtener sedes');
        }
    }

    public function obtenerInsumosFormulas() {
        try {
            $data = $this->servicio->obtenerInsumosFormulas();
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener insumos para fórmulas", $e, $_REQUEST);
            return response::error('Error al obtener insumos para fórmulas');
        }
    }

    public function registrarMarca(string $data, array $files) {
        try {
            $resultado = $this->servicio->registrarMarca($data, $files);
            return response::success($resultado, 'Marca registrada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar marca", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al registrar marca');
        }
    }

    public function obtenerMarcaPorID(int $idMarca): array {
        try {
            $data = $this->servicio->obtenerMarcaPorID($idMarca);
            return response::success($data);
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener marca por ID", $e, ['idMarca' => $idMarca, 'request' => $_REQUEST]);
            return response::error('Error al obtener marca por ID');
        }
    }

    public function actualizarMarca(int $idMarca, string $nombreMarca, array $files) {
        try {
            $resultado = $this->servicio->actualizarMarca($idMarca, $nombreMarca, $files);
            return response::success($resultado, 'Marca actualizada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar marca", $e, ['idMarca' => $idMarca, 'request' => $_REQUEST]);
            return response::error('Error al actualizar marca');
        }
    }

    public function eliminarMarca(int $idMarca) {
        try {
            $resultado = $this->servicio->eliminarMarca($idMarca);
            return response::success($resultado, 'Marca eliminada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al eliminar marca", $e, ['idMarca' => $idMarca, 'request' => $_REQUEST]);
            return response::error('Error al eliminar marca');
        }
    }

    public function registrarCategoria(string $nombreCategoria) {
        try {
            $resultado = $this->servicio->registrarCategoria($nombreCategoria);
            return response::success($resultado, 'Categoría registrada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar categoría", $e, ['nombreCategoria' => $nombreCategoria, 'request' => $_REQUEST]);
            return response::error('Error al registrar categoría');
        }
    }

    public function actualizarCategoria(int $idCategoria, string $nombreCategoria) {
        try {
            $resultado = $this->servicio->actualizarCategoria($idCategoria, $nombreCategoria);
            return response::success($resultado, 'Categoría actualizada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar categoría", $e, ['idCategoria' => $idCategoria, 'nombreCategoria' => $nombreCategoria, 'request' => $_REQUEST]);
            return response::error('Error al actualizar categoría');
        }
    }

    public function eliminarCategoria(int $idCategoria) {
        try {
            $resultado = $this->servicio->eliminarCategoria($idCategoria);
            return response::success($resultado, 'Categoría eliminada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al eliminar categoría", $e, ['idCategoria' => $idCategoria, 'request' => $_REQUEST]);
            return response::error('Error al eliminar categoría');
        }
    }

    public function registrarTipoProducto(string $nombreTipoProducto) {
        try {
            $resultado = $this->servicio->registrarTipoProducto($nombreTipoProducto);
            return response::success($resultado, 'Tipo de producto registrado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar tipo de producto", $e, ['nombreTipoProducto' => $nombreTipoProducto, 'request' => $_REQUEST]);
            return response::error('Error al registrar tipo de producto');
        }
    }

    public function actualizarTipoProducto(int $idTipoProducto, string $nombreTipoProducto) {
        try {
            $resultado = $this->servicio->actualizarTipoProducto($idTipoProducto, $nombreTipoProducto);
            return response::success($resultado, 'Tipo de producto actualizado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar tipo de producto", $e, ['idTipoProducto' => $idTipoProducto, 'nombreTipoProducto' => $nombreTipoProducto, 'request' => $_REQUEST]);
            return response::error('Error al actualizar tipo de producto');
        }
    }

    public function eliminarTipoProducto(int $idTipoProducto) {
        try {
            $resultado = $this->servicio->eliminarTipoProducto($idTipoProducto);
            return response::success($resultado, 'Tipo de producto eliminado exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al eliminar tipo de producto", $e, ['idTipoProducto' => $idTipoProducto, 'request' => $_REQUEST]);
            return response::error('Error al eliminar tipo de producto');
        }
    }

    public function registrarSede(array $data) {
        try {

            if (!utils::validateRequiredFields(['nombreSede', 'direccionSede', 'responsableSede'], $data)) {
                return response::error('Todos los campos son obligatorios');
            }

            $data['nombreSede'] = trim($data['nombreSede']);
            $data['direccionSede'] = trim($data['direccionSede']);
            $data['responsableSede'] = trim($data['responsableSede']);  
            $data['telefonoSede'] = trim($data['telefonoSede']) ?? ''; // Optional field, default to empty string if not provided

            $resultado = $this->servicio->registrarSede($data);
            return response::success($resultado, 'Sede registrada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar sede", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al registrar sede');
        }
    }

    public function actualizarSede(array $data) {
        try {

            if (!utils::validateRequiredFields(['idSede', 'nombreSede', 'direccionSede', 'responsableSede'], $data)) {
                return response::error('Todos los campos son obligatorios');
            }

            $data['idSede'] = (int)$data['idSede'];
            $data['nombreSede'] = trim($data['nombreSede']);
            $data['direccionSede'] = trim($data['direccionSede']);
            $data['responsableSede'] = trim($data['responsableSede']);  
            $data['telefonoSede'] = trim($data['telefonoSede']) ?? ''; // Optional field, default to empty string if not provided

            $resultado = $this->servicio->actualizarSede($data);
            return response::success($resultado, 'Sede actualizada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar sede", $e, ['data' => $data, 'request' => $_REQUEST]);
            return response::error('Error al actualizar sede');
        }
    }

    public function eliminarSede(int $idSede) {
        try {
            $resultado = $this->servicio->eliminarSede($idSede);
            return response::success($resultado, 'Sede eliminada exitosamente');
        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al eliminar sede", $e, ['idSede' => $idSede, 'request' => $_REQUEST]);
            return response::error('Error al eliminar sede');
        }
    }
}
