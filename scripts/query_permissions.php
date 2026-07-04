<?php
require_once __DIR__ . '/../config/configDB.php';
try {
    $host = (DB_HOST === 'localhost') ? '127.0.0.1' : DB_HOST;
    $dsn = "mysql:host={$host};dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "Perfiles:\n";
    $stmt = $pdo->query('SELECT id_perfil_usuario,nombre_perfil_usuario FROM perfil_usuario');
    $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($perfiles);

    echo "\nPermisos:\n";
    $stmt = $pdo->query('SELECT id_permiso,nombre_permiso FROM permisos');
    $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($permisos);

    echo "\nAsignaciones (perfil_usuario_permisos):\n";
    $stmt = $pdo->query('SELECT id_perfil_usuario,id_permiso FROM perfil_usuario_permisos');
    $asigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($asigs);

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
