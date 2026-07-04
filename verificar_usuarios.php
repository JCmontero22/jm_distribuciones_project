<?php
/**
 * Script para verificar/crear usuario de prueba
 * Ejecutar: http://localhost/RUTA/verificar_usuarios.php
 * ELIMINAR ESTE ARCHIVO DESPUÉS DE USAR
 */

require_once 'config/configDB.php';
require_once 'core/conexion.php';

echo "<h2>Verificando Usuarios</h2>";

try {
    $conexion = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Verificar usuarios existentes
    $stmt = $conexion->query("SELECT id_usuario, nombre_usuario, user_usuario, pass_usuario, id_estado FROM usuario LIMIT 5");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Usuarios en BD:</h3>";
    if (empty($usuarios)) {
        echo "<p style='color: red;'><strong>NO HAY USUARIOS</strong></p>";
        echo "<p>Necesitas crear un usuario manualmente. Ver abajo.</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>¿Hash?</th><th>Estado</th></tr>";
        foreach ($usuarios as $user) {
            $hasheada = (strlen($user['pass_usuario']) > 20 && strpos($user['pass_usuario'], '$') === 0) ? '✓ SÍ' : '✗ NO (plano)';
            $color = strpos($hasheada, 'NO') !== false ? 'red' : 'green';
            echo "<tr>";
            echo "<td>{$user['id_usuario']}</td>";
            echo "<td>{$user['nombre_usuario']}</td>";
            echo "<td>{$user['user_usuario']}</td>";
            echo "<td style='color: {$color};'><strong>{$hasheada}</strong></td>";
            echo "<td>{$user['id_estado']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<hr>";
    echo "<h3>Crear Usuario de Prueba</h3>";

    if (isset($_POST['crear'])) {
        $usuario = 'admin';
        $password = 'admin123';
        $nombre = 'Administrador';
        $idPerfil = 1;

        $passHasheada = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Verificar si existe
            $checkStmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE user_usuario = ?");
            $checkStmt->execute([$usuario]);

            if ($checkStmt->fetch()) {
                echo "<p style='color: orange;'><strong>⚠ Usuario '{$usuario}' ya existe</strong></p>";
            } else {
                // Insertar nuevo usuario
                $stmt = $conexion->prepare(
                    "INSERT INTO usuario (nombre_usuario, user_usuario, pass_usuario, id_perfil_usuario, id_estado)
                     VALUES (?, ?, ?, ?, 1)"
                );
                $stmt->execute([$nombre, $usuario, $passHasheada, $idPerfil]);

                echo "<p style='color: green;'><strong>✓ Usuario creado exitosamente</strong></p>";
                echo "<table border='1' cellpadding='10'>";
                echo "<tr><td><strong>Usuario:</strong></td><td>{$usuario}</td></tr>";
                echo "<tr><td><strong>Contraseña:</strong></td><td>{$password}</td></tr>";
                echo "<tr><td><strong>Hash almacenado:</strong></td><td>" . substr($passHasheada, 0, 30) . "...</td></tr>";
                echo "</table>";
                echo "<p>Ahora puedes hacer <a href='/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/'>login</a></p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'><strong>Error al crear usuario:</strong> " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<form method='POST'>";
        echo "<p>Esto creará un usuario <strong>admin</strong> / <strong>admin123</strong></p>";
        echo "<button type='submit' name='crear' style='padding: 10px 20px; background: green; color: white; border: none; cursor: pointer;'>Crear Usuario</button>";
        echo "</form>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error de conexión a BD:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>MySQL está corriendo</li>";
    echo "<li>Los datos en configDB.php son correctos</li>";
    echo "<li>La BD 'jm_distribuciones' existe</li>";
    echo "</ul>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { padding: 10px; text-align: left; }
    th { background: #333; color: white; }
</style>
