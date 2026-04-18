<?php

/**
 * Interfaz para servicios de almacenamiento de archivos
 * Abstrae la implementación de dónde y cómo se guardan los archivos
 * Puedes cambiar de LocalFileStorage a S3, Azure, etc. sin tocar ProductoService
 */
interface IStorageService {
    /**
     * Sube una imagen al almacenamiento
     *
     * @param array $archivo Array $_FILES con la imagen
     * @return string Nombre/ruta del archivo guardado
     * @throws Exception Si hay error en la validación o almacenamiento
     */
    public function subirImagen(array $archivo) : string;

    /**
     * Elimina una imagen del almacenamiento
     *
     * @param string $nombreArchivo Nombre o ruta del archivo a eliminar
     * @return void
     */
    public function eliminarImagen(string $nombreArchivo) : void;
}
