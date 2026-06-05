<?php

    require_once('../Infrastructure/FileStorageService.php');
    require_once('../model/ProductosModel.php');
    require_once('../core/Logger.php');

    class ProductoService {
        private ProductosModel $modelo;
        private LocalFileStorage $storage;

        public function __construct(LocalFileStorage $storage, ProductosModel $modelo) {
            $this->modelo = $modelo;
            $this->storage = $storage;
        }

        public function registrarProducto(array $data, array $files): mixed {
            $nombreImagen = null;

            try {
                if ($this->modelo->existeProducto($data['codigo'])) {
                    throw new DomainException('El producto ya existe');
                }

                if (isset($files['logoProducto']) && $files['logoProducto']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen = $this->storage->subirImagen($files['logoProducto']);
                    $data['logoProducto'] = $nombreImagen;
                }

                $idProducto = $this->modelo->registrarProducto($data);

                return [
                    'id_producto' => $idProducto,
                    'nombre' => $data['nombre'],
                    'codigo' => $data['codigo']
                ];

            } catch (\Exception $e) {
                if ($nombreImagen !== null) {
                    $this->storage->eliminarImagen($nombreImagen);
                }

                throw $e;
            }
        }

        public function registrarPresentaciones(array $presentaciones, array $files = []): array {

            if (empty($presentaciones)) {
                throw new DomainException('No se han proporcionado presentaciones para registrar');
            }

            $resultados = [];
            foreach ($presentaciones as $idx => $presentacion) {
                $nombreImagen = null;

                try {
                    $nombreCampo = "imagen_{$idx}";
                    // Buscar la imagen en $_FILES
                    if (isset($files[$nombreCampo]) && $files[$nombreCampo]['error'] === UPLOAD_ERR_OK) {
                        $nombreImagen = $this->storage->subirImagen($files[$nombreCampo]);
                        $presentacion['imgPresentacion'] = $nombreImagen;
                    } else {
                        $presentacion['imgPresentacion'] = null;
                    }

                    // Guardar la presentación en la BD
                    $resultado = $this->modelo->registroPresentacionProducto($presentacion);
                    $resultados[] = $resultado;

                } catch (\Exception $e) {
                    if ($nombreImagen !== null) {
                        $this->storage->eliminarImagen($nombreImagen);
                    }
                    throw $e;
                }
            }

            return $resultados;
        }

        public function obtenerProductos(string $categoria): array {
            return $this->modelo->obtenerProductos($categoria);
        }

        public function productosCompra(): array {
            return $this->modelo->obtenerProductosCompra();
        }

        public function obtenerEsencias(): array {
            return $this->modelo->obtenerEsencias();
        }

        public function obtenerPresentacionesPorProducto(int $idProducto): array {
            return $this->modelo->obtenerPresentacionesPorProducto($idProducto);
        }

        public function obtenerProductoPorId(int $id): array {
            return $this->modelo->obtenerProductoPorId($id);
        }

        public function editarProducto(int $id, array $data): void {
           /*  $params = [
                'nombre'      => utils::sanitizeInput($data['nombre']),
                'codigo'      => utils::sanitizeInput($data['codigo']),
                'categoria'   => $data['categoria'],
                'marca'       => $data['marca'],
                'descripcion' => utils::sanitizeInput($data['descripcion'] ?? ''),
                'favoritoProducto' => isset($data['favoritoProducto']) ? 1 : 0
            ]; */
            $this->modelo->editarProducto($id, $data);
        }

        public function eliminarProducto(int $id): void {
            $this->modelo->eliminarProducto($id);
        }

        public function eliminarPresentacion(int $id): void {
            $this->modelo->eliminarPresentacion($id);
        }
    }

