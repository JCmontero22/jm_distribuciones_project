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
}
