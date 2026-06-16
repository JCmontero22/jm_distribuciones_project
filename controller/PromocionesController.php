<?php

require_once(__DIR__ . '/../services/PromocionesService.php');
require_once(__DIR__ . '/../core/response.php');
require_once(__DIR__ . '/../core/Logger.php');
require_once(__DIR__ . '/../core/CustomExceptions.php');
require_once(__DIR__ . '/../helper/utils.php');

class PromocionesController {
    private PromocionesService $promocionesService;

    public function __construct(PromocionesService $promocionesService) {
        $this->promocionesService = $promocionesService;
    }

    public function registrarBannerPromocion(array $data, array $files): array {
        try {
            if (!utils::validateRequiredFields(['nombreBanner', 'fechaInicio', 'fechaFin'], $data)) {
                return response::error('Todos los campos son obligatorios');
            }

            $result = $this->promocionesService->registroBanner($data, $files);
            return response::success($result, 'Banner de promoción registrado exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al registrar banner de promoción", $e, $_REQUEST);
            return response::error('Error al registrar el banner de promoción');
        }
    }

    public function obtenerBanners(): array {
        try {
            $result = $this->promocionesService->obtenerBanners();
            return response::success($result, 'Banners de promoción obtenidos exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener banners de promoción", $e, $_REQUEST);
            return response::error('Error al obtener los banners de promoción');
        }
    }

    public function obtenerBannerID(array $request): array {
        try {
            $idBanner = (int)($request['id_banner_promocion'] ?? 0);
            if (!$idBanner) {
                return response::error('ID de banner de promoción requerido');
            }

            $result = $this->promocionesService->obtenerBannerPorID($idBanner);
            if (!$result) {
                return response::error('Banner de promoción no encontrado');
            }

            return response::success($result, 'Banner de promoción obtenido exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al obtener detalle del banner de promoción", $e, $_REQUEST);
            return response::error('Error al obtener el detalle del banner de promoción');
        }
    }

    public function actualizarBannerPromocion(int $idBanner, array $data, array $files): array {
        try {
            if (!utils::validateRequiredFields(['nombreBanner', 'fechaInicio', 'fechaFin'], $data)) {
                return response::error('Todos los campos son obligatorios');
            }

            $result = $this->promocionesService->actualizarBanner($idBanner, $data, $files);
            return response::success($result, 'Banner de promoción actualizado exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al actualizar banner de promoción", $e, ['idBanner' => $idBanner, 'data' => $data]);
            return response::error('Error al actualizar el banner de promoción');
        }
    }

    public function eliminarBanner(int $idBanner): array {
        try {
            $result = $this->promocionesService->eliminarBanner($idBanner);
            return response::success($result, 'Banner de promoción eliminado exitosamente');

        } catch (\DomainException $e) {
            return response::error($e->getMessage());
        } catch (\Exception $e) {
            Logger::error("Error al eliminar banner de promoción", $e, ['idBanner' => $idBanner]);
            return response::error('Error al eliminar el banner de promoción'); 
        }
    }
}
