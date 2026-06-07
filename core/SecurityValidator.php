<?php

/**
 * Clase SecurityValidator - Validación y sanitización de datos
 * Centraliza todas las validaciones del sistema
 */
class SecurityValidator {

    /**
     * Sanitiza y valida un array de datos
     *
     * @param array $rules Reglas de validación
     * @param array $data Datos a validar
     * @return array ['valid' => bool, 'data' => array, 'errors' => array]
     */
    public static function validateAndSanitize($rules, $data) {
        $errors = [];
        $sanitized = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? '';
            $sanitized[$field] = self::sanitize($value);

            // Validar requerido
            if (isset($fieldRules['required']) && $fieldRules['required'] && empty($sanitized[$field])) {
                $errors[$field] = "El campo {$field} es requerido";
                continue;
            }

            // Saltar si está vacío y no es requerido
            if (empty($sanitized[$field])) {
                continue;
            }

            // Validar tipo
            if (isset($fieldRules['type'])) {
                $typeResult = self::validateType($sanitized[$field], $fieldRules['type'], $field);
                if ($typeResult['valid'] === false) {
                    $errors[$field] = $typeResult['error'];
                    continue;
                }
                $sanitized[$field] = $typeResult['value'];
            }

            // Validar longitud
            if (isset($fieldRules['minLength'])) {
                if (strlen($sanitized[$field]) < $fieldRules['minLength']) {
                    $errors[$field] = "Mínimo {$fieldRules['minLength']} caracteres";
                }
            }

            if (isset($fieldRules['maxLength'])) {
                if (strlen($sanitized[$field]) > $fieldRules['maxLength']) {
                    $errors[$field] = "Máximo {$fieldRules['maxLength']} caracteres";
                }
            }

            // Validar patrón regex
            if (isset($fieldRules['pattern'])) {
                if (!preg_match($fieldRules['pattern'], $sanitized[$field])) {
                    $errors[$field] = $fieldRules['patternMessage'] ?? "Formato inválido para {$field}";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'data' => $sanitized,
            'errors' => $errors
        ];
    }

    /**
     * Sanitiza un valor (string, array o escalar)
     */
    public static function sanitize($value) {
        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }

        if (is_string($value)) {
            return trim(stripslashes($value));
        }

        return $value;
    }

    /**
     * Valida el tipo de un valor
     */
    private static function validateType($value, $type, $fieldName = '') {
        switch ($type) {
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return ['valid' => false, 'error' => 'Email inválido'];
                }
                break;

            case 'integer':
                if (!is_numeric($value) || intval($value) != $value) {
                    return ['valid' => false, 'error' => "{$fieldName} debe ser un número entero"];
                }
                return ['valid' => true, 'value' => (int)$value];

            case 'numeric':
                if (!is_numeric($value)) {
                    return ['valid' => false, 'error' => "{$fieldName} debe ser un número"];
                }
                return ['valid' => true, 'value' => (float)$value];

            case 'string':
                if (!is_string($value)) {
                    return ['valid' => false, 'error' => "{$fieldName} debe ser texto"];
                }
                break;

            case 'date':
                $dateObj = \DateTime::createFromFormat('Y-m-d', $value);
                if (!$dateObj || $dateObj->format('Y-m-d') !== $value) {
                    return ['valid' => false, 'error' => "{$fieldName} debe ser una fecha válida (YYYY-MM-DD)"];
                }
                break;

            case 'datetime':
                $dateObj = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                if (!$dateObj || $dateObj->format('Y-m-d H:i:s') !== $value) {
                    return ['valid' => false, 'error' => "{$fieldName} debe ser un datetime válido"];
                }
                break;

            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    return ['valid' => false, 'error' => 'URL inválida'];
                }
                break;

            case 'phone':
                if (!preg_match('/^[\d\s\-\+\(\)]{7,}$/', $value)) {
                    return ['valid' => false, 'error' => 'Teléfono inválido'];
                }
                break;

            default:
                return ['valid' => false, 'error' => "Tipo de validación desconocido: {$type}"];
        }

        return ['valid' => true, 'value' => $value];
    }

    /**
     * Valida un email
     */
    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException('Email inválido');
        }
        return $email;
    }

    /**
     * Valida que sea entero
     */
    public static function validateInteger($value) {
        if (!is_numeric($value) || intval($value) != $value) {
            throw new ValidationException('Debe ser un número entero');
        }
        return (int)$value;
    }

    /**
     * Valida que sea numérico
     */
    public static function validateNumeric($value) {
        if (!is_numeric($value)) {
            throw new ValidationException('Debe ser un número');
        }
        return (float)$value;
    }

    /**
     * Escapa valores para SQL (aunque se prefiere prepared statements)
     */
    public static function escapeSql($value) {
        return addslashes($value);
    }

    /**
     * Escapa para HTML
     */
    public static function escapeHtml($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida que un valor esté en una lista permitida
     */
    public static function validateInArray($value, $allowedValues, $fieldName = 'Campo') {
        if (!in_array($value, $allowedValues, true)) {
            throw new ValidationException("{$fieldName} tiene valor inválido");
        }
        return $value;
    }

    /**
     * Valida archivo de imagen
     */
    public static function validateImageFile($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new ValidationException('No se subió archivo');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'Archivo excede tamaño permitido (php.ini)',
                UPLOAD_ERR_FORM_SIZE => 'Archivo excede tamaño permitido (form)',
                UPLOAD_ERR_PARTIAL => 'Archivo subido parcialmente',
                UPLOAD_ERR_NO_FILE => 'Ningún archivo subido',
                UPLOAD_ERR_NO_TMP_DIR => 'Directorio temporal no disponible',
                UPLOAD_ERR_CANT_WRITE => 'No se puede escribir en disco',
                UPLOAD_ERR_EXTENSION => 'Extensión bloqueada',
            ];
            throw new ValidationException($errorMessages[$file['error']] ?? 'Error desconocido en upload');
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            throw new ValidationException('Archivo excede ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimetype, ALLOWED_IMAGE_TYPES)) {
            throw new ValidationException('Formato de imagen no permitido. Permitidos: ' . implode(', ', ALLOWED_EXTENSIONS));
        }

        return true;
    }
}
