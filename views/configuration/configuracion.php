<section class="content-panel" id="ConfiguracionModule">
    <div class="row">
        <div class="col-md-10">
            <h1>Gestión de Configuración</h1>
        </div>
        <div class="col-md-2">
        </div>
    </div>
</section>


<section class="content-configuracion container">
    <!-- TABS para navegación -->
    <ul class="nav nav-tabs descuentos-tabs mt-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="categoriaProductos" data-bs-toggle="tab" data-bs-target="#categoriaProductos" type="button" role="tab">
                <i class="fa-solid fa-list"></i> Categorias Productos
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tiposProductos" data-bs-toggle="tab" data-bs-target="#tiposProductos" type="button" role="tab">
                <i class="fa-solid fa-table-list"></i> Tipos de Productos
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sedes" data-bs-toggle="tab" data-bs-target="#sedes" type="button" role="tab">
                <i class="fa-solid fa-people-roof"></i> Sedes
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="usuarios" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">
                <i class="fa-solid fa-user-group"></i> Usuarios
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="perfilUsuarios" data-bs-toggle="tab" data-bs-target="#perfilUsuarios" type="button" role="tab">
                <i class="fa-solid fa-address-card"></i> Perfil usuarios
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="permisos" data-bs-toggle="tab" data-bs-target="#permisos" type="button" role="tab">
                <i class="fa-solid fa-list-check"></i> Permisos 
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="perfilUsuariosPermisos" data-bs-toggle="tab" data-bs-target="#perfilUsuariosPermisos" type="button" role="tab">
                <i class="fa-solid fa-user-check"></i> Perfil usuarios - Permisos
            </button>
        </li>
    </ul>

    <!-- TAB 1: Lista de descuentos registrados -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="categoriaProductos" role="tabpanel">
            <div class="row mt-5">
                <div class="col-md-12">
                    <form id="form-categoria-productos">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="nombreCategoria" class="form-label">Nombre de la categoría *</label>
                                <input type="text" class="form-control" id="nombreCategoria" placeholder="Ej: Ropa, Calzado, Accesorios..." required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary btnRegistro w-100" type="submit">Registrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="table-responsive mt-4">
                        <table id="tablaCategoriaProductos" class="table table-striped table-hover table-bordered custom-table table-dark text-center">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Categoria</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Aplicar descuentos con pestañas anidadas -->
        <div class="tab-pane fade" id="aplicarDescuentos" role="tabpanel">
            <div class="mt-4">
                <h5>Selecciona cómo quieres aplicar el descuento</h5>

                <ul class="nav nav-pills descuentos-pills mt-3 mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="subTabProductos" data-bs-toggle="tab" data-bs-target="#formProductos" type="button" role="tab">
                            Productos Específicos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="subTabMarca" data-bs-toggle="tab" data-bs-target="#formMarca" type="button" role="tab">
                            Por Marca
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="subTabProductoGenero" data-bs-toggle="tab" data-bs-target="#formProductoGenero" type="button" role="tab">
                            Género de Producto
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="subTabGenero" data-bs-toggle="tab" data-bs-target="#formGenero" type="button" role="tab">
                            Género Completo
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="subTabTodos" data-bs-toggle="tab" data-bs-target="#formTodos" type="button" role="tab">
                            Todos los Productos
                        </button>
                    </li>
                </ul>

                <div class="tab-content descuentos-apply-panel p-4">
                    <!-- 1️⃣ Productos Específicos -->
                    <div class="tab-pane fade show active" id="formProductos" role="tabpanel">
                        <form id="formAplicarProductos">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Descuento *</label>
                                    <select class="form-select selectDescuento" id="selectDescuentoProductos" required>
                                        <option value="">-- Selecciona un descuento --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label class="form-label">Selecciona los Productos *</label>
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="buscarProductosDescuento" placeholder="Busca por nombre o código..." autocomplete="off">
                                        <small class="text-muted d-block mt-2">
                                            <span id="countProductosBuscados">0</span> de <span id="totalProductosDescuento">0</span> productos
                                        </small>
                                    </div>
                                    <div id="productosCheckbox" class="descuentos-productos-list p-3" style="max-height: 400px; overflow-y: auto;">
                                        <!-- Se llena con JS -->
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-check"></i> Aplicar a Productos
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 2️⃣ Por Marca -->
                    <div class="tab-pane fade" id="formMarca" role="tabpanel">
                        <form id="formAplicarMarca">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Descuento *</label>
                                    <select class="form-select selectDescuento" id="selectDescuentoMarca" required>
                                        <option value="">-- Selecciona un descuento --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Marca *</label>
                                    <select class="form-select" id="selectMarca" required>
                                        <option value="">-- Selecciona una marca --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-check"></i> Aplicar a Marca
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 3️⃣ Género de un Producto -->
                    <div class="tab-pane fade" id="formProductoGenero" role="tabpanel">
                        <form id="formAplicarProductoGenero">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Descuento *</label>
                                    <select class="form-select selectDescuento" id="selectDescuentoProductoGenero" required>
                                        <option value="">-- Selecciona un descuento --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Producto *</label>
                                    <select class="form-select" id="selectProductoGenero" required>
                                        <option value="">-- Selecciona un producto --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Género *</label>
                                    <select class="form-select" id="selectGeneroProducto" required>
                                        <option value="">-- Selecciona un género --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-check"></i> Aplicar al Género
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 4️⃣ Género Completo -->
                    <div class="tab-pane fade" id="formGenero" role="tabpanel">
                        <form id="formAplicarGenero">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Descuento *</label>
                                    <select class="form-select selectDescuento" id="selectDescuentoGenero" required>
                                        <option value="">-- Selecciona un descuento --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Género *</label>
                                    <select class="form-select" id="selectGenero" required>
                                        <option value="">-- Selecciona un género --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-check"></i> Aplicar a Género
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 5️⃣ Todos los Productos -->
                    <div class="tab-pane fade" id="formTodos" role="tabpanel">
                        <div class="alert descuentos-alert">
                            Esta acción aplicará el descuento a <strong>TODOS</strong> los productos del sistema.
                        </div>
                        <form id="formAplicarTodos">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Descuento *</label>
                                    <select class="form-select selectDescuento" id="selectDescuentoTodos" required>
                                        <option value="">-- Selecciona un descuento --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fa-solid fa-exclamation"></i> Aplicar a TODO
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>