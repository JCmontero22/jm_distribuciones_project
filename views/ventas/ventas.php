<section class="content-panel" id="moduleVentas">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1>Ventas</h1>
            <p class="text-muted">Control de facturación, ventas de contado y crédito, abonos y descuento de inventario por sede.</p>
        </div>

        <div class="col-md-4 d-flex justify-content-end gap-3 flex-wrap">
            <button type="button" class="btn btn-outline-light btn-ventas-secundario" id="btnModalCliente" data-bs-toggle="modal" data-bs-target="#modalRegistroCliente">
                <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
            </button>
            <button type="button" class="btn btn-new-product" id="btnModalVenta" data-bs-toggle="modal" data-bs-target="#modalRegistroVenta">
                <i class="fa-solid fa-cash-register"></i> Nueva Venta
            </button>
        </div>
    </div>

    <!-- TARJETAS RESUMEN (KPIs) -->
    <div class="row mt-5 g-4 align-items-stretch">
        <div class="col-md-3">
            <div class="card-information">
                <h3>
                    <span class="icon icon-ventas"><i class="fa-solid fa-bag-shopping"></i></span>
                    VENTAS HOY
                </h3>
                <h3 id="kpiVentasHoy">$ 0</h3>
                <span class="ventas-kpi-sub" id="kpiCantidadHoy">0 ventas registradas hoy</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-information">
                <h3>
                    <span class="icon icon-potencial"><i class="fa-solid fa-coins"></i></span>
                    TOTAL RECAUDADO
                </h3>
                <h3 id="kpiTotalRecaudado">$ 0</h3>
                <span class="ventas-kpi-sub" id="kpiTotalFacturado">Facturado: $ 0</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-information">
                <h3>
                    <span class="icon icon-inversion"><i class="fa-solid fa-clock"></i></span>
                    POR COBRAR
                </h3>
                <h3 id="kpiPorCobrar">$ 0</h3>
                <span class="ventas-kpi-sub" id="kpiVentasPendientes">0 ventas a crédito</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-information">
                <h3>
                    <span class="icon icon-ganancia"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                    GANANCIA ESTIMADA
                </h3>
                <h3 id="kpiGananciaTotal">$ 0</h3>
                <span class="ventas-kpi-sub" id="kpiCostoTotal">Costo: $ 0</span>
            </div>
        </div>
    </div>
</section>

<?php $esVendedor = !empty(Auth::sedeUsuario()); ?>
<!-- FILTROS Y LISTADO DE VENTAS -->
<section class="content-list-ventas container">
    <div class="ventas-filtros-panel mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5" id="wrapperFiltroSedeVentas" <?= $esVendedor ? 'style="display: none;"' : '' ?>>
                <label for="filtroSedeVentas" class="form-label">Filtrar por Sede</label>
                <select class="form-select" id="filtroSedeVentas">
                    <option value="">Todas las sedes</option>
                </select>
            </div>
            <div class="<?= $esVendedor ? 'col-md-9' : 'col-md-5' ?>" id="wrapperFiltroEstadoVentas">
                <label for="filtroEstadoVentas" class="form-label">Estado de Venta</label>
                <select class="form-select" id="filtroEstadoVentas">
                    <option value="">Todos los estados</option>
                    <option value="pagada">Pagadas</option>
                    <option value="pendiente">Pendientes (Crédito)</option>
                    <option value="anulada">Anuladas</option>
                </select>
            </div>
            <div class="<?= $esVendedor ? 'col-md-3' : 'col-md-2' ?> d-flex justify-content-end" id="wrapperBtnRefrescarVentas">
                <button type="button" class="btn btnRegistro w-100" id="btnRefrescarVentas">
                    <i class="fa-solid fa-rotate me-1"></i> Actualizar
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="tablaVentas" class="table table-striped table-hover table-bordered custom-table table-dark text-center">
            <thead class="text-center">
                <tr>
                    <th>ID</th>
                    <th>N° Venta</th>
                    <th>Sede</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Pagado</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>


<!-- ==============================================================
     MODAL 1: REGISTRAR NUEVA VENTA
     ============================================================== -->
<div class="modal fade" id="modalRegistroVenta" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRegistroVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalRegistroVentaLabel">
                        <i class="fa-solid fa-cash-register me-2 text-warning"></i>Registrar Nueva Venta
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formRegistroVenta" autocomplete="off">
                    <!-- ENCABEZADO DE LA VENTA -->
                    <div class="row g-3">
                        <div class="col-md-3" id="wrapperSedeVenta" <?= $esVendedor ? 'style="display: none;"' : '' ?>>
                            <label for="selectSedeVenta" class="form-label">Sede *</label>
                            <select class="form-select" name="idSede" id="selectSedeVenta" required>
                                <option value="">Seleccione sede</option>
                            </select>
                        </div>

                        <div class="<?= $esVendedor ? 'col-md-4' : 'col-md-3' ?>" id="wrapperNumeroVenta">
                            <label for="numeroVenta" class="form-label">N° de Venta / Factura *</label>
                            <input type="text" name="numeroVenta" id="numeroVenta" class="form-control" placeholder="Ej: VTA-0001" required>
                        </div>

                        <div class="<?= $esVendedor ? 'col-md-5' : 'col-md-4' ?>" id="wrapperClienteVenta">
                            <label for="selectClienteVenta" class="form-label">Cliente</label>
                            <div class="d-flex gap-2">
                                <select class="form-select" name="idCliente" id="selectClienteVenta">
                                    <option value="">Consumidor Final (Opcional)</option>
                                </select>
                                <button type="button" class="btn btnRegistro px-3" id="btnNuevoClienteRapido" title="Registrar nuevo cliente">
                                    <i class="fa-solid fa-user-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="<?= $esVendedor ? 'col-md-3' : 'col-md-2' ?>" id="wrapperModalidadVenta">
                            <label for="selectModalidadVenta" class="form-label">Modalidad *</label>
                            <select class="form-select" id="selectModalidadVenta" name="modalidadVenta">
                                <option value="contado">Contado</option>
                                <option value="credito">Crédito / Abono</option>
                            </select>
                        </div>
                    </div>

                    <!-- AGREGAR PRODUCTO AL DETALLE -->
                    <div class="ventas-item-box mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="modal-title m-0">
                                <i class="fa-solid fa-cart-plus me-2 text-warning"></i>Agregar Producto a la Venta
                            </h5>
                            <div id="infoStockProductoSeleccionado" class="ventas-stock-indicator" style="display: none;">
                                <span id="badgeStockDisponible" class="stock-pill"></span>
                                <span id="badgeDescuentoProducto" class="stock-pill stock-pill-gold" style="display: none;"></span>
                            </div>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="selectCategoriaVenta" class="form-label">Categoría</label>
                                <select class="form-select" id="selectCategoriaVenta">
                                    <option value="">Todas las categorías</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="selectProductoVenta" class="form-label">Producto / Presentación *</label>
                                <select class="form-select" id="selectProductoVenta">
                                    <option value="">Seleccione sede primero</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <label for="cantidadVenta" class="form-label">Cant. *</label>
                                <input type="number" id="cantidadVenta" class="form-control text-center" placeholder="1" min="1" value="1">
                            </div>

                            <div class="col-md-2">
                                <label for="precioUnitarioVenta" class="form-label">Precio Unit. *</label>
                                <input type="text" id="precioUnitarioVenta" class="form-control" placeholder="$ 0" data-format-miles>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btnRegistro w-100" id="btnAgregarItemVenta">
                                    <i class="fa-solid fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TABLA DE ÍTEMS AGREGADOS Y RESUMEN -->
                    <div id="detallesVentaContainer" style="display: none; margin-top: 2.5rem;">
                        <h5 class="mb-3">Ítems en el carrito de venta</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm custom-table table-dark">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Categoría / Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaItemsVenta"></tbody>
                            </table>
                        </div>

                        <!-- PANEL DE LIQUIDACIÓN Y PAGO -->
                        <div class="row g-4 mt-2 align-items-stretch">
                            <div class="col-md-7">
                                <div class="ventas-resumen-box h-100">
                                    <h5 class="mb-3"><i class="fa-solid fa-wallet me-2 text-warning"></i>Datos de Pago y Observaciones</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="selectMetodoPagoVenta" class="form-label">Método de Pago *</label>
                                            <select class="form-select" name="idMetodoPago" id="selectMetodoPagoVenta">
                                                <option value="">Seleccione método</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="montoPagoInicial" class="form-label" id="labelMontoPagoInicial">Monto Pagado *</label>
                                            <input type="text" name="montoPagoInicial" id="montoPagoInicial" class="form-control" placeholder="0" data-format-miles>
                                        </div>

                                        <div class="col-md-12">
                                            <label for="observacionVenta" class="form-label">Observación (Opcional)</label>
                                            <input type="text" name="observacionVenta" id="observacionVenta" class="form-control" placeholder="Notas adicionales de la venta...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="ventas-resumen-box ventas-totales-card h-100">
                                    <div class="ventas-total-row">
                                        <span>Subtotal:</span>
                                        <strong id="resumenSubtotalVenta">$ 0</strong>
                                    </div>

                                    <div class="ventas-total-row align-items-center">
                                        <span>Descuento ($):</span>
                                        <div style="width: 150px;">
                                            <input type="text" name="descuentoVenta" id="inputDescuentoVenta" class="form-control form-control-sm text-end" placeholder="0" value="0" data-format-miles>
                                        </div>
                                    </div>

                                    <hr class="border-secondary my-2">

                                    <div class="ventas-total-row ventas-total-principal">
                                        <span>TOTAL A PAGAR:</span>
                                        <strong id="resumenTotalVenta">$ 0</strong>
                                    </div>

                                    <div class="ventas-total-row mt-2">
                                        <span>Saldo Pendiente:</span>
                                        <strong id="resumenSaldoPendiente" class="text-success">$ 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center mt-4">
                            <div class="col-md-5">
                                <button type="submit" class="btn btn-primary btnRegistro w-100" id="btnConfirmarVenta">
                                    <i class="fa-regular fa-floppy-disk"></i> Confirmar y Registrar Venta
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- ==============================================================
     MODAL 2: VER DETALLE DE VENTA Y ABONOS
     ============================================================== -->
<div class="modal fade" id="modalDetalleVenta" tabindex="-1" aria-labelledby="modalDetalleVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalDetalleVentaLabel">
                        <i class="fa-solid fa-file-invoice-dollar me-2 text-warning"></i>Detalle de Venta <span id="detalleNumeroVentaTitulo" class="text-warning"></span>
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- CABECERA INFORMATIVA -->
                <div class="ventas-resumen-box mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted">Sede</label>
                            <p id="detalleVentaSede" class="fw-bold m-0"></p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted">Cliente</label>
                            <p id="detalleVentaCliente" class="fw-bold m-0"></p>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">Vendedor</label>
                            <p id="detalleVentaUsuario" class="fw-bold m-0"></p>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">Fecha</label>
                            <p id="detalleVentaFecha" class="fw-bold m-0"></p>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">Estado</label>
                            <div id="detalleVentaEstado"></div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2 pt-3 border-top border-secondary">
                        <div class="col-md-2">
                            <label class="form-label text-muted">Subtotal</label>
                            <p id="detalleVentaSubtotal" class="fw-bold m-0"></p>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">Descuento</label>
                            <p id="detalleVentaDescuento" class="fw-bold m-0 text-warning"></p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted">Total Venta</label>
                            <p id="detalleVentaTotal" class="fw-bold m-0 fs-3 text-warning"></p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted">Total Abonado / Pagado</label>
                            <p id="detalleVentaPagado" class="fw-bold m-0 text-success"></p>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">Saldo Pendiente</label>
                            <p id="detalleVentaSaldo" class="fw-bold m-0 text-danger"></p>
                        </div>
                    </div>

                    <div class="row mt-2" id="contenedorDetalleObservacion" style="display: none;">
                        <div class="col-12">
                            <label class="form-label text-muted">Observación</label>
                            <p id="detalleVentaObservacion" class="m-0"></p>
                        </div>
                    </div>
                </div>

                <!-- PRODUCTOS DE LA VENTA -->
                <h5 class="mb-3"><i class="fa-solid fa-boxes-stacked me-2 text-warning"></i>Productos Vendidos</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-striped table-hover table-sm table-bordered custom-table table-dark">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>#</th>
                                <th>Producto / Presentación</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalleItemsVenta"></tbody>
                    </table>
                </div>

                <!-- PAGOS / ABONOS DE LA VENTA -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0"><i class="fa-solid fa-money-Check-dollar me-2 text-warning"></i>Historial de Pagos / Abonos</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm table-bordered custom-table table-dark">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>#</th>
                                <th>Método de Pago</th>
                                <th>Monto</th>
                                <th>Registrado por</th>
                                <th>Fecha de Pago</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetallePagosVenta"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ==============================================================
     MODAL 3: REGISTRAR ABONO A VENTA PENDIENTE
     ============================================================== -->
<div class="modal fade" id="modalAbonoVenta" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalAbonoVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalAbonoVentaLabel">
                        <i class="fa-solid fa-hand-holding-dollar me-2 text-warning"></i>Registrar Abono
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formAbonoVenta" autocomplete="off">
                    <input type="hidden" id="abonoIdVenta" name="idVenta">

                    <div class="ventas-resumen-box mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">N° Venta:</span>
                            <strong id="abonoNumeroVenta">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Cliente:</span>
                            <strong id="abonoClienteVenta">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Venta:</span>
                            <strong id="abonoTotalVenta">$ 0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Abonado:</span>
                            <strong id="abonoPagadoVenta" class="text-success">$ 0</strong>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="d-flex justify-content-between">
                            <span>Saldo Pendiente:</span>
                            <strong id="abonoSaldoPendiente" class="text-warning fs-4">$ 0</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="selectMetodoPagoAbono" class="form-label">Método de Pago *</label>
                        <select class="form-select" id="selectMetodoPagoAbono" name="idMetodoPago" required>
                            <option value="">Seleccione método</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="montoAbonoVenta" class="form-label">Monto a Abonar *</label>
                        <input type="text" class="form-control" id="montoAbonoVenta" name="montoPago" placeholder="Ej: 50.000" data-format-miles required>
                    </div>

                    <button type="submit" class="btn btn-primary btnRegistro w-100">
                        <i class="fa-solid fa-check"></i> Confirmar Abono
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- ==============================================================
     MODAL 4: REGISTRAR NUEVO CLIENTE RÁPIDO
     ============================================================== -->
<div class="modal fade" id="modalRegistroCliente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalRegistroClienteLabel">
                        <i class="fa-solid fa-user-plus me-2 text-warning"></i>Registrar Nuevo Cliente
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formRegistroCliente" autocomplete="off">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="identificacionCliente" class="form-label">Identificación (CC / NIT)</label>
                            <input type="text" class="form-control" id="identificacionCliente" name="identificacionCliente" placeholder="Ej: 1094950123">
                        </div>

                        <div class="col-md-6">
                            <label for="nombreCliente" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombreCliente" name="nombreCliente" placeholder="Ej: Carlos Ramírez" required>
                        </div>

                        <div class="col-md-6">
                            <label for="telefonoCliente" class="form-label">Teléfono / WhatsApp</label>
                            <input type="text" class="form-control" id="telefonoCliente" name="telefonoCliente" placeholder="Ej: 3150000000">
                        </div>

                        <div class="col-md-6">
                            <label for="direccionCliente" class="form-label">Dirección / Ciudad</label>
                            <input type="text" class="form-control" id="direccionCliente" name="direccionCliente" placeholder="Ej: Calle 15 # 10-20">
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center mt-4">
                        <div class="col-md-5">
                            <button type="submit" class="btn btn-primary btnRegistro w-100">
                                <i class="fa-regular fa-floppy-disk"></i> Guardar Cliente
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
