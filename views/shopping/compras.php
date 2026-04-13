<section class="content-panel" id="moduleCompras">
    <div class="row">
        <div class="col-md-10">
            <h1>Compras</h1>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-new-product" id="btnModalCompra" data-bs-toggle="modal" data-bs-target="#modalRegistroCompra"><i class="fa-solid fa-plus"></i> Nueva Compra</button>
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
                            <select class="form-select" name="proveedor" id="selectProveedor"></select>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="totalCompra" id="totalCompra" class="form-control" placeholder="Total Compra" readonly>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="numeroFactura" id="numeroFactura" class="form-control" placeholder="Número de Factura">
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


<!-- 
<div class="col-md-4">
                            <select class="form-select" name="producto" id="selectProducto"></select>
                        </div>
 -->