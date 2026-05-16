<?php

    require_once('../config/configDB.php');
    require_once('../core/Logger.php');
    
    class conexion {
        protected $db;

        public function __construct() {
            $this->db = $this->conect();
        }

        private function conect(): PDO{
            try {
                $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
                return $pdo;
            } catch (PDOException $e) {
                Logger::error("Error de conexión a la base de datos",$e);
                throw new Exception('Error de conexión a la base de datos: ');
            }
        }

        protected function execute(string $sql, array $params = []){
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);

                if (stripos(trim($sql), 'INSERT') === 0) {
                    return $this->db->lastInsertId();
                }

                return $stmt->rowCount();

            } catch (PDOException $e) {
                Logger::error("Error en el execute SQL",$e,['sql' => $sql,'params' => $params]);
                throw new Exception('Error en la consulta a la base de datos: ');
            }
        }

        protected function select($sql, $params = []){
            try {
                $query = $this->db->prepare($sql);
                $query->execute($params);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                Logger::error("Error en consulta SELECT",$e,['sql' => $sql,'params' => $params]);
                throw new Exception('Error en la consulta a la base de datos: ');
            }
        }
    }
    