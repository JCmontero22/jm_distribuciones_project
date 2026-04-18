<?php

    require_once('../contracts/IStorageService.php');

    class LocalFileStorage implements IStorageService
    {
    private $directorioBase;

    // Al construirlo, le decimos dónde va a guardar las cosas por defecto
    public function __construct(string $directorioBase) {
        $this->directorioBase = rtrim($directorioBase, '/') . '/';
    }

    public function subirImagen(array $archivo): string {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Error al subir el archivo físico.');
        }

        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            throw new \DomainException('Formato de imagen no permitido. Usa JPG, PNG o WEBP.');
        }

        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreUnico = uniqid('prod_', true) . '.' . $extension;
        $rutaFinal = $this->directorioBase . $nombreUnico;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            throw new \Exception('No se pudo guardar la imagen en el disco del servidor.');
        }

        return $nombreUnico;
    }

    public function eliminarImagen(string $nombreArchivo): void{
        $ruta = $this->directorioBase . $nombreArchivo;

        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }
}