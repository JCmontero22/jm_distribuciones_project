<section class="content-panel" id="locionesAAAModule" data-inventory-config='{"categoria":"Lociones AAA","contenedorSelector":".content-inventory","cantidadId":"#cantidad-locionesAAA","accionListar":"listarProductos","tieneSedes":true, "idModalPresentacion":"#modalRegistroPresentacion"}'>
    <div class="row">
        <div class="col-md-8">
            <h1>Inventario Lociones AAA</h1>
            <p>(Cantidad de lociones AAA: <span id="cantidad-locionesAAA"></span>)</p>
        </div>

        <div class="col-md-4 d-flex justify-content-end align-items-start">
            <button type="button" class="btn btn-new-product" data-bs-toggle="modal" data-bs-target="#modalRegistroProducto"><i class="fa-solid fa-plus"></i> Nueva Locion</button>
            <!-- <button type="button" class="btn btn-new-product" style="margin-left: 1rem;" data-bs-toggle="modal" data-bs-target="#modalRegistroPresentacion"><i class="fa-solid fa-plus"></i> Nueva Presentacion</button> -->
        </div>
    </div>
</section>

<section>
    <div class="content-buscador-inventory">
        <div class="row">
            <div class="col-md-12">
                <input type="text" name="buscar" id="buscar" class="form-control" placeholder="Buscar por nombre o código...">
            </div>
        </div>
    </div>    
    
    <div class="content-inventory"></div>
    <!-- <div class="card-inventory"> -->
        <!-- <div class="card-inventory_img">
            <img src="./assets/img/producto1.jpg" alt="Producto 1" id="imgProductos">
        </div>

        <div class="card-inventory_infoProducto">
            <span class="card-inventory_category">Relojes</span>
            <h3 class="card-inventory_name">Curren 1030</h3>
            <p><span class="card-inventory_cost">$ 60.000</span> <span class="card-inventory_sale">$ 120.000</span></p>
        </div>

        <div class="card-inventory_stock">
            <span class="stock-label">Stock:</span>
            <span class="stock-value">15</span>
        </div>

        <div class="card-inventory_op">
            <button class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i> Editar</button>
            <button class="btn btn-danger"><i class="fa-solid fa-trash"></i> Eliminar</button>
        </div> -->
    <!-- </div> -->
</section>


<!--*************** MODAL REGISTRO DE PRODUCTOS ***************-->
<div class="modal fade" id="modalRegistroProducto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="staticBackdropLabel">Registrar Producto</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div id="registroProducto">
                    <form action="" id="formRegistroProducto">
                        <input type="hidden" id="idProductoEditar" name="idProductoEditar">
                        <!-- Nombre -->
                        <div class="row">
                            <div class="col-md-8">
                                <label for="nombreProducto" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" placeholder="Ej: Reloj Curren 1030">
                                
                            </div>

                            <div class="col-md-4">
                                <label for="codigoProducto" class="form-label">Codigo Producto</label>
                                <input type="text" class="form-control" id="codigoProducto" name="codigoProducto" placeholder="1030">
                            </div>
                        </div>
                        <!-- Codigo - Categoria -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label for="categoriaProducto" class="form-label">Categoria</label>
                                <select class="form-select" id="categoriaProducto" name="categoriaProducto">
                                    <option value="" selected disabled>Seleccionar</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="marcaProducto" class="form-label">Marca</label>
                                <select class="form-select" id="marcaProducto" name="marcaProducto">
                                    <option value="" selected disabled>Seleccionar</option>
                                </select>
                            </div>
                        </div>

                         <div class="row mt-4">
                            <div class="col-md-4 form-check">
                                <label for="favoritoProducto" class="form-check-label" style="margin-left: 5px;">Favorito</label>
                                <input type="checkbox" name="favoritoProducto" id="favoritoProducto" class="form-check-input">
                            </div>
                        </div>

                        <!-- descripcion -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label for="descripcionProducto" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcionProducto" name="descripcionProducto" rows="3" placeholder="Descripción del producto..."></textarea>
                            </div>
                        </div>

                        <!-- imagen -->
                        <!-- <div class="row mt-4">
                            <div class="col-md-12">
                                <label for="imagenProducto" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagenProducto" name="imagenProducto" accept="image/*">
                            </div>
                        </div> -->

                        <div class="row d-flex justify-content-center mt-5">
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary w-100 btnRegistro"> <i class="fa-regular fa-floppy-disk"></i> Registrar Producto</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- *************************** MODAL PRESENTACION PRODUCTO *************** -->
<div class="modal fade" id="modalRegistroPresentacion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRegistroPresentacion" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">           
            <div class="modal-body">
                 <div class="modal-cabecera">
                    <h3 class="modal-title" id="staticBackdropLabel">Registrar Presentacion</h3>
                    <button type="button" style="color: white !important;" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Presentaciones ya registradas -->
                <div id="seccionPresentacionesExistentes" style="display:none;" class="mb-3">
                    <div class="accordion" id="accordionExistentes">
                        <div class="accordion-item" style="background:#2a2a2e;border:1px solid #3f3f46;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExistentes" style="background:#2a2a2e;color:#f4f4f5;">
                                    <i class="fa-solid fa-layer-group me-2"></i> Presentaciones ya registradas
                                </button>
                            </h2>
                            <div id="collapseExistentes" class="accordion-collapse collapse">
                                <div class="accordion-body p-0">
                                    <table class="table table-sm table-dark mb-0">
                                        <thead><tr><th>Nombre</th><th>Código</th><th class="text-center">P. Compra</th><th class="text-center">Stock</th><th></th></tr></thead>
                                        <tbody id="tablaExistentesBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--*************** REGISTRO PRESENTACION DE PRODUCTO ***************-->
                <div id="registroPresentacionProducto">
                    <form action="" id="formRegistroPresentacionProducto">
                        <!-- Nombre -->
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="nombrePresentacion" class="form-label">Nombre Presentacion</label>
                                <input type="text" class="form-control" id="nombrePresentacion" name="nombrePresentacion" placeholder="Ej: Curren 103 - rojo, 30ml">
                            </div>

                            <div class="col-md-4">
                                <label for="codigoPresentacion" class="form-label">Codigo Presentacion</label>
                                <input type="text" class="form-control" id="codigoPresentacion" name="codigoPresentacion" placeholder="1030">
                            </div>

                            <div class="col-md-4">
                                <label for="precioCompraPresentacion" class="form-label">Precio compra (Opcional)</label>
                                <input type="text" class="form-control" id="precioCompraPresentacion" name="precioCompraPresentacion" placeholder="10.000" data-format-miles>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <label for="precioVentaPresentacion" class="form-label">Precio venta (Opcional)</label>
                                <input type="text" class="form-control" id="precioVentaPresentacion" name="precioVentaPresentacion" placeholder="30.000" data-format-miles>
                            </div>
                            <div class="col-md-4">
                                <label for="tipoProducto" class="form-label">Tipo Producto</label>
                                    <select class="form-select" id="tipoProducto" name="tipoProducto">
                                        <option value="" selected disabled>Seleccionar</option>
                                    </select>
                            </div>

                            <div class="col-md-4">
                                <label for="unidadMedidaPresentacion" class="form-label">Unidad</label>
                                    <select class="form-select" id="unidadMedidaPresentacion" name="unidadMedidaPresentacion">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="true">Unidad</option>
                                        <option value="false">Gramo</option>
                                    </select>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="generoPresentacion" class="form-label">Género</label>
                                    <select class="form-select" id="generoPresentacion" name="generoPresentacion">
                                        <option value="" selected disabled>Seleccionar</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="imagenPresentacion" class="form-label">Imagen presentacion</label>
                                    <input type="file" class="form-control" id="imagenPresentacion" name="imagenPresentacion" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label for="descripcionPresentacion" class="form-label">Descripción (Opcional)</label>
                                <textarea class="form-control" id="descripcionPresentacion" name="descripcionPresentacion" rows="2" placeholder="Descripción de la presentación..."></textarea>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-2 form-check">
                                <label for="favoritoPresentacion" class="form-check-label" style="margin-left: 5px;">Favorito</label>
                                <input type="checkbox" name="favoritoPresentacion" id="favoritoPresentacion" class="form-check-input">
                            </div>
                        </div>

                        <!-- Botón Agregar -->
                        <div class="row">
                            <div class="col-md-12 mt-4 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btnRegistro" id="btnAgregarStock"><i class="fa-solid fa-plus"></i> Agregar</button>
                            </div>
                        </div>

                        <!-- Tabla de presentaciones acumuladas -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Presentaciones agregadas</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Código</th>
                                                <th>Precio Compra</th>
                                                <th>Precio Venta</th>
                                                <th>Tipo</th>
                                                
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaPresentaciones">
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No hay presentaciones agregadas</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center mt-5">
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary w-100 btnRegistro" > <i class="fa-regular fa-floppy-disk"></i> Registrar Presentaciones</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
