<?php

/**
 * Clase Response - Formatea todas las respuestas JSON del API
 * Asegura consistencia en estructura y códigos HTTP
 */
class response {

    public static function success($data = [], $message = 'Operación exitosa') {
        http_response_code(200);
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    public static function error($message = 'Ocurrió un error', $data = [], $statusCode = 400) {
        http_response_code($statusCode);
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    public static function validationError($errors = []) {
        http_response_code(422);
        return [
            'success' => false,
            'message' => 'Errores de validación',
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    public static function unauthorized($message = 'No autorizado') {
        http_response_code(401);
        return self::error($message, [], 401);
    }

    public static function forbidden($message = 'Acceso denegado') {
        http_response_code(403);
        return self::error($message, [], 403);
    }

    public static function notFound($message = 'Recurso no encontrado') {
        http_response_code(404);
        return self::error($message, [], 404);
    }

    public static function conflict($message = 'Conflicto con recurso existente') {
        http_response_code(409);
        return self::error($message, [], 409);
    }
}
