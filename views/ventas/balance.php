<section class="content-panel" id="moduleBalance">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h1>Balance mensual</h1>
            <p class="subtitulo-seccion">Cómo fue el mes en cada sede: ganancias netas de gastos y cuánto queda para reinvertir después de las compras.</p>
        </div>
        <div class="col-md-5 d-flex gap-3 justify-content-end align-items-end flex-wrap">
            <div>
                <label for="filtroMesBalance" class="form-label">Mes</label>
                <input type="month" class="form-control" id="filtroMesBalance">
            </div>
            <div>
                <label for="filtroSedeBalance" class="form-label">Sede</label>
                <select class="form-select" id="filtroSedeBalance"></select>
            </div>
            <button class="btn btnRegistro px-4" id="btnActualizarBalance">
                <i class="fa-solid fa-rotate me-1"></i> Actualizar
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4 align-items-stretch">
        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-potencial"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    FACTURADO DEL MES
                </h3>
                <h3 id="balFacturado">$ 0</h3>
                <span class="ventas-kpi-sub">Total vendido en el mes</span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-ganancia"><i class="fa-solid fa-coins"></i></span>
                    COBRADO DEL MES
                </h3>
                <h3 id="balRecaudado" class="text-success">$ 0</h3>
                <span class="ventas-kpi-sub">Pagos recibidos efectivamente</span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-inversion"><i class="fa-solid fa-clock"></i></span>
                    POR COBRAR DEL MES
                </h3>
                <h3 id="balPorCobrar" class="text-warning">$ 0</h3>
                <span class="ventas-kpi-sub">Saldo pendiente de ventas del mes</span>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-inversion"><i class="fa-solid fa-arrows-rotate"></i></span>
                    PARA REINVERTIR
                </h3>
                <h3 id="balParaReinvertir" class="text-info">$ 0</h3>
                <span class="ventas-kpi-sub">Costo de lo vendido en el mes</span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-ventas"><i class="fa-solid fa-cart-shopping"></i></span>
                    REINVERTIDO
                </h3>
                <h3 id="balReinvertido">$ 0</h3>
                <span class="ventas-kpi-sub">Compras registradas en el mes</span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-potencial"><i class="fa-solid fa-scale-balanced"></i></span>
                    QUEDA TRAS REINVERTIR
                </h3>
                <h3 id="balQueda">$ 0</h3>
                <span class="ventas-kpi-sub">Para reinvertir menos compras</span>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-ganancia"><i class="fa-solid fa-chart-line"></i></span>
                    GANANCIA BRUTA
                </h3>
                <h3 id="balGananciaBruta">$ 0</h3>
                <span class="ventas-kpi-sub">Facturado menos costo de venta</span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-gasto"><i class="fa-solid fa-receipt"></i></span>
                    GASTOS DEL MES
                </h3>
                <h3 id="balGastos" class="text-danger">$ 0</h3>
                <span class="ventas-kpi-sub">Gastos operativos registrados</span>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-potencial"><i class="fa-solid fa-trophy"></i></span>
                    GANANCIA NETA DEL MES
                </h3>
                <h3 id="balGananciaNeta" class="text-warning">$ 0</h3>
                <span class="ventas-kpi-sub">Ganancia bruta menos gastos</span>
            </div>
        </div>
    </div>

    <h3 class="mb-3">Detalle por sede</h3>
    <div class="table-responsive mb-5">
        <table id="tablaBalance" class="table table-dark table-striped table-hover table-bordered custom-table text-center align-middle">
            <thead class="text-center">
                <tr>
                    <th>Sede</th>
                    <th>Ventas</th>
                    <th>Facturado</th>
                    <th>Costo</th>
                    <th>Ganancia bruta</th>
                    <th>Gastos</th>
                    <th>Ganancia neta</th>
                    <th>Reinvertido</th>
                    <th>Queda</th>
                </tr>
            </thead>
            <tbody id="tablaBalanceBody"></tbody>
            <tfoot id="tablaBalanceFoot"></tfoot>
        </table>
    </div>

    <div class="row g-4 align-items-stretch">
        <div class="col-md-6">
            <h3 class="mb-3">Gastos por categoría</h3>
            <div class="table-responsive">
                <table id="tablaGastosCategoria" class="table table-dark table-striped table-hover table-bordered custom-table text-center align-middle">
                    <thead class="text-center">
                        <tr>
                            <th>Categoría</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="tablaGastosCategoriaBody"></tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h3 class="mb-3">Cómo se calcula</h3>
            <div class="ventas-resumen-box">
                <ul class="lista-explicacion-balance m-0">
                    <li><strong>Facturado:</strong> total de las ventas del mes, se cobren ese mes o después (sin anuladas).</li>
                    <li><strong>Ganancia bruta:</strong> facturado menos el costo de lo vendido.</li>
                    <li><strong>Para reinvertir:</strong> el costo de lo vendido, es decir, la base que hay que volver a comprar.</li>
                    <li><strong>Reinvertido:</strong> compras registradas en el mes.</li>
                    <li><strong>Queda:</strong> para reinvertir menos lo reinvertido.</li>
                    <li><strong>Ganancia neta:</strong> ganancia bruta menos los gastos del mes. Es tu ganancia real, aparte de la base.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
