<?php 

require_once('../model/ComprasModel.php');
require_once('../core/Logger.php');

class ComprasService {
	private ComprasModel $model;

	public function __construct(ComprasModel $model) {
		$this->model = $model;
	}

	public function registrarCompra(array $data): mixed {
		$idCompra = $this->model->registrarCompra($data);
		return [
			'id_compras' => $idCompra,
		];
	}

	public function registrarDetalleCompra(int $idCompra, array $detalle): mixed {
		$this->model->registrarDetalleCompra($idCompra, $detalle);
		return true;
	}

	public function obtenerCompras(): array {
		return $this->model->obtenerCompras();
	}

	public function obtenerDetalleCompra(int $idCompra): array {
		return $this->model->obtenerDetalleCompra($idCompra);
	}

	public function obtenerCompraPorId(int $idCompra): ?array {
		return $this->model->obtenerCompraPorId($idCompra);
	}

	public function actualizarCompraConDetalles(int $idCompra, array $data, array $detalles): mixed {
		try {
			$this->model->actualizarCompra($idCompra, $data);
			$this->model->eliminarDetallesCompra($idCompra);
			$this->model->registrarDetalleCompra($idCompra, $detalles);
			return ['id_compras' => $idCompra];
		} catch (\Exception $e) {
			Logger::error("Error al actualizar compra", $e, ['idCompra' => $idCompra]);
			throw $e;
		}
	}

	public function eliminarCompra(int $idCompra): mixed {
		return $this->model->eliminarCompra($idCompra);
	}

	/**
	 * Registra la compra y sus detalles en una sola transacción
	 */
	public function registrarCompraConDetalles(array $data, array $detalles): mixed {
		try {
			$idCompra = $this->model->registrarCompra($data);
			$this->model->registrarDetalleCompra($idCompra, $detalles);
			return [ 'id_compras' => $idCompra ];
		} catch (\Exception $e) {
			Logger::error("Error al registrar compra y detalles", $e, ['data' => $data, 'detalles' => $detalles]);
			throw $e;
		}
	}

}