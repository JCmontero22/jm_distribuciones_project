<?php 

    require_once('../model/PresentacionProductoModel.php');
    require_once('../Infrastructure/FileStorageService.php');

    class PresentacionProductoService{
        
        private $modelo;
        private $storage;

        public function __construct(LocalFileStorage $storage, PresentacionProductoModel $modelo) {
            $this->storage = $storage;
            $this->modelo = $modelo;
        }

        public function registroPresentacionProducto(array $data, array $files){
            try {
                if (isset($files['imagenPresentacion']) && $files['imagenPresentacion']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen = $this->storage->subirImagen($files['imagenPresentacion']);
                    $data['imgPresentacion'] = $nombreImagen;
                } else {
                    $data['imgPresentacion'] = null;
                }

                return $this->modelo->registroPresentacionProducto($data);

            } catch (\Exception $e) {
                if (isset($nombreImagen)) {
                    $this->storage->eliminarImagen($nombreImagen);
                }
                throw $e;     
            
            }
        }
    }
    