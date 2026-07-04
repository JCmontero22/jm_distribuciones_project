<?php
require_once __DIR__ . '/../config/configDB.php';
try {
    $host = (DB_HOST === 'localhost') ? '127.0.0.1' : DB_HOST;
    $dsn = "mysql:host={$host};dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $idPerfil = 1;
    $idPermiso = 1; // configuraciones-Prueba

    // Verificar si ya existe
    $stmt = $pdo->prepare('SELECT * FROM perfil_usuario_permisos WHERE id_perfil_usuario = :p AND id_permiso = :perm');
    $stmt->execute([':p' => $idPerfil, ':perm' => $idPermiso]);
    if ($stmt->fetch()) {
        echo "Asignación ya existe\n";
    } else {
        $ins = $pdo->prepare('INSERT INTO perfil_usuario_permisos (id_perfil_usuario,id_permiso) VALUES (:p,:perm)');
        $ins->execute([':p' => $idPerfil, ':perm' => $idPermiso]);
        echo "Permiso asignado\n";
    }

    // Mostrar asignaciones
    $stmt = $pdo->query('SELECT id_perfil_usuario,id_permiso FROM perfil_usuario_permisos');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
