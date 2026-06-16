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
            $query = "SELECT * FROM banner_promociones WHERE id_estado = 1";
            return $this->select($query);
        }

        public function obtenerBannerPorID(int $idBanner): ?array {
            $query = "SELECT * FROM banner_promociones WHERE id_banner_promocion = :idBanner AND id_estado = 1";
            $params = [':idBanner' => $idBanner];
            return $this->select($query, $params);
        }

        public function updateBannerPromocion(int $idBanner, array $data): mixed {

            if($data['imgBanner'] === null){
                $data['imgBanner'] = $this->obtenerBannerPorID($idBanner)[0]['img_banner_promocion'];
            }

            $query = "UPDATE banner_promociones SET 
                            nombre_banner_promocion = :nombreBanner, 
                            img_banner_promocion = :imgBanner, 
                            fecha_inicio = :fechaInicio, 
                            fecha_fin = :fechaFin
                        WHERE id_banner_promocion = :idBanner";
            $params = [
                ':idBanner' => $idBanner,
                ':nombreBanner' => $data['nombreBanner'],
                ':imgBanner' => $data['imgBanner'],
                ':fechaInicio' => $data['fechaInicio'],
                ':fechaFin' => $data['fechaFin']
            ];
            return $this->execute($query, $params);
        }

        public function eliminarBannerPromocion(int $idBanner): mixed {
            $query = "UPDATE banner_promociones SET id_estado = 0 WHERE id_banner_promocion = :idBanner";
            $params = [':idBanner' => $idBanner];
            return $this->execute($query, $params);
        }
    }
    