<section class="content-panel" id="bannerPromocionesModule">
    <div class="row">
        <div class="col-md-10">
            <h1>Banners Promocionales</h1>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-new-product" id="btnModalCompra" data-bs-toggle="modal" data-bs-target="#modalRegistrarBanner"><i class="fa-solid fa-plus"></i> Nuevo banner</button>
            <br>
        </div>
    </div>
</section>

<section class="content-list-banners container">
    <div class="table-responsive">
        <table id="tablaBanners" class="table table-striped table-hover table-bordered table-smal custom-table table-dark text-center">
            <thead class="text-center">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Img_banner</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>


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

<!-- ************* MODAL DE REGISTRO DE BANNERS ********** -->
<div class="modal fade" id="modalRegistrarBanner" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRegistrarBannerLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="modalRegistrarBannerLabel">Registrar Nuevo Banner</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="registroDeBanner">
                    <input type="hidden" name="id_banner_promocion" id="id_banner_promocion">
                    <div class="row">

                        <div class="col-md-4">
                            <input type="text" name="nombreBanner" id="nombreBanner" class="form-control" placeholder="Nombre del Banner" required>
                        </div>

                        <div class="col-md-4">
                            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control" placeholder="Fecha de Inicio" required>
                        </div>

                        <div class="col-md-4">
                            <input type="date" name="fechaFin" id="fechaFin" class="form-control" placeholder="Fecha de Fin" required>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <input type="file" name="imgBanner" id="imgBanner" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center mt-4">
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btnRegistro w-100" id="btnRegistrarDetalles">
                                <i class="fa-regular fa-floppy-disk"></i> Registrar Banner
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

