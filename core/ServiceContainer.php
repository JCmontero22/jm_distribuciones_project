<?php

class ServiceContainer {
    private static array $instances = [];

    public static function getProductoService(): ProductoService {
        if (!isset(self::$instances['productoService'])) {
            require_once(__DIR__ . '/../model/ProductosModel.php');
            require_once(__DIR__ . '/../services/ProductoService.php');
            require_once(__DIR__ . '/../Infrastructure/FileStorageService.php');

            $modelo = new ProductosModel();
            $storage = new LocalFileStorage(__DIR__ . '/../assets/img/productos/');

            self::$instances['productoService'] = new ProductoService($storage, $modelo);
        }
        return self::$instances['productoService'];
    }

    public static function getProveedorService(): ProveedorService {
        if (!isset(self::$instances['proveedorService'])) {
            require_once(__DIR__ . '/../model/ProveedorModel.php');
            require_once(__DIR__ . '/../services/ProveedorService.php');

            $modelo = new ProveedorModel();

            self::$instances['proveedorService'] = new ProveedorService($modelo);
        }
        return self::$instances['proveedorService'];
    }

    public static function getCatalogoService(): CatalogoService {
        if (!isset(self::$instances['catalogoService'])) {
            require_once(__DIR__ . '/../model/MarcaModel.php');
            require_once(__DIR__ . '/../model/GeneroModel.php');
            require_once(__DIR__ . '/../model/CategoriaModel.php');
            require_once(__DIR__ . '/../model/PresentacionProductoModel.php');
            require_once(__DIR__ . '/../model/TiposProductosModel.php');
            require_once(__DIR__ . '/../model/SedesModel.php');
            require_once(__DIR__ . '/../services/CatalogoService.php');

            $marcaModel = new MarcaModel();
            $generoModel = new GeneroModel();
            $categoriaModel = new CategoriaModel();
            
            $tiposProductoModel = new TiposProductosModel();
            $sedesModel = new SedesModel();

            self::$instances['catalogoService'] = new CatalogoService(
                $marcaModel,
                $generoModel,
                $categoriaModel,
                $presentacionModel,
                $tiposProductoModel,
                $sedesModel
            );
        }
        return self::$instances['catalogoService'];
    }

    public static function getFormulaService(): FormulasService {
        if (!isset(self::$instances['formulaService'])) {
            require_once(__DIR__ . '/../model/FormulasModel.php');
            require_once(__DIR__ . '/../services/FormulasService.php');
            require_once(__DIR__ . '/../Infrastructure/FileStorageService.php');
            require_once(__DIR__ . '/../core/Logger.php');

            $modelo = new FormulasModel();

            self::$instances['formulaService'] = new FormulasService($modelo);
        }
        return self::$instances['formulaService'];
    }

    public static function getComprasService(): ComprasService {
        if (!isset(self::$instances['comprasService'])) {
            require_once(__DIR__ . '/../model/ComprasModel.php');
            require_once(__DIR__ . '/../services/ComprasService.php');

            $modelo = new ComprasModel();

            self::$instances['comprasService'] = new ComprasService($modelo);
        }
        return self::$instances['comprasService'];
    }

    public static function getCapacidadService(): CapacidadProduccionService {
        if (!isset(self::$instances['capacidadService'])) {
            require_once(__DIR__ . '/../model/CapacidadProduccionModel.php');
            require_once(__DIR__ . '/../services/CapacidadProduccionService.php');

            $modelo = new CapacidadProduccionModel();

            self::$instances['capacidadService'] = new CapacidadProduccionService($modelo);
        }
        return self::$instances['capacidadService'];
    }
}
