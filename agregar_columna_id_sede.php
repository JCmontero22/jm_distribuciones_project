<?php
/**
 * Script simple para agregar la columna id_sede a la tabla detalle_compra
 * EJECUTA ESTO PRIMERO - sin transacciones, directo
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión directa a la BD
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'jm_distribuciones';

try {
    echo "<h1>🔧 Agregar columna id_sede a detalle_compra</h1>\n";
    echo "<hr>\n";

    // Conectar
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p>✓ Conectado a la base de datos</p>\n";

    // Paso 1: Verificar si la columna ya existe
    echo "<h3>Paso 1: Verificar columna</h3>\n";
    $stmt = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                         WHERE TABLE_NAME='detalle_compra' AND COLUMN_NAME='id_sede'");
    $exists = $stmt->fetch();

    if ($exists) {
        echo "<p style='color: green;'>✓ La columna id_sede YA existe</p>\n";
    } else {
        echo "<p style='color: orange;'>La columna id_sede NO existe, agregando...</p>\n";

        // Paso 2: Agregar la columna
        echo "<h3>Paso 2: Ejecutar ALTER TABLE</h3>\n";
        $sql = "ALTER TABLE detalle_compra
                ADD COLUMN id_sede INT NOT NULL DEFAULT 1 AFTER id_compra";

        $pdo->exec($sql);
        echo "<p style='color: green;'>✓ Columna id_sede agregada</p>\n";
    }

    // Paso 3: Agregar índice si no existe
    echo "<h3>Paso 3: Agregar índice</h3>\n";
    $stmt = $pdo->query("SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS
                         WHERE TABLE_NAME='detalle_compra' AND COLUMN_NAME='id_sede' AND INDEX_NAME='idx_detalle_compra_sede'");
    $indexExists = $stmt->fetch();

    if (!$indexExists) {
        $pdo->exec("CREATE INDEX idx_detalle_compra_sede ON detalle_compra(id_sede)");
        echo "<p style='color: green;'>✓ Índice idx_detalle_compra_sede creado</p>\n";
    } else {
        echo "<p style='color: green;'>✓ Índice ya existe</p>\n";
    }

    // Paso 4: Verificación final
    echo "<h3>Paso 4: Verificación final</h3>\n";
    $stmt = $pdo->query("DESCRIBE detalle_compra");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>\n";
    echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Null</th></tr>\n";
    foreach ($columns as $col) {
        $highlight = (strtolower($col['Field']) === 'id_sede') ? "style='background: #90EE90;'" : "";
        echo "<tr $highlight>\n";
        echo "  <td>{$col['Field']}</td>\n";
        echo "  <td>{$col['Type']}</td>\n";
        echo "  <td>{$col['Null']}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";

    echo "<hr>\n";
    echo "<h2 style='color: green;'>✓ ¡LISTO!</h2>\n";
    echo "<p>La columna id_sede está lista. Ahora intenta registrar un detalle de compra nuevamente.</p>\n";
    echo "<p><a href='javascript:history.back()'>Volver</a></p>\n";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>✗ ERROR</h2>\n";
    echo "<pre style='background: #fee; padding: 10px; border: 1px solid red;'>\n";
    echo htmlspecialchars($e->getMessage()) . "\n";
    echo "</pre>\n";
    echo "<p><strong>Detalles técnicos:</strong></p>\n";
    echo "<ul>\n";
    echo "  <li>Host: $host</li>\n";
    echo "  <li>Usuario: $user</li>\n";
    echo "  <li>Base de datos: $database</li>\n";
    echo "</ul>\n";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar id_sede</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            margin: 15px 0;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Resultados arriba -->
    </div>
</body>
</html>
