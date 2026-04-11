<?php 

    require_once('../model/GeneroModel.php');
    
    class GeneroService{
        
        private $modelo;

        public function __construct(GeneroModel $modelo) {
            $this->modelo = $modelo;
        }

        public function listaGeneros(){
            return $this->modelo->getGenero();
        }
    }
    