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
}
