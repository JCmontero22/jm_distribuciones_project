<?php 

    require_once('../core/conexion.php');

    class InsumoFormulasModel extends conexion
    {
        public function listadoInsumosFormulas() : array {
            $query = "SELECT id_insumo_formula, nombre_insumo, tamanio_insumo FROM insumo_formulas WHERE id_estado = 1";
            return $this->select($query);
        }

        public function listadoTiposConcentracion() : array {
            $query = "SELECT id_tipo_concentracion, nombre_concentracion FROM tipo_concentracion WHERE id_estado = 1";
            return $this->select($query);
        }
    }
    