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

<section class="content-list-compras container">
    <div class="table-responsive">
        <table id="tablaCompras" class="table table-striped table-hover table-bordered table-smal custom-table table-dark text-center">
            <thead class="text-center">
                <tr>
                    <th>ID</th>
                    <th>Factura</th>
                    <th>Proveedor</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>


<!-- ************* MODAL VER DETALLE DE COMPRA ********** -->
<div class="modal fade" id="modalDetalleCompra" tabindex="-1" aria-labelledby="modalDetalleCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalDetalleCompraLabel">Detalle de Compra</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="row mb-4 mt-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted">Proveedor</label>
                        <p id="detalleProveedor" class="fw-bold"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Número de Factura</label>
                        <p id="detalleFactura" class="fw-bold"></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Total</label>
                        <p id="detalleTotal" class="fw-bold"></p>
                    </div>
                </div>

                <h5 class="mb-3">Ítems de la compra</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm table-bordered custom-table table-dark">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>#</th>
                                <th>Sede</th>
                                <th>Producto / Presentación</th>
                                <th>Cantidad</th>
                                <th>Costo Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalleCompra"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ************* MODAL EDITAR COMPRA ********** -->
<div class="modal fade" id="modalEditarCompra" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalEditarCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalEditarCompraLabel">Editar Compra</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="formEditarCompra">
                    <input type="hidden" id="editIdCompra" name="idCompra">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-select" name="idProveedor" id="editSelectProveedor">
                                <option value="">Seleccione proveedor</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="numeroFacturaCompra" id="editNumeroFactura" class="form-control" placeholder="Número de Factura" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="totalCompra" id="editTotalCompra" class="form-control" disabled placeholder="Total Compra" data-format-miles>
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center mt-5">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="modal-title mb-3">Agregar ítem</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-select" name="sede" id="editSelectSede">
                                    <option value="">Seleccione sede</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="categoria" id="editSelectCategoria">
                                    <option value="">Todas las categorías</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" name="producto" id="editSelectProducto">
                                    <option value="">Seleccione producto</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="cantidad" id="editCantidad" class="form-control" placeholder="Cantidad">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <input type="text" name="costoUnitario" id="editCostoUnitario" class="form-control" placeholder="Costo Unitario" data-format-miles-edit>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="subtotal" id="editSubtotal" class="form-control" placeholder="Subtotal" readonly>
                            </div>
                            <div class="col-md-3 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btnRegistro w-100" id="editBtnAgregarDetalle">
                                    <i class="fa-solid fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="editDetallesAcumuladosContainer" style="display: none; margin-top: 30px;">
                        <h5>Ítems de la compra</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm custom-table table-dark">
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
                                <tbody id="editTablaDetalles"></tbody>
                            </table>
                        </div>
                        <div class="row d-flex justify-content-center mt-4">
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btnRegistro w-100">
                                    <i class="fa-regular fa-floppy-disk"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ************* MODAL DE REGISTRO DE COMPRAS ********** -->
<div class="modal fade" id="modalRegistroCompra" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRegistroCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalRegistroCompraLabel">Registrar Nueva Compra</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="registroDeCompra">
                    <div class="row">

                        <div class="col-md-4">
                            <select class="form-select" name="idProveedor" id="selectProveedor" >
                                <option value="">Seleccione proveedor</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="numeroFacturaCompra" id="numeroFacturaCompra" class="form-control" placeholder="Número de Factura" required>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="totalCompra" id="totalCompra" class="form-control" disabled placeholder="Total Compra" data-format-miles>
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center mt-5">                  
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="modal-title mb-3" id="modalRegistroDetalleCompraLabel">Registrar Detalle de Compra</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-select" name="sede" id="selectSede" required>
                                    <option value="">Seleccione sede</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" name="categoria" id="selectCategoria">
                                    <option value="">Todas las categorías</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select class="form-select" name="producto" id="selectProducto" >
                                    <option value="">Seleccione producto</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="text" name="cantidad" id="cantidad" class="form-control" placeholder="Cantidad" >
                            </div>

                        </div>

                        <div class="row mt-4">
                            <div class="col-md-3">
                                <input type="text" name="costoUnitario" id="costoUnitario" class="form-control" placeholder="Costo Unitario" data-format-miles >
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
                    </div>

                     <!-- Tabla de detalles agregados -->
                    <div id="detallesAcumuladosContainer" style="display: none; margin-top: 30px;">
                        <h5>Detalles de compra agregados</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm custom-table table-dark">
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
                                <button type="submit" class="btn btn-primary btnRegistro w-100" id="btnRegistrarDetalles">
                                    <i class="fa-regular fa-floppy-disk"></i> Registrar Compra
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

               
            </div>
        </div>
    </div>
</div>

