<?php

    require_once('../model/MarcaModel.php');
    require_once('../model/GeneroModel.php');
    require_once('../model/CategoriaModel.php');
    require_once('../model/PresentacionProductoModel.php');

    class CatalogoService {
        private $marcaModel;
        private $generoModel;
        private $categoriaModel;
        private $presentacionModel;

        public function __construct(
            MarcaModel $marcaModel,
            GeneroModel $generoModel,
            CategoriaModel $categoriaModel,
            PresentacionProductoModel $presentacionModel
        ) {
            $this->marcaModel = $marcaModel;
            $this->generoModel = $generoModel;
            $this->categoriaModel = $categoriaModel;
            $this->presentacionModel = $presentacionModel;
        }

        // ✅ Obtener Marcas con validación
        public function obtenerMarcas() : array {
            $marcas = $this->marcaModel->obtenerTodos();

            if (empty($marcas)) {
                return [];
            }

            return $marcas;
        }

        // ✅ Obtener Géneros con validación
        public function obtenerGeneros() : array {
            $generos = $this->generoModel->obtenerTodos();

            if (empty($generos)) {
                return [];
            }

            return $generos;
        }

        // ✅ Obtener Categorías con validación
        public function obtenerCategorias() : array {
            $categorias = $this->categoriaModel->obtenerTodos();

            if (empty($categorias)) {
                return [];
            }

            return $categorias;
        }

        // ✅ Obtener Presentaciones con validación
        public function obtenerPresentaciones() : array {
            $presentaciones = $this->presentacionModel->getPresentaciones();

            if (empty($presentaciones)) {
                return [];
            }

            return $presentaciones;
        }

        // ✅ LÓGICA: Obtener todos los catálogos de una vez (útil para inicialización)
        public function obtenerCatalogosCompletos() : array {
            return [
                'marcas' => $this->obtenerMarcas(),
                'generos' => $this->obtenerGeneros(),
                'categorias' => $this->obtenerCategorias(),
                'presentaciones' => $this->obtenerPresentaciones()
            ];
        }

        // ✅ LÓGICA: Validar que una marca exista
        public function validarMarca($idMarca) : bool {
            $marcas = $this->obtenerMarcas();
            return in_array($idMarca, array_column($marcas, 'id_marca'));
        }

        // ✅ LÓGICA: Validar que un género exista
        public function validarGenero($idGenero) : bool {
            $generos = $this->obtenerGeneros();
            return in_array($idGenero, array_column($generos, 'id_genero'));
        }

        // ✅ LÓGICA: Validar que una categoría exista
        public function validarCategoria($idCategoria) : bool {
            $categorias = $this->obtenerCategorias();
            return in_array($idCategoria, array_column($categorias, 'id_categoria'));
        }
    }
