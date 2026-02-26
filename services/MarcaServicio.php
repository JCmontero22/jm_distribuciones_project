<?php 

    require_once('../model/MarcaModel.php');

    class MarcaService {
        
        private $modelo;
        
        public function __construct(MarcaModel $modelo) {
            $this->modelo = $modelo;
        }
        
        public function listadoMarcas() {
            return $this->modelo->getMarcas();
        }
        
    }
    