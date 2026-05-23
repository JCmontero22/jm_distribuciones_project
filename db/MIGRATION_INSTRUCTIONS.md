# Database Migrations - JM Distribuciones

## Instrucciones para Ejecutar Migraciones

Las migraciones están en la carpeta `db/migrations/`.

### Migración 001: Add id_sede to detalle_compra

**Descripción**: Agrega la columna `id_sede` a la tabla `detalle_compra` para registrar a qué sede pertenece cada detalle de compra.

**Archivos afectados**:
- `detalle_compra`: Agrega columna `id_sede` con clave foránea a `sede`

**Ejecutar migración**:

#### Opción 1: phpMyAdmin o cliente MySQL directo
```bash
# Abrir phpMyAdmin y ejecutar este comando en la base de datos distribuciones_jm:
source /path/to/db/migrations/001_add_id_sede_to_detalle_compra.sql
```

#### Opción 2: Línea de comandos
```bash
# Desde el terminal (si tienes acceso a mysql)
mysql -u usuario -p distribuciones_jm < db/migrations/001_add_id_sede_to_detalle_compra.sql
```

#### Opción 3: Script PHP (recomendado)
Crear un archivo temporal `ejecutar_migracion.php`:

```php
<?php
require_once('core/conexion.php');

$db = new conexion();

// Leer el archivo SQL
$sql = file_get_contents('db/migrations/001_add_id_sede_to_detalle_compra.sql');

// Separar múltiples consultas y ejecutar cada una
$queries = array_filter(array_map('trim', explode(';', $sql)), function($q) {
    return !empty($q) && !str_starts_with($q, '--');
});

foreach ($queries as $query) {
    try {
        $db->getDb()->exec($query . ';');
        echo "✓ Ejecutado: " . substr($query, 0, 50) . "...\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n✓ Migración completada exitosamente!\n";
?>
```

Luego ejecutar:
```bash
php ejecutar_migracion.php
```

## Estado de Migraciones

- [x] 001_add_id_sede_to_detalle_compra.sql - **Pendiente de ejecutar**

## Notas Importantes

- La columna `id_sede` es requerida (NOT NULL)
- Se agregó índice para mejorar rendimiento en queries
- Se agregó restricción de clave foránea para integridad referencial

## Verificación

Después de ejecutar la migración, verifica que la columna existe:

```sql
DESCRIBE detalle_compra;
```

Debería mostrar una nueva fila con `id_sede`.
