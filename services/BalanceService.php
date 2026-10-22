<?php

require_once(__DIR__ . '/../model/BalanceModel.php');

class BalanceService {
    private BalanceModel $balanceModel;

    public function __construct(BalanceModel $balanceModel) {
        $this->balanceModel = $balanceModel;
    }

    /**
     * Balance de un mes por sede (o de una sede), con totales.
     * Reglas:
     *  - facturado      = total de las ventas del mes (sin anuladas), se cobren o no ese mes
     *  - ganancia bruta = facturado - costo de lo vendido
     *  - ganancia neta  = ganancia bruta - gastos del mes
     *  - para reinvertir = costo de lo vendido (la base que se debe volver a comprar)
     *  - reinvertido    = compras del mes
     *  - queda          = para reinvertir - reinvertido
     */
    public function balanceMensual(string $mes, ?int $idSede = null): array {
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mes)) {
            throw new \DomainException('Mes inválido. Usa el formato AAAA-MM');
        }

        $filas = [];
        foreach ($this->balanceModel->obtenerSedes($idSede) as $s) {
            $filas[$s['id_sede']] = [
                'id_sede' => (int)$s['id_sede'],
                'nombre_sede' => $s['nombre_sede'],
                'cantidad_ventas' => 0,
                'facturado' => 0.0,
                'costo_ventas' => 0.0,
                'recaudado' => 0.0,
                'por_cobrar' => 0.0,
                'gastos' => 0.0,
                'compras' => 0.0
            ];
        }

        $this->acumular($filas, $this->balanceModel->ventasDelMes($mes, $idSede), ['cantidad_ventas', 'facturado', 'costo_ventas']);
        $this->acumular($filas, $this->balanceModel->recaudoDelMes($mes, $idSede), ['recaudado']);
        $this->acumular($filas, $this->balanceModel->porCobrarDelMes($mes, $idSede), ['por_cobrar']);
        $this->acumular($filas, $this->balanceModel->gastosDelMes($mes, $idSede), ['gastos']);
        $this->acumular($filas, $this->balanceModel->comprasDelMes($mes, $idSede), ['compras']);

        $sedes = [];
        foreach ($filas as $f) {
            $sedes[] = $this->calcularDerivados($f);
        }

        return [
            'mes' => $mes,
            'sedes' => $sedes,
            'totales' => $this->totales($sedes),
            'gastos_por_categoria' => $this->balanceModel->gastosPorCategoria($mes, $idSede)
        ];
    }

    private function acumular(array &$filas, array $registros, array $campos): void {
        foreach ($registros as $r) {
            $id = (int)$r['id_sede'];
            if (!isset($filas[$id])) {
                continue;
            }
            foreach ($campos as $c) {
                $filas[$id][$c] = is_int($filas[$id][$c]) ? (int)$r[$c] : (float)$r[$c];
            }
        }
    }

    private function calcularDerivados(array $f): array {
        $f['ganancia_bruta'] = round($f['facturado'] - $f['costo_ventas'], 2);
        $f['ganancia_neta'] = round($f['ganancia_bruta'] - $f['gastos'], 2);
        $f['para_reinvertir'] = round($f['costo_ventas'], 2);
        $f['reinvertido'] = round($f['compras'], 2);
        $f['queda'] = round($f['para_reinvertir'] - $f['reinvertido'], 2);
        return $f;
    }

    private function totales(array $sedes): array {
        $campos = ['cantidad_ventas', 'facturado', 'costo_ventas', 'ganancia_bruta', 'recaudado', 'por_cobrar',
                   'gastos', 'ganancia_neta', 'para_reinvertir', 'compras', 'reinvertido', 'queda'];
        $t = array_fill_keys($campos, 0);
        foreach ($sedes as $s) {
            foreach ($campos as $c) {
                $t[$c] += $s[$c];
            }
        }
        return $t;
    }
}
