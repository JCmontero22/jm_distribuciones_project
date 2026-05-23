<?php
/**
 * Script de diagnóstico para verificar la base de datos y tabla detalle_compra
 */

require_once('core/conexion.php');

echo "<h2>🔍 Diagnóstico de Base de Datos</h2>\n";
echo "<hr>\n";

try {
    $db = new conexion();
    $pdo = $db->getDb();

    // Test 1: Verificar conexión
    echo "<h3>1. Conexión a la base de datos</h3>\n";
    echo "<p><strong>✓ Conectado exitosamente</strong></p>\n";

    // Test 2: Listar estructura de tabla detalle_compra
    echo "<h3>2. Estructura de tabla 'detalle_compra'</h3>\n";
    $stmt = $pdo->query("DESCRIBE detalle_compra");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>\n";
    echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Clave</th><th>Por Defecto</th></tr>\n";

    $id_sede_exists = false;
    foreach ($columns as $col) {
        $key = $col['Key'] ?: '-';
        $default = $col['Default'] ?: 'Sin defecto';

        $row_style = '';
        if (strtolower($col['Field']) === 'id_sede') {
            $row_style = "style='background: #90EE90;'";
            $id_sede_exists = true;
        }

        echo "<tr $row_style>\n";
        echo "  <td><strong>{$col['Field']}</strong></td>\n";
        echo "  <td>{$col['Type']}</td>\n";
        echo "  <td>{$col['Null']}</td>\n";
        echo "  <td>{$key}</td>\n";
        echo "  <td>{$default}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";

    if ($id_sede_exists) {
        echo "<p style='color: green;'><strong>✓ Columna 'id_sede' existe en la tabla</strong></p>\n";
    } else {
        echo "<p style='color: red;'><strong>✗ Columna 'id_sede' NO existe</strong></p>\n";
        echo "<p>Necesita ejecutar la migración primero.</p>\n";
    }

    // Test 3: Verificar relación con tabla sede
    echo "<h3>3. Claves Foráneas</h3>\n";
    $stmt = $pdo->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                         FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                         WHERE TABLE_NAME = 'detalle_compra'");
    $fks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($fks)) {
        echo "<p style='color: orange;'>No hay claves foráneas definidas</p>\n";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>\n";
        echo "<tr style='background: #f0f0f0;'><th>Constraint</th><th>Columna</th><th>Tabla Ref.</th><th>Columna Ref.</th></tr>\n";
        foreach ($fks as $fk) {
            if ($fk['REFERENCED_TABLE_NAME']) {
                echo "<tr>\n";
                echo "  <td>{$fk['CONSTRAINT_NAME']}</td>\n";
                echo "  <td>{$fk['COLUMN_NAME']}</td>\n";
                echo "  <td>{$fk['REFERENCED_TABLE_NAME']}</td>\n";
                echo "  <td>{$fk['REFERENCED_COLUMN_NAME']}</td>\n";
                echo "</tr>\n";
            }
        }
        echo "</table>\n";
    }

    // Test 4: Verificar tabla sede
    echo "<h3>4. Tabla 'sede' - Registros disponibles</h3>\n";
    $stmt = $pdo->query("SELECT id_sede, nombre_sede FROM sede LIMIT 10");
    $sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($sedes)) {
        echo "<p style='color: red;'>No hay sedes registradas</p>\n";
    } else {
        echo "<ul>\n";
        foreach ($sedes as $sede) {
            echo "  <li>ID: {$sede['id_sede']} - {$sede['nombre_sede']}</li>\n";
        }
        echo "</ul>\n";
    }

    // Test 5: Hacer un INSERT de prueba
    echo "<h3>5. Prueba de INSERT</h3>\n";
    echo "<p>Intentando hacer un INSERT de prueba...</p>\n";

    try {
        // Verificar si existen registros necesarios
        $stmt = $pdo->query("SELECT COUNT(*) FROM compras");
        $comprasCount = $stmt->fetchColumn();

        if ($comprasCount === 0) {
            echo "<p style='color: orange;'>⚠️ No hay compras registradas. Se necesita al menos una compra.</p>\n";
        } else {
            $stmt = $pdo->query("SELECT id_compra FROM compras LIMIT 1");
            $idCompra = $stmt->fetchColumn();

            $testQuery = "INSERT INTO detalle_compra (id_compra, id_presentacion, id_sede, cantidad_detalle_compra, costo_unitario_detalle_compra, subtotal_detalle_compra)
                         VALUES (:id_compra, :id_presentacion, :id_sede, :cantidad, :costo_unitario, :subtotal)";

            $testParams = [
                ':id_compra' => $idCompra,
                ':id_presentacion' => 1,
                ':id_sede' => 1,
                ':cantidad' => 1,
                ':costo_unitario' => 1000,
                ':subtotal' => 1000
            ];

            $testStmt = $pdo->prepare($testQuery);
            $testStmt->execute($testParams);

            echo "<p style='color: green;'><strong>✓ INSERT de prueba exitoso!</strong></p>\n";
            echo "<p>La tabla y columnas funcionan correctamente.</p>\n";

            // Eliminar el registro de prueba
            $pdo->query("DELETE FROM detalle_compra WHERE id_presentacion = 1 AND id_sede = 1 AND cantidad_detalle_compra = 1 AND subtotal_detalle_compra = 1 LIMIT 1");
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>✗ Error en INSERT:</strong></p>\n";
        echo "<pre style='background: #fee; padding: 10px; border: 1px solid red;'>\n";
        echo htmlspecialchars($e->getMessage()) . "\n";
        echo "</pre>\n";
    }

    echo "<hr>\n";
    echo "<p><a href='javascript:location.reload()'>Refrescar Diagnóstico</a></p>\n";

} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Error de Conexión</h3>\n";
    echo "<pre style='background: #fee; padding: 10px; border: 1px solid red;'>\n";
    echo htmlspecialchars($e->getMessage()) . "\n";
    echo "</pre>\n";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - BD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        code {
            background: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Los resultados se imprimen arriba -->
    </div>
</body>
</html>
