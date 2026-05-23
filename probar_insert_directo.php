<?php
/**
 * Script para probar el INSERT directamente y ver qué pasa
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'distribuciones_jm';

echo "<h1>🔍 Prueba de INSERT Directo</h1>\n";
echo "<hr>\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>1. Estructura actual de detalle_compra</h2>\n";
    $result = $pdo->query("DESCRIBE detalle_compra");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>\n";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>Campo</th><th>Tipo</th><th>Null</th><th>Clave</th><th>Por Defecto</th><th>Extra</th>";
    echo "</tr>\n";

    foreach ($columns as $col) {
        $highlight = (strtolower($col['Field']) === 'id_sede') ? "style='background: #90EE90;'" : "";
        echo "<tr $highlight>\n";
        echo "  <td><strong>{$col['Field']}</strong></td>\n";
        echo "  <td>{$col['Type']}</td>\n";
        echo "  <td>{$col['Null']}</td>\n";
        echo "  <td>" . ($col['Key'] ?: '-') . "</td>\n";
        echo "  <td>" . ($col['Default'] !== null ? $col['Default'] : '-') . "</td>\n";
        echo "  <td>{$col['Extra']}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";

    // Verificar que exista una compra
    echo "<h2>2. Verificar que existe al menos una compra</h2>\n";
    $stmt = $pdo->query("SELECT id_compra FROM compras LIMIT 1");
    $compra = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$compra) {
        echo "<p style='color: red;'>❌ No hay compras registradas. Necesita registrar una compra primero.</p>\n";
        exit;
    }

    $idCompra = $compra['id_compra'];
    echo "<p style='color: green;'>✓ Se encontró compra con ID: <strong>$idCompra</strong></p>\n";

    // Verificar que exista una presentación
    echo "<h2>3. Verificar que existe una presentación</h2>\n";
    $stmt = $pdo->query("SELECT id_presentacion FROM productos_presentaciones LIMIT 1");
    $presentacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$presentacion) {
        echo "<p style='color: red;'>❌ No hay presentaciones registradas.</p>\n";
        exit;
    }

    $idPresentacion = $presentacion['id_presentacion'];
    echo "<p style='color: green;'>✓ Se encontró presentación con ID: <strong>$idPresentacion</strong></p>\n";

    // Verificar que exista una sede
    echo "<h2>4. Verificar que existe una sede</h2>\n";
    $stmt = $pdo->query("SELECT id_sede FROM sede LIMIT 1");
    $sede = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sede) {
        echo "<p style='color: red;'>❌ No hay sedes registradas.</p>\n";
        exit;
    }

    $idSede = $sede['id_sede'];
    echo "<p style='color: green;'>✓ Se encontró sede con ID: <strong>$idSede</strong></p>\n";

    // INTENTO 1: INSERT SIMPLE (solo columnas necesarias)
    echo "<h2>5. INTENTO 1: INSERT Simple</h2>\n";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>\n";
    $sql1 = "INSERT INTO detalle_compra (id_compra, id_presentacion, id_sede, cantidad_detalle_compra, costo_unitario_detalle_compra, subtotal_detalle_compra)
             VALUES (:id_compra, :id_presentacion, :id_sede, :cantidad, :costo_unitario, :subtotal)";
    echo htmlspecialchars($sql1);
    echo "\n</pre>\n";

    try {
        $stmt = $pdo->prepare($sql1);
        $stmt->execute([
            ':id_compra' => $idCompra,
            ':id_presentacion' => $idPresentacion,
            ':id_sede' => $idSede,
            ':cantidad' => 2,
            ':costo_unitario' => 50000,
            ':subtotal' => 100000
        ]);
        echo "<p style='color: green;'><strong>✓ INSERT 1 exitoso!</strong></p>\n";
        $lastId = $pdo->lastInsertId();
        echo "<p>ID del nuevo registro: <strong>$lastId</strong></p>\n";

        // Limpiar
        $pdo->exec("DELETE FROM detalle_compra WHERE id_detalle_compra = $lastId");
        echo "<p style='color: gray;'>(Registro de prueba eliminado)</p>\n";

    } catch (PDOException $e) {
        echo "<p style='color: red;'><strong>✗ Error:</strong></p>\n";
        echo "<pre style='background: #fee; padding: 10px; border: 1px solid red;'>\n";
        echo htmlspecialchars($e->getMessage());
        echo "\n</pre>\n";

        // INTENTO 2: Sin id_sede
        echo "<h2>6. INTENTO 2: INSERT sin id_sede (para verificar)</h2>\n";
        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>\n";
        $sql2 = "INSERT INTO detalle_compra (id_compra, id_presentacion, cantidad_detalle_compra, costo_unitario_detalle_compra, subtotal_detalle_compra)
                 VALUES (:id_compra, :id_presentacion, :cantidad, :costo_unitario, :subtotal)";
        echo htmlspecialchars($sql2);
        echo "\n</pre>\n";

        try {
            $stmt = $pdo->prepare($sql2);
            $stmt->execute([
                ':id_compra' => $idCompra,
                ':id_presentacion' => $idPresentacion,
                ':cantidad' => 2,
                ':costo_unitario' => 50000,
                ':subtotal' => 100000
            ]);
            echo "<p style='color: orange;'><strong>⚠️ INSERT 2 exitoso SIN id_sede!</strong></p>\n";
            echo "<p>Esto significa que <strong>id_sede tiene un valor por defecto</strong>.</p>\n";

            $lastId = $pdo->lastInsertId();
            $pdo->exec("DELETE FROM detalle_compra WHERE id_detalle_compra = $lastId");

        } catch (PDOException $e2) {
            echo "<p style='color: red;'><strong>✗ También falla sin id_sede:</strong></p>\n";
            echo "<pre style='background: #fee; padding: 10px; border: 1px solid red;'>\n";
            echo htmlspecialchars($e2->getMessage());
            echo "\n</pre>\n";
        }
    }

    echo "<hr>\n";
    echo "<h2>📊 Conclusión</h2>\n";
    echo "<p>Si ambos INSERTs fallan, probablemente:</p>\n";
    echo "<ul>\n";
    echo "  <li>Hay otra restricción impidiendo el INSERT (foreign key, check constraint)</li>\n";
    echo "  <li>La tabla `detalle_compra` está corrupta o bloqueada</li>\n";
    echo "  <li>Hay un problema con el nombre de la columna (espacios, caracteres especiales)</li>\n";
    echo "</ul>\n";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error de conexión</h2>\n";
    echo "<pre style='background: #fee; padding: 10px; border: 1px solid red;'>\n";
    echo htmlspecialchars($e->getMessage());
    echo "\n</pre>\n";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba INSERT</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
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
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Resultados arriba -->
    </div>
</body>
</html>
