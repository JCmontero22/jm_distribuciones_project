<?php

    require_once('../contracts/IStorageService.php');
    require_once('../contracts/IProductoRepositorio.php');
    require_once('../Infrastructure/FileStorageService.php');
    require_once('../model/ProductosModel.php');

    class ProductoService {
        private $modelo;
        private $storage;

        public function __construct(IStorageService $storage, IProductoRepositorio $modelo) {
            $this->modelo = $modelo;
            $this->storage = $storage;
        }

        public function registrarProducto(array $data, array $files) {
            $nombreImagen = null;

            try {
                if ($this->modelo->existeProducto($data['codigo'])) {
                    throw new DomainException('El producto ya existe');
                }

                if (isset($files['imagen']) && $files['imagen']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen = $this->storage->subirImagen($files['imagen']);
                    $data['imagen'] = $nombreImagen;
                }

                return $this->modelo->registrarProducto($data);

            } catch (\Exception $e) {
                if ($nombreImagen !== null) {
                    $this->storage->eliminarImagen($nombreImagen);
                }

                throw $e;
            }
        }

        public function obtenerProductos(string $categoria) : array {
            return $this->modelo->obtenerPorCategoria($categoria);
        }
    }

