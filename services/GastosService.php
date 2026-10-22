<?php

require_once(__DIR__ . '/../model/GastosModel.php');

class GastosService {
    private GastosModel $gastosModel;

    public function __construct(GastosModel $gastosModel) {
        $this->gastosModel = $gastosModel;
    }

    public function registrarGasto(array $data): array {
        if ($data['idSede'] <= 0) {
            throw new \DomainException('Sede inválida');
        }
        if ($data['categoriaGasto'] === '') {
            throw new \DomainException('La categoría del gasto es obligatoria');
        }
        if ($data['montoGasto'] <= 0) {
            throw new \DomainException('El monto del gasto debe ser mayor a 0');
        }
        if ($data['idUsuario'] <= 0) {
            throw new \DomainException('Sesión no válida. Inicia sesión nuevamente.');
        }

        $fecha = \DateTime::createFromFormat('Y-m-d', $data['fechaGasto']);
        if (!$fecha || $fecha->format('Y-m-d') !== $data['fechaGasto']) {
            throw new \DomainException('La fecha del gasto no es válida');
        }

        $idGasto = $this->gastosModel->registrarGasto($data);
        return ['id_gasto' => $idGasto];
    }

    public function listarGastos(int $idSede, string $desde, string $hasta): array {
        if ($idSede <= 0) {
            throw new \DomainException('Sede inválida');
        }
        return $this->gastosModel->obtenerGastos($idSede, $desde, $hasta);
    }
}
