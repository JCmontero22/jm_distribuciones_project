<?php 

    require_once(__DIR__ . '/../model/PromocionesModel.php');

    class PromocionesService {
        private PromocionesModel $promocionesModel;
        private LocalFileStorage $storage;


        public function __construct(PromocionesModel $promocionesModel, LocalFileStorage $storage) {
            $this->promocionesModel = $promocionesModel;
            $this->storage = $storage;
        }

        public function registroBanner(array $data, array $files): mixed {

            try{
                if(isset($files['imgBanner']) && $files['imgBanner']['error'] === UPLOAD_ERR_OK){
                    $nombreImagen = $this->storage->subirImagen($files['imgBanner']);
                    $data['imgBanner'] = $nombreImagen;
                } else {
                    throw new DomainException('Error al subir la imagen del banner');
                }

                return $this->promocionesModel->registrarBannerPromocion($data);

            }catch(\Exception $e){
                if (isset($data['imgBanner'])) {
                    $this->storage->eliminarImagen($data['imgBanner']);
                }
                throw $e;
            }
        }

        public function obtenerBanners(): array {
            return $this->promocionesModel->obtenerBanners();
        }
    }
    