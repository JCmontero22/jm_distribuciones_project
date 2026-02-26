<?php 

    require_once('../model/CategoriaModel.php');
    
    class CategoriaServices{
        
        private $modelo;

        public function __construct(CategoriaModel $modelo) {
            $this->modelo = $modelo;
        }

        public function listaCategorias(){
            return $this->modelo->getCategorias();
        }
    }
    