<?php

    require_once('../core/response.php');
    require_once('../core/Logger.php');

    class CatalogoController {
        private $servicio;

        public function __construct(CatalogoService $servicio) {
            $this->servicio = $servicio;
        }

        public function obtenerMarcas() {
            try {
                $data = $this->servicio->obtenerMarcas();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error al obtener marcas", $e);
                return response::error('Error al obtener marcas');
            }
        }

        public function obtenerGeneros() {
            try {
                $data = $this->servicio->obtenerGeneros();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error al obtener géneros", $e);
                return response::error('Error al obtener géneros');
            }
        }

        public function obtenerCategorias() {
            try {
                $data = $this->servicio->obtenerCategorias();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error al obtener categorías", $e);
                return response::error('Error al obtener categorías');
            }
        }

        public function obtenerPresentaciones() {
            try {
                $data = $this->servicio->obtenerPresentaciones();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error al obtener presentaciones", $e);
                return response::error('Error al obtener presentaciones');
            }
        }

        public function obtenerCatalogosCompletos() {
            try {
                $data = $this->servicio->obtenerCatalogosCompletos();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error al obtener catálogos completos", $e);
                return response::error('Error al obtener catálogos completos');
            }
        }

        public function obtenerTiposProductos() {
            try {
                $data = $this->servicio->obtenerTiposProductos();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error al obtener tipos de productos", $e);
                return response::error('Error al obtener tipos de productos');
            }
        }

        public function obtenerSedes() {
            try {
                $data = $this->servicio->obtenerSedes();
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error al obtener sedes", $e);
                return response::error('Error al obtener sedes');
            }
        }
    }
