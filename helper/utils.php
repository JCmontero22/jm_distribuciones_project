<?php

/**
 * Clase Utils - Utilidades comunes del proyecto
 */
class utils {

    /**
     * Valida que campos requeridos existan y no estén vacíos
     * @deprecated Usar SecurityValidator::validateAndSanitize() en su lugar
     */
    public static function validateRequiredFields($fields, $data) {
        foreach ($fields as $field) {
            // Usar isset para permitir valores como '0' (empty('0') devuelve true)
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Sanitiza entrada de usuario (solo trim)
     * @deprecated Usar SecurityValidator::sanitize() en su lugar
     */
    public static function sanitizeInput($input) {
        return trim($input);
    }

    /**
     * Formatea número con separadores de miles
     */
    public static function formatNumber($number, $decimals = 0, $decPoint = ',', $thousandsSep = '.') {
        return number_format((float)$number, $decimals, $decPoint, $thousandsSep);
    }

    /**
     * Convierte número formateado a valor numérico
     */
    public static function unformatNumber($number) {
        return str_replace(['.', ','], ['', '.'], $number);
    }

    /**
     * Valida email
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Convierte string a slug (URL friendly)
     */
    public static function toSlug($string) {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $string = preg_replace('/[^a-z0-9\-]/', '', strtolower($string));
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }

    /**
     * Genera código aleatorio
     */
    public static function generateCode($length = 8, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $charset[random_int(0, strlen($charset) - 1)];
        }
        return $code;
    }

    /**
     * Obtiene la edad en años desde una fecha
     */
    public static function getAge($birthDate) {
        $today = new \DateTime('today');
        $d = \DateTime::createFromFormat('Y-m-d', $birthDate);
        if ($d === false) {
            return null;
        }
        return $today->diff($d)->y;
    }

    /**
     * Formatea fecha para mostrar
     */
    public static function formatDate($date, $format = 'd/m/Y') {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        if ($d === false) {
            return '';
        }
        return $d->format($format);
    }

    /**
     * Formatea datetime para mostrar
     */
    public static function formatDateTime($datetime, $format = 'd/m/Y H:i:s') {
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        if ($d === false) {
            return '';
        }
        return $d->format($format);
    }

    /**
     * Convierte fecha a formato ISO (Y-m-d)
     */
    public static function toISODate($date, $currentFormat = 'd/m/Y') {
        $d = \DateTime::createFromFormat($currentFormat, $date);
        if ($d === false) {
            return '';
        }
        return $d->format('Y-m-d');
    }

    /**
     * Paginación - Calcula offset
     */
    public static function getPaginationOffset($page, $perPage = 10) {
        $page = max(1, (int)$page);
        return ($page - 1) * $perPage;
    }

    /**
     * Calcula páginas totales
     */
    public static function getTotalPages($total, $perPage = 10) {
        return ceil($total / $perPage);
    }

    /**
     * Array en formato de tabla HTML (para debugging)
     */
    public static function arrayToHtmlTable($data) {
        $html = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    /**
     * Obtiene cliente IP
     */
    public static function getClientIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
    }

    /**
     * Obtiene navegador del cliente
     */
    public static function getBrowserName() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } else {
            return 'Unknown';
        }
    }
}
