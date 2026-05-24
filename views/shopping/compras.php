<section class="content-panel" id="moduleCompras">
    <div class="row">
        <div class="col-md-10">
            <h1>Compras</h1>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-new-product" id="btnModalCompra" data-bs-toggle="modal" data-bs-target="#modalRegistroCompra"><i class="fa-solid fa-plus"></i> Nueva Compra</button>
            <br>
        </div>
    </div>
</section>

<section class="content-list-compras">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="text-center">
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody id="comprasTableBody">
                <!-- Aquí se llenarán las compras dinámicamente -->
            </tbody>
        </table>
    </div>
</section>

<!-- ================ REPORTE DE CAPACIDAD DE PRODUCCIÓN ================== -->
<section class="content-panel mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>📊 Capacidad de Producción</h2>
            <p class="text-muted">Cantidad de lociones que puedes producir basado en los gramos de esencia disponibles</p>
        </div>
    </div>

    <div id="reporteCapacidadContainer" style="display: none;">
        <div class="row mt-4" id="reporteCapacidad">
            <!-- Se llenarán las tarjetas de esencias dinámicamente -->
        </div>
    </div>

    <div id="sinReporteMessage" class="alert alert-info mt-4">
        <i class="fa-solid fa-info-circle"></i> No hay esencias con stock disponible. Registra una compra de esencia para ver el reporte de capacidad.
    </div>
</section>


<!-- ************* MODAL DE REGISTRO DE COMPRAS ********** -->
<div class="modal fade" id="modalRegistroCompra" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRegistroCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalRegistroCompraLabel">Registrar Nueva Compra</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="registroDeCompra">
                    <div class="row">

                        <div class="col-md-4">
                            <select class="form-select" name="idProveedor" id="selectProveedor">
                                <option value="">Seleccione proveedor</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="totalCompra" id="totalCompra" class="form-control" placeholder="Total Compra" data-format-miles>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="numeroFacturaCompra" id="numeroFacturaCompra" class="form-control" placeholder="Número de Factura">
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center mt-5">
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100 btnRegistro"> <i class="fa-regular fa-floppy-disk"></i>Siguiente</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ************************** MODAL DE REGISTRO DEL DETALLE DE COMPRA ********** -->
<div class="modal fade" id="modalRegistroDetalleCompra" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRegistroDetalleCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalRegistroDetalleCompraLabel">Registrar Detalle de Compra</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="formDetalleCompra">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-select" name="sede" id="selectSede">
                                <option value="">Seleccione sede</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <select class="form-select" name="producto" id="selectProducto">
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="text" name="cantidad" id="cantidad" class="form-control" placeholder="Cantidad">
                        </div>

                    </div>

                    <div class="row mt-4">
                        <div class="col-md-3">
                            <input type="text" name="costoUnitario" id="costoUnitario" class="form-control" placeholder="Costo Unitario" data-format-miles>
                        </div>

                        <div class="col-md-3">
                            <input type="text" name="subtotal" id="subtotal" class="form-control" placeholder="Subtotal" readonly>
                        </div>

                        <div class="col-md-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btnRegistro w-100" id="btnAgregarDetalle">
                                <i class="fa-solid fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de detalles agregados -->
                <div id="detallesAcumuladosContainer" style="display: none; margin-top: 30px;">
                    <h5>Detalles de compra agregados</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sede</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaDetalles"></tbody>
                        </table>
                    </div>

                    <div class="row d-flex justify-content-center mt-4">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary btnRegistro w-100" id="btnRegistrarDetalles">
                                <i class="fa-regular fa-floppy-disk"></i> Registrar Detalles
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- 
<div class="col-md-4">
                            <select class="form-select" name="producto" id="selectProducto"></select>
                        </div>
 -->