<?php

    require_once('../core/response.php');
    require_once('../helper/utils.php');
    require_once('../core/Logger.php');
    require_once('../services/ProductoService.php');

    class ProductoController
    {
        private ProductoService $servicio;

        public function __construct(ProductoService $servicio)
        {
            $this->servicio = $servicio;
        }

        public function registrar(array $request): array {

            try {

                if (!utils::validateRequiredFields(['nombreProducto', 'codigoProducto', 'categoriaProducto', 'marcaProducto'], $request)) {
                    return response::error('Todos los campos son obligatorios');
                }

                $params = [
                    'nombre'    => utils::sanitizeInput($request['nombreProducto']),
                    'codigo'    => utils::sanitizeInput($request['codigoProducto']),
                    'categoria' => $request['categoriaProducto'] ?? null,
                    'marca'     => $request['marcaProducto'] ?? null,
                    'descripcion' => utils::sanitizeInput($request['descripcionProducto'] ?? null),
                ];

                $data = $this->servicio->registrarProducto($params, $_FILES);
                return response::success($data, 'Producto registrado exitosamente');

            } catch (\DomainException $e) {
                return response::error($e->getMessage());
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Registro de Producto", $e, $request);
                return response::error('Error al registrar el producto');
            }
        }

        public function registrarPresentaciones(array $presentaciones, array $files = []): array {
            try {
                
                if (empty($presentaciones)) {
                    return response::error('No se han proporcionado presentaciones para registrar');
                }

                $data = $this->servicio->registrarPresentaciones($presentaciones, $files);
                return response::success($data, 'Presentaciones registradas exitosamente');

            } catch (\DomainException $e) {
                return response::error($e->getMessage());
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Registro de Presentaciones", $e, $presentaciones);
                return response::error('Error al registrar las presentaciones: ' . $e->getMessage());
            }
        }

        public function listar(array $request): array {
            try {
                $categoria = $request['categoria'] ?? null;

                if (!$categoria) {
                    return response::error('Debe especificar la categoría');
                }

                $data = $this->servicio->obtenerProductos($categoria);
                return response::success($data);

            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Productos", $e, $request);
                return response::error('Error al obtener el listado de productos');
            }
        }

        public function listarProductosCompra(array $request): array {
            try {
                $data = $this->servicio->productosCompra();
                return response::success($data, 'Productos para compra obtenidos exitosamente');

            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Productos para Compra", $e, $request);
                return response::error('Error al obtener el listado de productos para compra');
            }
        }

        public function listarEsencias(): array {
            try {
                $data = $this->servicio->obtenerEsencias();
                return response::success($data, 'Esencias obtenidas exitosamente');

            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Listado de Esencias", $e, []);
                return response::error('Error al obtener el listado de esencias');
            }
        }

        public function listarPresentacionesPorProducto(array $request): array {
            try {
                $idProducto = (int)($request['idProducto'] ?? 0);
                if (!$idProducto) return response::error('ID de producto requerido');
                $data = $this->servicio->obtenerPresentacionesPorProducto($idProducto);
                return response::success($data, 'Presentaciones obtenidas exitosamente');

            } catch (\Exception $e) {
                Logger::error("Error al listar presentaciones por producto", $e, $request);
                return response::error('Error al obtener las presentaciones');
            }
        }

        public function obtenerProducto(array $request): array {
            try {
                $id = (int)($request['idProducto'] ?? 0);
                if (!$id) return response::error('ID de producto requerido');
                $data = $this->servicio->obtenerProductoPorId($id);
                return response::success($data);
            } catch (\Exception $e) {
                Logger::error("Error al obtener producto", $e, $request);
                return response::error('Error al obtener el producto');
            }
        }

        public function editar(array $request): array {
            try {
                $id = (int)($request['idProductoEditar'] ?? 0);
                if (!$id) return response::error('ID de producto requerido');
                if (!utils::validateRequiredFields(['nombreProducto', 'codigoProducto', 'categoriaProducto', 'marcaProducto'], $request)) {
                    return response::error('Todos los campos son obligatorios');
                }
                $data = [
                    'nombre'      => $request['nombreProducto'],
                    'codigo'      => $request['codigoProducto'],
                    'categoria'   => $request['categoriaProducto'],
                    'marca'       => $request['marcaProducto'],
                    'descripcion' => $request['descripcionProducto'] ?? '',
                    'favoritoProducto' => isset($request['favoritoProducto']) ? 1 : 0
                ];
                $this->servicio->editarProducto($id, $data);
                return response::success([], 'Producto actualizado exitosamente');
            } catch (\Exception $e) {
                Logger::error("Error al editar producto", $e, $request);
                return response::error('Error al editar el producto');
            }
        }

        public function eliminar(array $request): array {
            try {
                $id = (int)($request['idProducto'] ?? 0);
                if (!$id) return response::error('ID de producto requerido');
                $this->servicio->eliminarProducto($id);
                return response::success([], 'Producto eliminado exitosamente');
            } catch (\Exception $e) {
                Logger::error("Error al eliminar producto", $e, $request);
                return response::error('Error al eliminar el producto');
            }
        }

        public function eliminarPresentacion(array $request): array {
            try {
                $id = (int)($request['idPresentacion'] ?? 0);
                if (!$id) return response::error('ID de presentación requerido');
                $this->servicio->eliminarPresentacion($id);
                return response::success([], 'Presentación eliminada exitosamente');
            } catch (\Exception $e) {
                Logger::error("Error al eliminar presentación", $e, $request);
                return response::error('Error al eliminar la presentación');
            }
        }
    }

