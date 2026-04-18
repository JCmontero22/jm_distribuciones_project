<?php

/**
 * Interfaz para modelos de catálogos (Marca, Género, Categoría, Presentación)
 * Todos los datos de referencia que son lookups/enumeraciones implementan esta interfaz
 */
interface ICatalogModel {
    /**
     * Obtiene todos los registros de este catálogo
     *
     * @return array Array de registros del catálogo
     */
    public function obtenerTodos() : array;
}
