<?php

/**
 * Interfaz para el repositorio de Productos
 * Define el contrato para acceder a datos de productos de cualquier tipo
 * (Relojes, Lociones, Insumos, etc.)
 */
interface IProductoRepositorio {
    /**
     * Verifica si un producto existe por código
     *
     * @param string $codigo Código del producto
     * @return bool True si existe, false si no
     */
    public function existeProducto(string $codigo) : bool;

    /**
     * Registra un nuevo producto en la base de datos
     *
     * @param array $data Array con los datos del producto
     * @return mixed ID del producto creado o resultado de la inserción
     */
    public function registrarProducto(array $data);

    /**
     * Obtiene todos los productos de una categoría específica
     *
     * @param string $categoria Nombre de la categoría (ej: "Relojes")
     * @return array Array de productos de esa categoría
     */
    public function obtenerPorCategoria(string $categoria) : array;
}
