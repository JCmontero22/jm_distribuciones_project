<?php

/**
 * ServiceContainer - Factory de servicios
 * Centraliza la creación de instancias usando inyección de dependencias
 *
 * IMPORTANTE: Todos los servicios se instancian aquí una sola vez (Singleton)
 */
class ServiceContainer {
    private static array $instances = [];

    public static function getProductoService(): ProductoService {
        if (!isset(self::$instances['productoService'])) {
            require_once(DIR_MODEL . 'ProductosModel.php');
            require_once(DIR_SERVICE . 'ProductoService.php');
            require_once(DIR_INFRASTRUCTURE . 'FileStorageService.php');

            $modelo = new ProductosModel();
            $storage = new LocalFileStorage(UPLOAD_PRODUCTS);

            self::$instances['productoService'] = new ProductoService($storage, $modelo);
        }
        return self::$instances['productoService'];
    }

    public static function getProveedorService(): ProveedorService {
        if (!isset(self::$instances['proveedorService'])) {
            require_once(DIR_MODEL . 'ProveedorModel.php');
            require_once(DIR_SERVICE . 'ProveedorService.php');

            $modelo = new ProveedorModel();

            self::$instances['proveedorService'] = new ProveedorService($modelo);
        }
        return self::$instances['proveedorService'];
    }

    public static function getCatalogoService(): CatalogoService {
        if (!isset(self::$instances['catalogoService'])) {
            require_once(DIR_MODEL . 'MarcaModel.php');
            require_once(DIR_MODEL . 'GeneroModel.php');
            require_once(DIR_MODEL . 'CategoriaModel.php');
            require_once(DIR_MODEL . 'TiposProductosModel.php');
            require_once(DIR_MODEL . 'SedesModel.php');
            require_once(DIR_MODEL . 'InsumoFormulasModel.php');
            require_once(DIR_SERVICE . 'CatalogoService.php');
            require_once(DIR_INFRASTRUCTURE . 'FileStorageService.php');

            $marcaModel = new MarcaModel();
            $generoModel = new GeneroModel();
            $categoriaModel = new CategoriaModel();
            $tiposProductoModel = new TiposProductosModel();
            $sedesModel = new SedesModel();
            $insumosFormulasModel = new InsumoFormulasModel();
            $storage = new LocalFileStorage(UPLOAD_MARCAS);

            self::$instances['catalogoService'] = new CatalogoService(
                $marcaModel,
                $generoModel,
                $categoriaModel,
                $tiposProductoModel,
                $sedesModel,
                $insumosFormulasModel,
                $storage
            );
        }
        return self::$instances['catalogoService'];
    }

    public static function getFormulaService(): FormulasService {
        if (!isset(self::$instances['formulaService'])) {
            require_once(DIR_MODEL . 'FormulasModel.php');
            require_once(DIR_SERVICE . 'FormulasService.php');
            require_once(DIR_INFRASTRUCTURE . 'FileStorageService.php');

            $modelo = new FormulasModel();

            self::$instances['formulaService'] = new FormulasService($modelo);
        }
        return self::$instances['formulaService'];
    }

    public static function getComprasService(): ComprasService {
        if (!isset(self::$instances['comprasService'])) {
            require_once(DIR_MODEL . 'ComprasModel.php');
            require_once(DIR_SERVICE . 'ComprasService.php');

            $modelo = new ComprasModel();

            self::$instances['comprasService'] = new ComprasService($modelo);
        }
        return self::$instances['comprasService'];
    }

    public static function getPromocionesService(): PromocionesService {
        if (!isset(self::$instances['promocionesService'])) {
            require_once(DIR_MODEL . 'PromocionesModel.php');
            require_once(DIR_SERVICE . 'PromocionesService.php');
            require_once(DIR_INFRASTRUCTURE . 'FileStorageService.php');

            $modelo = new PromocionesModel();
            $storage = new LocalFileStorage(IMG_COMUNES);

            self::$instances['promocionesService'] = new PromocionesService($modelo, $storage);
        }
        return self::$instances['promocionesService'];
    }

    public static function getDescuentosService(): DescuentosService {
        if (!isset(self::$instances['descuentosService'])) {
            require_once(DIR_MODEL . 'DescuentosModel.php');
            require_once(DIR_MODEL . 'ProductosModel.php');
            require_once(DIR_SERVICE . 'DescuentosService.php');

            $modeloDescuento = new DescuentosModel();
            $modeloProducto = new ProductosModel();

            self::$instances['descuentosService'] = new DescuentosService($modeloDescuento, $modeloProducto);
        }
        return self::$instances['descuentosService'];
    }

    public static function getUsuariosService(): UsuariosService {
        if (!isset(self::$instances['usuariosService'])) {
            require_once(DIR_MODEL . 'UsuariosModel.php');
            require_once(DIR_MODEL . 'PermisosModel.php');
            require_once(DIR_MODEL . 'PerfilUsuarioModel.php');
            require_once(DIR_SERVICE . 'UsuariosService.php');

            $usuariosModel = new UsuariosModel();
            $permisosModel = new PermisosModel();
            $perfilUsuarioModel = new PerfilUsuarioModel();

            self::$instances['usuariosService'] = new UsuariosService(
                $usuariosModel,
                $permisosModel,
                $perfilUsuarioModel
            );
        }
        return self::$instances['usuariosService'];
    }
}

