<?php

    require_once('../model/DescuentosModel.php');
    require_once('../model/ProductosModel.php');
    require_once('../core/Logger.php');

    class DescuentosService {
        private DescuentosModel $modeloDescuento;
        private ProductosModel $modeloProducto;

        public function __construct(DescuentosModel $modeloDescuento, ProductosModel $modeloProducto) {
            $this->modeloDescuento = $modeloDescuento;
            $this->modeloProducto = $modeloProducto;
        }

        public function registrarDescuento(array $data): mixed {
            return $this->modeloDescuento->registrarDescuento($data);
        }

        public function obtenerDescuentos(): array {
            return $this->modeloDescuento->obtenerDescuentos();
        }

        public function obtenerDescuentoPorID(int $idDescuento): ?array {
            return $this->modeloDescuento->obtenerDescuentoPorID($idDescuento);
        }

        public function actualizarDescuento(int $idDescuento, array $data): bool {
            return $this->modeloDescuento->actualizarDescuento($idDescuento, $data);
        }

        public function aplicarDescuentoAProductosEspecificos(int $idDescuento, array $idsProductos): bool {
            if (empty($idsProductos)) {
                throw new DomainException('Debe especificar al menos un producto');
            }

            // Obtener IDs de presentaciones de estos productos
            $idsPresentaciones = [];
            foreach ($idsProductos as $idProducto) {
                $presentaciones = $this->modeloProducto->obtenerPresentacionesPorProducto($idProducto);
                foreach ($presentaciones as $p) {
                    $idsPresentaciones[] = $p['id_presentacion'];
                }
            }

            if (empty($idsPresentaciones)) {
                throw new DomainException('Los productos especificados no tienen presentaciones registradas');
            }

            return $this->modeloDescuento->asignarDescuentoAPresentaciones($idDescuento, $idsPresentaciones);
        }

        public function aplicarDescuentoAMarca(int $idDescuento, int $idMarca): bool {
            $idsPresentaciones = $this->modeloProducto->obtenerIdsPresentacionesPorMarca($idMarca);

            if (empty($idsPresentaciones)) {
                throw new DomainException('La marca especificada no tiene presentaciones activas');
            }

            return $this->modeloDescuento->asignarDescuentoAPresentaciones($idDescuento, $idsPresentaciones);
        }

        public function aplicarDescuentoAProductoPorGenero(int $idDescuento, int $idProducto, int $idGenero): bool {
            $idsPresentaciones = $this->modeloProducto->obtenerIdsPresentacionesPorProductoYGenero($idProducto, $idGenero);

            if (empty($idsPresentaciones)) {
                throw new DomainException('No hay presentaciones de este producto con el género especificado');
            }

            return $this->modeloDescuento->asignarDescuentoAPresentaciones($idDescuento, $idsPresentaciones);
        }

        public function aplicarDescuentoAGenero(int $idDescuento, int $idGenero): bool {
            $idsPresentaciones = $this->modeloProducto->obtenerIdsPresentacionesPorGenero($idGenero);

            if (empty($idsPresentaciones)) {
                throw new DomainException('No hay presentaciones para el género especificado');
            }

            return $this->modeloDescuento->asignarDescuentoAPresentaciones($idDescuento, $idsPresentaciones);
        }

        public function aplicarDescuentoATodos(int $idDescuento): bool {
            $idsPresentaciones = $this->modeloProducto->obtenerIdsTodosPresentaciones();

            if (empty($idsPresentaciones)) {
                throw new DomainException('No hay presentaciones activas en el sistema');
            }

            return $this->modeloDescuento->asignarDescuentoAPresentaciones($idDescuento, $idsPresentaciones);
        }

        public function removerDescuento(int $idDescuento): bool {
            $idsPresentaciones = $this->modeloProducto->obtenerIdsPresentacionesPorDescuento($idDescuento);

            if (empty($idsPresentaciones)) {
                return true;
            }

            return $this->modeloDescuento->removerDescuentoDePresentaciones($idsPresentaciones);
        }

        public function eliminarDescuento(int $idDescuento): bool {
            $descuento = $this->modeloDescuento->obtenerDescuentoPorID($idDescuento);
            if (!$descuento) {
                throw new DomainException('Descuento no encontrado');
            }

            $this->removerDescuento($idDescuento);
            return $this->modeloDescuento->eliminarDescuento($idDescuento);
        }
    }
