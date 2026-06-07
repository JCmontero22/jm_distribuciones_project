<?php

/**
 * Clase LocalFileStorage - Gestiona almacenamiento de archivos en disco local
 * Valida tipos, tamaños y crea directorios automáticamente
 */
class LocalFileStorage {

    private string $directorioBase;

    public function __construct(string $directorioBase) {
        $this->directorioBase = rtrim($directorioBase, '/') . '/';
        $this->createDirectoryIfNotExists();
    }

    /**
     * Crea el directorio si no existe
     */
    private function createDirectoryIfNotExists(): void {
        if (!is_dir($this->directorioBase)) {
            if (!mkdir($this->directorioBase, 0755, true)) {
                throw new \RuntimeException("No se pudo crear directorio: {$this->directorioBase}");
            }
        }

        if (!is_writable($this->directorioBase)) {
            throw new \RuntimeException("Directorio no tiene permisos de escritura: {$this->directorioBase}");
        }
    }

    /**
     * Sube una imagen con validaciones robustas
     *
     * @param array $archivo Array $_FILES['campo']
     * @return string Nombre del archivo guardado
     * @throws ValidationException Si hay errores de validación
     */
    public function subirImagen(array $archivo): string {
        // Validar que existe el archivo
        if (!isset($archivo['tmp_name']) || !is_uploaded_file($archivo['tmp_name'])) {
            throw new ValidationException('No se subió archivo');
        }

        // Validar error de upload
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'Archivo excede tamaño permitido (php.ini)',
                UPLOAD_ERR_FORM_SIZE => 'Archivo excede tamaño permitido (form)',
                UPLOAD_ERR_PARTIAL => 'Archivo subido parcialmente',
                UPLOAD_ERR_NO_FILE => 'Ningún archivo subido',
                UPLOAD_ERR_NO_TMP_DIR => 'Directorio temporal no disponible',
                UPLOAD_ERR_CANT_WRITE => 'No se puede escribir en disco',
                UPLOAD_ERR_EXTENSION => 'Extensión bloqueada',
            ];
            throw new ValidationException($errorMessages[$archivo['error']] ?? 'Error desconocido en upload');
        }

        // Validar tamaño
        if ($archivo['size'] > MAX_UPLOAD_SIZE) {
            throw new ValidationException('Archivo excede ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB');
        }

        // Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimetype, ALLOWED_IMAGE_TYPES)) {
            throw new ValidationException('Formato de imagen no permitido. Permitidos: ' . implode(', ', ALLOWED_EXTENSIONS));
        }

        // Validar extensión del archivo
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            throw new ValidationException("Extensión '{$extension}' no permitida");
        }

        // Generar nombre único
        $nombreUnico = $this->generateUniqueFileName($extension);
        $rutaFinal = $this->directorioBase . $nombreUnico;

        // Mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            Logger::error("Error al mover archivo subido a: {$rutaFinal}");
            throw new \RuntimeException('No se pudo guardar la imagen en el servidor');
        }

        Logger::info("Imagen guardada correctamente", ['archivo' => $nombreUnico, 'ruta' => $rutaFinal]);
        return $nombreUnico;
    }

    /**
     * Elimina una imagen del almacenamiento
     */
    public function eliminarImagen(string $nombreArchivo): void {
        if (empty($nombreArchivo)) {
            return;
        }

        $ruta = $this->directorioBase . basename($nombreArchivo);

        // Validar que el archivo está dentro del directorio permitido
        if (realpath($ruta) === false || strpos(realpath($ruta), realpath($this->directorioBase)) !== 0) {
            Logger::warning("Intento de eliminar archivo fuera del directorio permitido", ['archivo' => $nombreArchivo]);
            throw new \RuntimeException("Archivo no encontrado o ubicación inválida");
        }

        if (file_exists($ruta)) {
            if (!unlink($ruta)) {
                Logger::error("Error al eliminar archivo: {$ruta}");
                throw new \RuntimeException("No se pudo eliminar el archivo");
            }
            Logger::info("Imagen eliminada correctamente", ['archivo' => $nombreArchivo]);
        }
    }

    /**
     * Genera nombre de archivo único
     */
    private function generateUniqueFileName(string $extension): string {
        $prefix = 'file_' . date('Ymd_His') . '_';
        $random = bin2hex(random_bytes(8));
        return $prefix . $random . '.' . strtolower($extension);
    }

    /**
     * Obtiene la ruta completa de un archivo
     */
    public function getFullPath(string $nombreArchivo): string {
        return $this->directorioBase . basename($nombreArchivo);
    }

    /**
     * Obtiene el directorio base
     */
    public function getBaseDirectory(): string {
        return $this->directorioBase;
    }

    /**
     * Verifica si un archivo existe
     */
    public function fileExists(string $nombreArchivo): bool {
        return file_exists($this->directorioBase . basename($nombreArchivo));
    }
}
