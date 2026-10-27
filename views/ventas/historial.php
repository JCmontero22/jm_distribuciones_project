<section class="content-panel" id="moduleHistorial">
    <div class="row align-items-center mb-4">
        <div class="col-md-12">
            <h1>Historial de ventas</h1>
            <p class="subtitulo-seccion">Todas las ventas con su costo, ganancia, pagos y saldo. Filtra por sede y por fechas.</p>
        </div>
    </div>

    <div class="ventas-filtros-panel mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="filtroSedeHistorial" class="form-label">Sede</label>
                <select class="form-select" id="filtroSedeHistorial"></select>
            </div>
            <div class="col-md-2">
                <label for="filtroDesdeHistorial" class="form-label">Desde</label>
                <input type="date" class="form-control" id="filtroDesdeHistorial">
            </div>
            <div class="col-md-2">
                <label for="filtroHastaHistorial" class="form-label">Hasta</label>
                <input type="date" class="form-control" id="filtroHastaHistorial">
            </div>
            <div class="col-md-2">
                <label for="filtroEstadoHistorial" class="form-label">Estado</label>
                <select class="form-select" id="filtroEstadoHistorial">
                    <option value="">Todos</option>
                    <option value="pagada">Pagada</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="anulada">Anulada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filtroTextoHistorial" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="filtroTextoHistorial" placeholder="N°, cliente o vendedor">
            </div>
            <div class="col-md-2">
                <button class="btn btnRegistro w-100" id="btnFiltrarHistorial">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Filtrar
                </button>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4 align-items-stretch">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-ventas"><i class="fa-solid fa-bag-shopping"></i></span>
                    VENTAS ACTIVAS
                </h3>
                <h3 id="resCantidadHistorial">0</h3>
                <span class="ventas-kpi-sub">Ventas válidas en el período</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-potencial"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    TOTAL FACTURADO
                </h3>
                <h3 id="resFacturadoHistorial">$ 0</h3>
                <span class="ventas-kpi-sub">Monto bruto vendido</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-inversion"><i class="fa-solid fa-boxes-stacked"></i></span>
                    COSTO DE VENTAS
                </h3>
                <h3 id="resCostoHistorial">$ 0</h3>
                <span class="ventas-kpi-sub">Costo base de mercancía</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-ganancia"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                    GANANCIA BRUTA
                </h3>
                <h3 id="resGananciaHistorial" class="text-success">$ 0</h3>
                <span class="ventas-kpi-sub">Facturado menos costo</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-inversion"><i class="fa-solid fa-coins"></i></span>
                    TOTAL COBRADO
                </h3>
                <h3 id="resCobradoHistorial" class="text-info">$ 0</h3>
                <span class="ventas-kpi-sub">Ingresos recibidos en caja</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card-information card-information--sm">
                <h3>
                    <span class="icon icon-potencial"><i class="fa-solid fa-clock"></i></span>
                    SALDO POR COBRAR
                </h3>
                <h3 id="resPorCobrarHistorial" class="text-warning">$ 0</h3>
                <span class="ventas-kpi-sub">Créditos pendientes de pago</span>
            </div>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table id="tablaHistorial" class="table table-dark table-striped table-hover table-bordered custom-table text-center align-middle">
            <thead class="text-center">
                <tr>
                    <th>N°</th>
                    <th>Fecha</th>
                    <th>Sede</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Total</th>
                    <th>Costo</th>
                    <th>Ganancia</th>
                    <th>Pagado</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tablaHistorialBody"></tbody>
        </table>
    </div>
</section>

<div class="modal fade" id="modalDetalleHistorial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="tituloDetalleHistorial">Detalle de venta</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div id="cuerpoDetalleHistorial"></div>
            </div>
        </div>
    </div>
</div>
