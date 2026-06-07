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
}
