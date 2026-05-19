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

                if (isset($files['imagen']) && $files['imagen']['error'] === UPLOAD_ERR_OK) {
                    $nombreImagen = $this->storage->subirImagen($files['imagen']);
                    $data['imagen'] = $nombreImagen;
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
    }

