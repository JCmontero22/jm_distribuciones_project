<?php 

    require_once('../model/ProductoModel.php');
    require_once('../Infrastructure/FileStorageService.php');

    class productoService {
        private $modelo;
        private $storage;

        public function __construct(LocalFileStorage $storage, ProductoModel $modelo) {
            $this->modelo = $modelo;
            $this->storage = $storage;
        }

        public function registrarProducto(array $data, array $files) {

            try {
                if ($this->modelo->existeProducto($data['codigo'])) {
                    throw new DomainException('El producto ya existe');
                }

                
                if (isset($files['imagenProducto']) && $files['imagenProducto']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen = $this->storage->subirImagen($files['imagenProducto']);
                    $data['imagen'] = $nombreImagen;
                }

                return $this->modelo->registoProducto($data);

            } catch (\Exception $e) {
                if ($nombreImagen !== null) {
                    $this->storage->eliminarImagen($nombreImagen);
                }

                throw $e;
            }
        }
    }
    