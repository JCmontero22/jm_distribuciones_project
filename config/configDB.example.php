<?php

/**
 * CREDENCIALES DE LA BASE DE DATOS - PLANTILLA
 *
 * Copia este archivo como config/configDB.php en CADA entorno y pon sus datos.
 * config/configDB.php NO se sube a Git (guarda contraseñas).
 *
 * Local (XAMPP):   DB_HOST localhost, DB_USER root, DB_PASS '', DB_NAME jm_distribuciones
 * Producción:      datos de la base y usuario que creaste en cPanel
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'usuario_de_la_base');
define('DB_PASS', 'contraseña');
define('DB_NAME', 'nombre_de_la_base');

// Entorno: 'production' en el servidor, 'development' en XAMPP
define('APP_ENVIRONMENT', 'production');
