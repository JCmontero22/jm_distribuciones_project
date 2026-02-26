<section class="content-panel">
    <div class="row">
        <div class="col-md-10">
            <h1>Inventario</h1>
            <p>(Cantidad de productos: <span id="cantidad-productos">0</span>)</p>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-new-product" data-bs-toggle="modal" data-bs-target="#modalRegistroProducto"><i class="fa-solid fa-plus"></i> Nuevo Producto</button>
        </div>
    </div>
</section>

<section class="content-inventory">
    <div class="card-inventory">
        <div class="card-inventory_img">
            <img src="./assets/img/producto1.jpg" alt="Producto 1">
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
        </div>
    </div>
</section>


<!--*************** MODAL REGISTRO DE PRODUCTOS ***************-->

<!-- Modal -->
<div class="modal fade" id="modalRegistroProducto" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-cabecera">
                    <h3 class="modal-title" id="staticBackdropLabel">Registrar Producto</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <!--*************** REGISTRO PRODUCTO ***************-->
                <div id="registroProducto">
                    <form action="" id="formRegistroProducto">
                        <!-- Nombre -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="nombreProducto" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" placeholder="Ej: Reloj Curren 1030">
                                </div>
                            </div>
                        </div>
                        <!-- Codigo - Categoria -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label for="codigoProducto" class="form-label">Codigo Producto</label>
                                    <input type="text" class="form-control" id="codigoProducto" name="codigoProducto" placeholder="1030">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label for="categoriaProducto" class="form-label">Categoria</label>
                                    <select class="form-select" id="categoriaProducto" name="categoriaProducto">
                                        <option value="" selected disabled>Seleccionar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label for="marcaProducto" class="form-label">Marca</label>
                                    <select class="form-select" id="marcaProducto" name="marcaProducto">
                                        <option value="" selected disabled>Seleccionar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- imagen -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="imagenProducto" class="form-label">Imagen</label>
                                    <input type="file" class="form-control" id="imagenProducto" name="imagenProducto" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center mt-5">
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary w-100 btnRegistro"> <i class="fa-regular fa-floppy-disk"></i> Registrar Producto</button>
                            </div>
                        </div>
                    </form>
                    
                </div>

                <!--*************** REGISTRO DETALLE DE PRODUCTO ***************-->
                <div id="registroDetalleProducto">
                    <form action="" id="formRegistroDetalleProducto">
                        <!-- Nombre -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="nombreProducto" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombreProducto" placeholder="Ej: Reloj Curren 1030">
                                </div>
                            </div>
                        </div>
                        <!-- Codigo - Categoria -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label for="codigoProducto" class="form-label">Codigo Producto</label>
                                    <input type="text" class="form-control" id="codigoProducto" placeholder="1030">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label for="categoriaProducto" class="form-label">Categoria</label>
                                    <select class="form-select" id="categoriaProducto">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="relojes">Relojes</option>
                                        <option value="celulares">Celulares</option>
                                        <option value="audifonos">Audifonos</option>
                                        <option value="laptops">Laptops</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <label for="marcaProducto" class="form-label">Marca</label>
                                    <select class="form-select" id="marcaProducto">
                                        <option value="" selected disabled>Seleccionar</option>
                                        <option value="curren">Curren</option>
                                        <option value="samsung">Samsung</option>
                                        <option value="apple">Apple</option>
                                        <option value="lenovo">Lenovo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- imagen -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label for="imagenProducto" class="form-label">Imagen</label>
                                    <input type="file" class="form-control" id="imagenProducto" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row d-flex justify-content-center mt-5">
                        <div class="col-md-8">
                            <button type="button" class="btn btn-primary w-100 btnRegistro"> <i class="fa-regular fa-floppy-disk"></i> Registrar Detalle</button>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>