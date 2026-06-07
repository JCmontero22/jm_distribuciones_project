<?php

class Logger
{
    private const LOG_FILE = __DIR__ . '/../logs/app.log';

    public static function error(string $message, ?\Throwable $exception = null, ?array $context = null): void
    {
        self::ensureLogDirectoryExists();

        $log = sprintf(
            "[%s] ERROR: %s%s" . PHP_EOL . PHP_EOL . PHP_EOL,
            date('Y-m-d H:i:s'),
            $message,
            PHP_EOL
        );

        if ($exception !== null) {
            $log .= sprintf(
                PHP_EOL . PHP_EOL . "Mensaje: %s%sFile: %s%sLine: %s%sStack trace:%s%s%s",
                $exception->getMessage(),
                PHP_EOL,
                $exception->getFile(),
                PHP_EOL,
                $exception->getLine(),
                PHP_EOL,
                PHP_EOL,
                $exception->getTraceAsString(),
                PHP_EOL
            );
        }

        if ($context !== null) {
            $log .= "Context:" . PHP_EOL;
            $log .= print_r($context, true) . PHP_EOL . PHP_EOL;
        }

        $log .= str_repeat('-', 120) . PHP_EOL . PHP_EOL . PHP_EOL;

        file_put_contents(self::LOG_FILE, $log, FILE_APPEND);
    }

    public static function info(string $message, ?array $context = null): void
    {
        self::ensureLogDirectoryExists();

        $log = sprintf(
            "[%s] INFO: %s%s",
            date('Y-m-d H:i:s'),
            $message,
            PHP_EOL
        );

        if ($context !== null) {
            $log .= "Context: " . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
        }

        $log .= str_repeat('-', 120) . PHP_EOL;

        file_put_contents(self::LOG_FILE, $log, FILE_APPEND);
    }

    public static function warning(string $message, ?array $context = null): void
    {
        self::ensureLogDirectoryExists();

        $log = sprintf(
            "[%s] WARNING: %s%s",
            date('Y-m-d H:i:s'),
            $message,
            PHP_EOL
        );

        if ($context !== null) {
            $log .= "Context: " . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
        }

        $log .= str_repeat('-', 120) . PHP_EOL;

        file_put_contents(self::LOG_FILE, $log, FILE_APPEND);
    }

    public static function debug(string $message, ?array $context = null): void
    {
        if (APP_DEBUG !== true) {
            return;
        }

        self::ensureLogDirectoryExists();

        $log = sprintf(
            "[%s] DEBUG: %s%s",
            date('Y-m-d H:i:s'),
            $message,
            PHP_EOL
        );

        if ($context !== null) {
            $log .= "Context: " . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
        }

        $log .= str_repeat('-', 120) . PHP_EOL;

        file_put_contents(self::LOG_FILE, $log, FILE_APPEND);
    }

    private static function ensureLogDirectoryExists(): void
    {
        $dir = dirname(self::LOG_FILE);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        if (!is_writable($dir)) {
            throw new Exception("No se puede escribir en el directorio de logs.");
        }
    }
}
