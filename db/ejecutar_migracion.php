<?php
/**
 * Script para ejecutar migraciones de base de datos
 *
 * ADVERTENCIA: Este archivo es solo para desarrollo/mantenimiento
 * ELIMINA este archivo después de ejecutar la migración en producción
 */

require_once('../core/conexion.php');
require_once('../core/Logger.php');

try {
    echo "<h2>🔄 Ejecutando Migraciones...</h2>\n";

    $db = new conexion();

    // Leer el archivo SQL
    $migrationFile = __DIR__ . '/migrations/001_add_id_sede_to_detalle_compra.sql';

    if (!file_exists($migrationFile)) {
        throw new Exception("Archivo de migración no encontrado: $migrationFile");
    }

    $sql = file_get_contents($migrationFile);

    // Separar múltiples consultas (por ; y excluyendo comentarios)
    $lines = array_filter(array_map('trim', explode("\n", $sql)), function($line) {
        return !empty($line) && !str_starts_with($line, '--');
    });

    $query = '';
    $executed = 0;

    foreach ($lines as $line) {
        $query .= $line . ' ';

        if (str_ends_with($line, ';')) {
            $query = trim(str_replace(';', '', $query));

            if (!empty($query)) {
                try {
                    $db->getDb()->exec($query . ';');
                    echo "<span style='color: green;'>✓ Ejecutado:</span> " . substr($query, 0, 60) . "...<br>\n";
                    $executed++;
                } catch (Exception $e) {
                    echo "<span style='color: orange;'>⚠️ Advertencia:</span> " . $e->getMessage() . "<br>\n";
                    // No detenemos, algunas restricciones pueden ya existir
                }
            }

            $query = '';
        }
    }

    echo "<hr>\n";
    echo "<h3 style='color: green;'>✓ Migración completada!</h3>\n";
    echo "<p>Total de sentencias ejecutadas: <strong>$executed</strong></p>\n";

    echo "<h4>Próximos pasos:</h4>\n";
    echo "<ol>\n";
    echo "  <li>Verifica que la columna <code>id_sede</code> fue agregada a <code>detalle_compra</code></li>\n";
    echo "  <li>Intenta registrar un nuevo detalle de compra en la aplicación</li>\n";
    echo "  <li><strong>IMPORTANTE:</strong> Elimina este archivo (<code>db/ejecutar_migracion.php</code>) después de confirmar que todo funciona</li>\n";
    echo "</ol>\n";

} catch (Exception $e) {
    echo "<h3 style='color: red;'>✗ Error al ejecutar migración:</h3>\n";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid red;'>\n";
    echo htmlspecialchars($e->getMessage()) . "\n";
    echo "\n" . htmlspecialchars($e->getTraceAsString()) . "\n";
    echo "</pre>\n";

    Logger::error("Error ejecutando migración", $e, [
        'migration_file' => $migrationFile
    ]);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejecutar Migración - JM Distribuciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f9f9f9;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ JM Distribuciones - Migración de Base de Datos</h1>
        <hr>
        <!-- El resultado se imprime arriba en los echo -->
    </div>
</body>
</html>
