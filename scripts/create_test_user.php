<?php
// Script temporal para crear un usuario de prueba
require_once __DIR__ . '/../config/configDB.php';

try {
    $host = (DB_HOST === 'localhost') ? '127.0.0.1' : DB_HOST;
    $dsn = "mysql:host={$host};dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $user = 'testuser';
    $nombre = 'Usuario de Prueba';
    $passwordPlain = 'Test1234!';
    $passHash = password_hash($passwordPlain, PASSWORD_DEFAULT);
    $idPerfil = 1; // ajustar si es necesario
    $idEstado = 1; // activo

    // Evitar duplicados
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE user_usuario = :u LIMIT 1");
    $stmt->execute([':u' => $user]);
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($exists) {
        echo "Usuario ya existe: {$user}\n";
        $stmt = $pdo->prepare("SELECT id_usuario,user_usuario,nombre_usuario,id_estado,pass_usuario FROM usuario WHERE user_usuario = :u LIMIT 1");
        $stmt->execute([':u' => $user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        print_r($row);
        exit(0);
    }

    $insert = $pdo->prepare(
        "INSERT INTO usuario (nombre_usuario,user_usuario,pass_usuario,id_perfil,id_estado) VALUES (:nombre,:user,:pass,:perfil,:estado)"
    );

    $insert->execute([
        ':nombre' => $nombre,
        ':user' => $user,
        ':pass' => $passHash,
        ':perfil' => $idPerfil,
        ':estado' => $idEstado
    ]);

    echo "Usuario creado: {$user} - password: {$passwordPlain}\n";

    $stmt = $pdo->prepare("SELECT id_usuario,user_usuario,nombre_usuario,id_estado,pass_usuario FROM usuario WHERE user_usuario = :u LIMIT 1");
    $stmt->execute([':u' => $user]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($row);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
