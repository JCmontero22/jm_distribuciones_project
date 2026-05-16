<section class="content-panel" id="locionesPreparadasModule" data-inventory-config='{"categoria":"Lociones","contenedorSelector":".content-inventory-lociones-preparadas","cantidadId":"#cantidad-lociones-preparadas","accionListar":"listadoLociones","tieneSedes":true}'>
    <div class="row">
        <div class="col-md-8">
            <h1>Inventario Lociones Preparadas</h1>
            <p>(Cantidad de lociones preparadas: <span id="cantidad-lociones-preparadas">0</span>)</p>
        </div>

        <div class="col-md-4 d-flex justify-content-end align-items-start">
            <button type="button" class="btn btn-new-product" data-bs-toggle="modal" data-bs-target="#modalRegistroProducto"><i class="fa-solid fa-plus"></i> Nueva Loción</button>
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
    
    <div class="content-inventory-lociones-preparadas"></div>
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
                            <div class="col-md-4">
                                <label for="categoriaProducto" class="form-label">Categoria</label>
                                <select class="form-select" id="categoriaProducto" name="categoriaProducto">
                                    <option value="" selected disabled>Seleccionar</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="marcaProducto" class="form-label">Marca</label>
                                <select class="form-select" id="marcaProducto" name="marcaProducto">
                                    <option value="" selected disabled>Seleccionar</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="generoProducto" class="form-label">Género</label>
                                <select class="form-select" id="generoProducto" name="generoProducto">
                                    <option value="" selected disabled>Seleccionar</option>
                                </select>
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
                                <label for="precioCompraPresentacion" class="form-label">Precio compra presentacion</label>
                                <input type="text" class="form-control" id="precioCompraPresentacion" name="precioCompraPresentacion" placeholder="10.000" data-format-miles>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-3">
                                <label for="precioVentaPresentacion" class="form-label">Precio venta presentacion</label>
                                <input type="text" class="form-control" id="precioVentaPresentacion" name="precioVentaPresentacion" placeholder="30.000" data-format-miles>
                            </div>
                            <div class="col-md-3">
                                <label for="tipoProducto" class="form-label">Tipo Producto</label>
                                    <select class="form-select" id="tipoProducto" name="tipoProducto">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="1">REVENTA</option>
                                        <option value="2">PRODUCCION</option>
                                    </select>
                            </div>

                            <div class="col-md-3">
                                <label for="unidad" class="form-label">Unidad</label>
                                    <select class="form-select" id="unidad" name="unidad">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="true">Unidad</option>
                                        <option value="false">Gramo</option>
                                    </select>
                            </div>

                            <div class="col-md-3">
                                <label for="preparada" class="form-label">Preparada</label>
                                    <select class="form-select" id="preparada" name="preparada">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="1">SI</option>
                                        <option value="0">NO</option>
                                    </select>
                            </div>
                           
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <label for="imagenPresentacion" class="form-label">Imagen presentacion</label>
                                <input type="file" class="form-control" id="imagenPresentacion" name="imagenPresentacion" accept="image/*">
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
                                                <th>Imagen</th>
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
