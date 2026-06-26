<?php

    require_once(__DIR__ . '/../core/conexion.php');
    

    class SedesModel extends conexion {
    
        public function obtenerSedes() : array {
            $query = "SELECT * FROM sedes WHERE id_estado = 1";
            return $this->select($query);
        }

        public function crearSede(array $data) : bool {
            $query = "INSERT INTO sedes (nombre_sede, direccion_sede, responsable_sede, telefono_sede) VALUES (:nombre, :direccion, :responsable, :telefono)";
            $params = [
                ':nombre' => $data['nombreSede'],
                ':direccion' => $data['direccionSede'],
                ':responsable' => $data['responsableSede'],
                ':telefono' => $data['telefonoSede']
            ];
            return $this->execute($query, $params);
        }

        public function actualizarSede(array $data) : bool {
            $query = "UPDATE sedes SET nombre_sede = :nombre, direccion_sede = :direccion, responsable_sede = :responsable, telefono_sede = :telefono WHERE id_sede = :id";
            $params = [
                ':nombre' => $data['nombreSede'],
                ':direccion' => $data['direccionSede'],
                ':responsable' => $data['responsableSede'],
                ':telefono' => $data['telefonoSede'],
                ':id' => $data['idSede']
            ];

            return $this->execute($query, $params);
        }

        public function eliminarSede(int $id) : bool {
            $query = "UPDATE sedes SET id_estado = 2 WHERE id_sede = :id";
            $params = [':id' => $id];
            return $this->execute($query, $params);
        }

    }
    