<?php 

require_once('../model/ComprasModel.php');
require_once('../core/Logger.php');

class ComprasService {
	private ComprasModel $model;

	public function __construct(ComprasModel $model) {
		$this->model = $model;
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

	/**
	 * Edita la compra: revierte lo que dejo (stock, lotes, kardex, caja) y vuelve a aplicar los detalles nuevos, todo en una transaccion.
	 */
	public function actualizarCompraConDetalles(int $idCompra, array $data, array $detalles): mixed {
		try {
			return $this->model->ejecutarEnTransaccion(function () use ($idCompra, $data, $detalles) {
				$idSede = (int)$data['idSede'];
				$idUsuario = $data['idUsuario'] ?? null;
				$subtotalCompra = $this->asignarSedeDetalles($detalles, $idSede);

				$this->model->revertirEfectosCompra($idCompra);
				$this->model->actualizarCompra($idCompra, $data);
				$this->model->eliminarDetallesCompra($idCompra);
				$this->model->registrarDetalleCompra($idCompra, $detalles, $idUsuario);
				$this->model->registrarEgresoCaja($idCompra, $idSede, $subtotalCompra, $idUsuario);

				return ['id_compras' => $idCompra];
			});
		} catch (\Throwable $e) {
			Logger::error("Error al actualizar compra", $e, ['idCompra' => $idCompra]);
			throw $e;
		}
	}

	/**
	 * Elimina la compra revirtiendo su stock, lotes, kardex y caja.
	 */
	public function eliminarCompra(int $idCompra): mixed {
		return $this->model->ejecutarEnTransaccion(fn() => $this->model->eliminarCompra($idCompra));
	}

	/**
	 * Todos los detalles deben ser de la sede del encabezado. Devuelve el subtotal de la compra.
	 */
	private function asignarSedeDetalles(array &$detalles, int $idSede): float {
		$subtotal = 0.0;
		foreach ($detalles as &$item) {
			$item['idSede'] = $item['idSede'] ?? $idSede;
			if ((int)$item['idSede'] !== $idSede) {
				throw new \DomainException('Todos los productos de la compra deben ser de la misma sede');
			}
			$subtotal += (float)$item['subTotal'];
		}
		unset($item);
		return $subtotal;
	}

	/**
	 * Registra la compra (por sede), sus detalles, el stock/lotes/kardex y el egreso de caja en una sola transacción.
	 */
	public function registrarCompraConDetalles(array $data, array $detalles): mixed {
		try {
			return $this->model->ejecutarEnTransaccion(function () use ($data, $detalles) {
				$idSede = (int)$data['idSede'];
				$idUsuario = $data['idUsuario'] ?? null;
				$subtotalCompra = $this->asignarSedeDetalles($detalles, $idSede);

				$idCompra = (int)$this->model->registrarCompra($data);
				$this->model->registrarDetalleCompra($idCompra, $detalles, $idUsuario);
				$this->model->registrarEgresoCaja($idCompra, $idSede, $subtotalCompra, $idUsuario);

				return [ 'id_compras' => $idCompra ];
			});
		} catch (\Exception $e) {
			Logger::error("Error al registrar compra y detalles", $e, ['data' => $data, 'detalles' => $detalles]);
			throw $e;
		}
	}

}