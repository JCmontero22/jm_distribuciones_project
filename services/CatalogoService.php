<?php

    require_once('../model/MarcaModel.php');
    require_once('../model/GeneroModel.php');
    require_once('../model/CategoriaModel.php');
    require_once('../model/TiposProductosModel.php');
    require_once('../model/SedesModel.php');
    require_once('../model/InsumoFormulasModel.php');

    class CatalogoService {
        private $marcaModel;
        private $generoModel;
        private $categoriaModel;
        private $presentacionModel;
        private $tiposProductoModel;
        private $sedesModel;
        private $insumosFormulasModel;
        private LocalFileStorage $storage;

        public function __construct(
            MarcaModel $marcaModel,
            GeneroModel $generoModel,
            CategoriaModel $categoriaModel,
            TiposProductosModel $tiposProductoModel,
            SedesModel $sedesModel,
            InsumoFormulasModel $insumosFormulasModel,
            LocalFileStorage $storage
        ) {
            $this->marcaModel = $marcaModel;
            $this->generoModel = $generoModel;
            $this->categoriaModel = $categoriaModel;
            $this->insumosFormulasModel = $insumosFormulasModel;
            $this->tiposProductoModel = $tiposProductoModel;
            $this->sedesModel = $sedesModel;
            $this->storage = $storage;
        }

        // ✅ Logica de Marcas
        public function obtenerMarcas() : array {
            $marcas = $this->marcaModel->obtenerTodos();

            if (empty($marcas)) {
                return [];
            }

            return $marcas;
        }

        public function obtenerMarcaPorID(int $idMarca): ?array {
            return $this->marcaModel->obtenerMarcaPorID($idMarca);
        }

        public function validarMarca($idMarca) : bool {
            $marcas = $this->obtenerMarcas();
            return in_array($idMarca, array_column($marcas, 'id_marca'));
        }

        public function registrarMarca(string $nombreMarca, array $files) : bool {
            $nombreImagen = null;
            if (isset($files['imagenMarca']) && $files['imagenMarca']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = $this->storage->subirImagen($files['imagenMarca']);
            }

            return $this->marcaModel->registrarMarca($nombreMarca, $nombreImagen);
        }
        
        public function actualizarMarca(int $idMarca, string $nombreMarca, array $files) : bool {
            $marca = $this->obtenerMarcaPorID($idMarca);
            if (!$marca) {
                throw new DomainException('Marca no encontrada');
            }

            $nombreImagen = null;
            if (isset($files['imagenMarca']) && $files['imagenMarca']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = $this->storage->subirImagen($files['imagenMarca']);
                // Eliminar imagen anterior si existe
                if ($marca[0]['img_marca']) {
                    $this->storage->eliminarImagen($marca[0]['img_marca']);
                }
            }

            return $this->marcaModel->actualizarMarca($idMarca, $nombreMarca, $nombreImagen ?? $marca[0]['img_marca']);
        }

         public function eliminarMarca(int $idMarca) : bool {
            $marca = $this->obtenerMarcaPorID($idMarca);
            if (!$marca) {
                throw new DomainException('Marca no encontrada');
            }

            // Eliminar imagen si existe
            if ($marca[0]['img_marca']) {
                $this->storage->eliminarImagen($marca[0]['img_marca']);
            }

            return $this->marcaModel->eliminarMarca($idMarca);
        }

        // ✅ Logica de Tipos de Productos
        public function obtenerTiposProductos() : array {
            $tipos = $this->tiposProductoModel->obtenerTiposProductos();
            if (empty($tipos)) {
                return [];
            }
            return $tipos;
        }

        public function registrarTipoProducto(string $nombreTipoProducto) : bool {
            return $this->tiposProductoModel->crearTipoProducto($nombreTipoProducto);
        }
        
        public function actualizarTipoProducto(int $idTipoProducto, string $nombreTipoProducto) : bool {
            return $this->tiposProductoModel->updateTipoProducto($idTipoProducto, $nombreTipoProducto);
        }

        public function eliminarTipoProducto(int $idTipoProducto) : bool {
            return $this->tiposProductoModel->deleteTipoProducto($idTipoProducto); 
        }

        // ✅ Logica de Categorías
        public function obtenerCategorias() : array {
            $categorias = $this->categoriaModel->obtenerTodos();

            if (empty($categorias)) {
                return [];
            }

            return $categorias;
        }

        public function validarCategoria($idCategoria) : bool {
            $categorias = $this->obtenerCategorias();
            return in_array($idCategoria, array_column($categorias, 'id_categoria'));
        }

        public function registrarCategoria(string $nombreCategoria) : bool {
            return $this->categoriaModel->registrarCategoria($nombreCategoria);
        }

        public function actualizarCategoria(int $idCategoria, string $nombreCategoria) : bool {
            return $this->categoriaModel->actualizarCategoria($idCategoria, $nombreCategoria);
        }

        public function eliminarCategoria(int $idCategoria) : bool {
            return $this->categoriaModel->eliminarCategoria($idCategoria);
        }

        //Logica de Sedes
        public function obtenerSedes() : array {
            $sedes = $this->sedesModel->obtenerSedes();
            if (empty($sedes)) {
                return [];
            }
            return $sedes;
        }

        public function registrarSede(array $data) : bool {
            return $this->sedesModel->crearSede($data);
        }
        
        public function actualizarSede(array $data) : bool {
            return $this->sedesModel->actualizarSede($data);
        }
        
        public function eliminarSede(int $idSede) : bool {
            return $this->sedesModel->eliminarSede($idSede);
        }

        // ✅ Logica de Géneros
        public function obtenerGeneros() : array {
            $generos = $this->generoModel->obtenerTodos();

            if (empty($generos)) {
                return [];
            }

            return $generos;
        }

        public function validarGenero($idGenero) : bool {
            $generos = $this->obtenerGeneros();
            return in_array($idGenero, array_column($generos, 'id_genero'));
        }

        // ✅ Logica de Presentaciones
        public function obtenerPresentaciones() : array {
            $presentaciones = $this->presentacionModel->obtenerTodos();

            if (empty($presentaciones)) {
                return [];
            }

            return $presentaciones;
        }

        public function validarPresentacion($idPresentacion) : bool {
            $presentaciones = $this->obtenerPresentaciones();
            return in_array($idPresentacion, array_column($presentaciones, 'id_presentacion'));
        }

        // ✅ Obtener tipos de concentración
        public function obtenerTiposConcentracion() : array {
            return $this->insumosFormulasModel->listadoTiposConcentracion();
        }

        // ✅ Logica de Catalogos Completos
        public function obtenerCatalogosCompletos() : array {
            return [
                'marcas' => $this->obtenerMarcas(),
                'generos' => $this->obtenerGeneros(),
                'categorias' => $this->obtenerCategorias(),
                'presentaciones' => $this->obtenerPresentaciones(),
                'sedes' => $this->obtenerSedes()        
            ];
        }

    }
