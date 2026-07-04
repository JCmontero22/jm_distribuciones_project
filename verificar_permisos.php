<?php
/**
 * Script para verificar/asignar permisos
 * Ejecutar: http://localhost/RUTA/verificar_permisos.php
 * ELIMINAR ESTE ARCHIVO DESPUÉS DE USAR
 */

require_once 'config/configDB.php';
require_once 'core/conexion.php';
require_once 'model/PermisosModel.php';
require_once 'model/PerfilUsuarioModel.php';

echo "<h2>Verificando Permisos del Sistema</h2>";

try {
    $conexion = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $permisosModel = new PermisosModel();
    $perfilModel = new PerfilUsuarioModel();

    // ===== SECCIÓN 1: PERMISOS EXISTENTES =====
    echo "<h3>1. Permisos Existentes en la BD</h3>";
    $permisos = $permisosModel->obtenerPermisos();

    if (empty($permisos)) {
        echo "<p style='color: red;'><strong>No hay permisos creados</strong></p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th></tr>";
        foreach ($permisos as $permiso) {
            echo "<tr>";
            echo "<td>{$permiso['id_permiso']}</td>";
            echo "<td><strong>{$permiso['nombre_permiso']}</strong></td>";
            echo "<td>{$permiso['descripcion_permiso']}</td>";
            echo "<td>" . ($permiso['id_estado'] == 1 ? '✓ Activo' : '✗ Inactivo') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // ===== SECCIÓN 2: CREAR PERMISOS FALTANTES =====
    echo "<hr>";
    echo "<h3>2. Crear Permisos Faltantes</h3>";

    $permisosRequeridos = array(
        'home' => 'Acceso al Dashboard',
        'configuracion' => 'Gestión de Configuración',
        'usuarios' => 'Gestión de Usuarios',
        'usuariosAjax' => 'API de Usuarios',
        'proveedores' => 'Gestión de Proveedores',
        'relojes' => 'Inventario de Relojes',
        'esencias' => 'Inventario de Esencias',
        'insumos' => 'Inventario de Insumos',
        'locionesAAA' => 'Inventario de Lociones AAA',
        'compras' => 'Gestión de Compras',
        'historial' => 'Ver Historial',
    );

    if (isset($_POST['crear_permisos'])) {
        $creados = 0;
        $existentes = 0;

        foreach ($permisosRequeridos as $nombre => $descripcion) {
            $permisoExiste = false;
            foreach ($permisos as $p) {
                if ($p['nombre_permiso'] === $nombre) {
                    $permisoExiste = true;
                    break;
                }
            }

            if (!$permisoExiste) {
                try {
                    $stmt = $conexion->prepare(
                        "INSERT INTO permisos (nombre_permiso, descripcion_permiso, id_estado)
                         VALUES (?, ?, 1)"
                    );
                    $stmt->execute([$nombre, $descripcion]);
                    $creados++;
                } catch (Exception $e) {
                    echo "<p style='color: orange;'>⚠ Error al crear permiso '{$nombre}': " . $e->getMessage() . "</p>";
                }
            } else {
                $existentes++;
            }
        }

        echo "<p style='color: green;'><strong>✓ {$creados} permisos creados</strong></p>";
        if ($existentes > 0) {
            echo "<p style='color: orange;'>⚠ {$existentes} permisos ya existían</p>";
        }

        // Recargar permisos
        $permisos = $permisosModel->obtenerPermisos();
    } else {
        $faltantes = 0;
        foreach ($permisosRequeridos as $nombre => $desc) {
            $existe = false;
            foreach ($permisos as $p) {
                if ($p['nombre_permiso'] === $nombre) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) $faltantes++;
        }

        if ($faltantes > 0) {
            echo "<form method='POST'>";
            echo "<p>{$faltantes} permisos faltantes. Crear automáticamente:</p>";
            echo "<button type='submit' name='crear_permisos' style='padding: 10px 20px; background: blue; color: white; border: none; cursor: pointer;'>Crear Permisos Faltantes</button>";
            echo "</form>";
        } else {
            echo "<p style='color: green;'>✓ Todos los permisos existen</p>";
        }
    }

    // ===== SECCIÓN 3: ASIGNAR PERMISOS A PERFILES =====
    echo "<hr>";
    echo "<h3>3. Asignar Permisos a Perfiles</h3>";

    $perfiles = $perfilModel->obtenerPerfiles();

    if (empty($perfiles)) {
        echo "<p style='color: red;'>No hay perfiles creados</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Perfil</th><th>Permisos Asignados</th><th>Acción</th></tr>";

        foreach ($perfiles as $perfil) {
            $permisosDelPerfil = $permisosModel->obtenerPermisosDelPerfil($perfil['id_perfil_usuario']);
            $permisosList = array_column($permisosDelPerfil, 'nombre_permiso');
            $permisosTxt = empty($permisosList) ? 'Sin permisos' : implode(', ', $permisosList);

            echo "<tr>";
            echo "<td><strong>{$perfil['nombre_perfil_usuario']}</strong></td>";
            echo "<td>{$permisosTxt}</td>";
            echo "<td>";
            echo "<form method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='idPerfil' value='{$perfil['id_perfil_usuario']}'>";
            echo "<button type='submit' name='asignar_todos' value='1' style='padding: 5px 10px; background: green; color: white; border: none; cursor: pointer;'>Asignar Todos</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Procesar asignación
        if (isset($_POST['asignar_todos'])) {
            $idPerfil = (int)$_POST['idPerfil'];

            // Eliminar permisos existentes
            $stmt = $conexion->prepare("DELETE FROM perfil_usuario_permisos WHERE id_perfil_usuario = ?");
            $stmt->execute([$idPerfil]);

            // Asignar todos los permisos
            $stmt = $conexion->prepare(
                "INSERT INTO perfil_usuario_permisos (id_perfil_usuario, id_permiso)
                 SELECT ?, id_permiso FROM permisos WHERE id_estado = 1"
            );
            $stmt->execute([$idPerfil]);

            echo "<p style='color: green;'><strong>✓ Todos los permisos asignados al perfil</strong></p>";

            // Recargar
            $perfiles = $perfilModel->obtenerPerfiles();
        }
    }

    // ===== SECCIÓN 4: VERIFICAR PERMISOS DEL USUARIO ACTUAL =====
    echo "<hr>";
    echo "<h3>4. Permisos del Usuario Actual (en sesión)</h3>";

    session_start();
    if (isset($_SESSION['usuario'])) {
        $usuario = $_SESSION['usuario'];
        echo "<p><strong>Usuario:</strong> {$usuario['nombre_usuario']}</p>";
        echo "<p><strong>Perfil:</strong> {$usuario['nombre_perfil_usuario']}</p>";

        $permisosUsuario = $permisosModel->obtenerPermisosDelPerfil($usuario['id_perfil_usuario']);

        if (empty($permisosUsuario)) {
            echo "<p style='color: red;'>Sin permisos asignados</p>";
        } else {
            echo "<ul>";
            foreach ($permisosUsuario as $p) {
                echo "<li>{$p['nombre_permiso']}</li>";
            }
            echo "</ul>";
        }

        // Botón para probar acceso a configuración
        echo "<p>";
        echo "<a href='/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/configuracion' target='_blank' style='padding: 10px 15px; background: #0066cc; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Ir a Configuración</a>";
        echo "</p>";
    } else {
        echo "<p style='color: red;'>No hay sesión activa. <a href='/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/'>Inicia sesión primero</a></p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h2, h3 { color: #333; }
    table { border-collapse: collapse; margin: 20px 0; background: white; }
    th, td { padding: 10px; text-align: left; }
    th { background: #333; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    button { border-radius: 5px; }
    form { margin: 10px 0; }
</style>
