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
                    'genero'    => $request['generoProducto'] ?? null,
                    'descripcion' => utils::sanitizeInput($request['descripcionProducto'] ?? null),
                    'imagen'    => ""
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
    }

