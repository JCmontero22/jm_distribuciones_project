<?php 

    require_once('../core/response.php');
    require_once('../helper/utils.php');
    require_once('../core/Logger.php');
    require_once('../services/ComprasService.php');

    class ComprasController
    {
        private ComprasService $servicio;

        public function __construct(ComprasService $servicio)
        {
            $this->servicio = $servicio;
        }

        public function listar(): array {
            try {
                $compras = $this->servicio->obtenerCompras();
                return response::success($compras, 'Compras obtenidas exitosamente');
            } catch (\Exception $e) {
                Logger::error("Error al listar compras", $e, []);
                return response::error('Error al obtener compras');
            }
        }

        public function registrar(array $request): array {
            try {

                if (!utils::validateRequiredFields(['idProveedor', 'totalCompra', 'numeroFacturaCompra'], $request)) {
                    return response::error('Todos los campos son obligatorios');
                }

                $params = [
                    'idProveedor' => utils::sanitizeInput($request['idProveedor']),
                    'totalCompra' => utils::sanitizeInput($request['totalCompra']),
                    'numeroFacturaCompra' => utils::sanitizeInput($request['numeroFacturaCompra'])
                ];

                $data = $this->servicio->registrarCompra($params);
                return response::success($data, 'Compra registrada exitosamente');

            } catch (\DomainException $e) {
                return response::error($e->getMessage());
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Registro de Compra", $e, $request);
                return response::error('Error al registrar la compra');
            }
        }

        public function registrarDetalles(array $request): array {
            try {
                if (!utils::validateRequiredFields(['idCompra', 'detalles'], $request)) {
                    return response::error('Campos obligatorios: idCompra, detalles');
                }

                $idCompra = utils::sanitizeInput($request['idCompra']);
                $detalles = json_decode($request['detalles'], true);

                if (!is_array($detalles) || empty($detalles)) {
                    return response::error('Detalles inválidos');
                }

                // Normalizar nombres de campos en detalles
                $detallesNormalizados = [];
                foreach ($detalles as $detalle) {
                    $detallesNormalizados[] = [
                        'idSede' => (int) $detalle['idSede'],
                        'idProductoPresentacion' => $detalle['idProducto'],
                        'cantidad' => (int) $detalle['cantidad'],
                        'precioCompra' => (float) $detalle['costoUnitario'],
                        'subTotal' => (float) $detalle['subTotal']
                    ];
                }

                $this->servicio->registrarDetalleCompra($idCompra, $detallesNormalizados);
                return response::success([], 'Detalles registrados exitosamente');

            } catch (\DomainException $e) {
                return response::error($e->getMessage());
            } catch (\Exception $e) {
                Logger::error("Error interno en el Controlador de Detalles de Compra", $e, $request);
                return response::error('Error al registrar los detalles');
            }
        }
    }
    