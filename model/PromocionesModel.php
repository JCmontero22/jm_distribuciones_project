<?php 

    require_once(__DIR__ . '/../core/conexion.php');

    class PromocionesModel extends conexion 
    {
        public function registrarBannerPromocion(array $data): mixed {
            $query = "INSERT INTO banner_promociones (
                            nombre_banner_promocion, 
                            img_banner_promocion, 
                            fecha_inicio, 
                            fecha_fin
                        )VALUES (
                            :nombreBanner, 
                            :imgBanner, 
                            :fechaInicio, 
                            :fechaFin
                        )";
            $params = [
                ':nombreBanner' => $data['nombreBanner'],
                ':imgBanner' => $data['imgBanner'],
                ':fechaInicio' => $data['fechaInicio'],
                ':fechaFin' => $data['fechaFin']
            ];
            return $this->execute($query, $params);
        }

        public function obtenerBanners(): array {
            $query = "SELECT * FROM banner_promociones";
            return $this->select($query);
        }
    }
    