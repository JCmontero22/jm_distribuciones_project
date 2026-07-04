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
            <button class="nav-link active" id="tab-categoriaProductos" data-bs-toggle="tab" data-bs-target="#categoriaProductos" type="button" role="tab" aria-controls="categoriaProductos">
                <i class="fa-solid fa-list"></i> Categorias Productos
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-tiposProductos" data-bs-toggle="tab" data-bs-target="#tiposProductos" type="button" role="tab" aria-controls="tiposProductos">
                <i class="fa-solid fa-table-list"></i> Tipos de Productos
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-sedes" data-bs-toggle="tab" data-bs-target="#sedes" type="button" role="tab" aria-controls="sedes">
                <i class="fa-solid fa-people-roof"></i> Sedes
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-usuarios" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios">
                <i class="fa-solid fa-user-group"></i> Usuarios
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-perfilUsuarios" data-bs-toggle="tab" data-bs-target="#perfilUsuarios" type="button" role="tab" aria-controls="perfilUsuarios">
                <i class="fa-solid fa-address-card"></i> Perfil usuarios
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-permisos" data-bs-toggle="tab" data-bs-target="#permisos" type="button" role="tab" aria-controls="permisos">
                <i class="fa-solid fa-list-check"></i> Permisos 
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-perfilUsuariosPermisos" data-bs-toggle="tab" data-bs-target="#perfilUsuariosPermisos" type="button" role="tab" aria-controls="perfilUsuariosPermisos">
                <i class="fa-solid fa-user-check"></i> Perfil usuarios - Permisos
            </button>
        </li>
    </ul>

    
    <div class="tab-content">

        <!-- TAB 1: Categorías de productos -->
        <div class="tab-pane fade show active" id="categoriaProductos" role="tabpanel" aria-labelledby="tab-categoriaProductos">
            <div class="row mt-5">
                <div class="col-md-12">
                    <form id="form-categoria-productos">
                        <input type="hidden" id="idCategoria" value="">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="nombreCategoria" class="form-label">Nombre de la categoría *</label>
                                <input type="text" class="form-control" id="nombreCategoria"name="nombreCategoria" placeholder="Ej: Ropa, Calzado, Accesorios..." required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end" >
                                <button class="btn btn-primary btnRegistro w-100" id="btnRegistrarCategoria"type="submit">Registrar</button>
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

        <!-- TAB 2: Tipos de productos con pestañas anidadas -->
        <div class="tab-pane fade" id="tiposProductos" role="tabpanel" aria-labelledby="tab-tiposProductos">
            <div class="row mt-5">
                <div class="col-md-12">
                    <form id="form-tipo-productos">
                        <input type="hidden" id="idTipoProducto" value="">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="descripcionTipoProducto" class="form-label">Nombre del tipo de producto *</label>
                                <input type="text" class="form-control" id="descripcionTipoProducto"name="descripcionTipoProducto" placeholder="Ej: Insumno, Reventa..." required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end" >
                                <button class="btn btn-primary btnRegistro w-100" id="btnRegistrarTipoProducto"type="submit">Registrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="table-responsive mt-4">
                        <table id="tablaTipoProductos" class="table table-striped table-hover table-bordered custom-table table-dark text-center">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Tipo Producto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Sedes -->
         <div class="tab-pane fade" id="sedes" role="tabpanel" aria-labelledby="tab-sedes">
            <div class="row mt-5">
                <div class="col-md-12">
                    <form id="form-sedes">
                        <input type="hidden" id="idSede" value="">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="nombreSede" class="form-label">Nombre de la sede *</label>
                                <input type="text" class="form-control" id="nombreSede"name="nombreSede" placeholder="Ej: Sede Central, Sede Norte..." required>
                            </div>

                            <div class="col-md-3">
                                <label for="direccionSede" class="form-label">Dirección de la sede *</label>
                                <input type="text" class="form-control" id="direccionSede"name="direccionSede" placeholder="Ej: Calle 123, Ciudad..." required>
                            </div>

                            <div class="col-md-3">
                                <label for="responsableSede" class="form-label">Responsable de la sede *</label>
                                <input type="text" class="form-control" id="responsableSede"name="responsableSede" placeholder="Ej: Juan Pérez..." required>
                            </div>

                            <div class="col-md-3">
                                <label for="telefonoSede" class="form-label">Teléfono de la sede *</label>
                                <input type="text" class="form-control" id="telefonoSede"name="telefonoSede" placeholder="Ej: 3177116506">
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 d-flex align-items-end justify-content-end" >
                                <div class="col-md-2">
                                    <button class="btn btn-primary btnRegistro w-100" id="btnRegistrarSede"type="submit">Registrar</button>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="table-responsive mt-4">
                        <table id="tablaSedes" class="table table-striped table-hover table-bordered custom-table table-dark text-center">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Sede</th>
                                    <th>Dirección</th>
                                    <th>Responsable</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

         <!-- TAB 4:  Usuarios-->
         <div class="tab-pane fade" id="usuarios" role="tabpanel" aria-labelledby="tab-usuarios">
            <div class="row mt-5">
                <div class="col-md-12">
                    <form id="form-usuarios">
                        <input type="hidden" id="idUsuario" value="">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="nombreUsuario" class="form-label">Nombre del usuario *</label>
                                <input type="text" class="form-control" id="nombreUsuario"name="nombreUsuario" placeholder="Ej: Usuario A, Usuario B..." required>
                            </div>

                            <div class="col-md-3">
                                <label for="userUsuario" class="form-label">Usuario *</label>
                                <input type="text" class="form-control" id="userUsuario" name="userUsuario" placeholder="Ej: usuario123" autocomplete="new-username" data-lpignore="true" required>
                            </div>

                            <div class="col-md-3">
                                <label for="passwordUsuario" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="passwordUsuario" name="passwordUsuario" placeholder="Ej: ********" autocomplete="new-password" data-lpignore="true" required>
                            </div>

                            <div class="col-md-3">
                                <label for="perfilUsuario" class="form-label">Perfil usuario *</label>
                                <select class="form-select" id="perfilUsuario" name="perfilUsuario" required>
                                    <option value="">Seleccione un perfil</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12 d-flex align-items-end justify-content-end" >
                                <div class="col-md-2">
                                    <button class="btn btn-primary btnRegistro w-100" id="btnRegistrarUsuario"type="submit">Registrar</button>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="table-responsive mt-4">
                        <table id="tablaUsuarios" class="table table-striped table-hover table-bordered custom-table table-dark text-center">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre usuario</th>
                                    <th>Usuario</th>
                                    <th>Contraseña</th>
                                    <th>Perfil</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5:  Perfil usuario-->
         <div class="tab-pane fade" id="perfilUsuarios" role="tabpanel" aria-labelledby="tab-perfilUsuarios">
            <div class="row mt-5">
                <div class="col-md-12">
                    <form id="form-perfiles">
                        <input type="hidden" id="idPerfil" value="">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="nombrePerfil" class="form-label">Nombre del perfil *</label>
                                <input type="text" class="form-control" id="nombrePerfil"name="nombrePerfil" placeholder="Ej: Perfil A, Perfil B..." required>
                            </div>

                            <div class="col-md-8">
                                <label for="descripcionPerfil" class="form-label">Descripción del perfil *</label>
                                <input type="text" class="form-control" id="descripcionPerfil"name="descripcionPerfil" placeholder="Ej: Descripción del perfil..." required>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 d-flex align-items-end justify-content-end" >
                                <div class="col-md-2">
                                    <button class="btn btn-primary btnRegistro w-100" id="btnRegistrarPerfil"type="submit">Registrar</button>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="table-responsive mt-4">
                        <table id="tablaPerfiles" class="table table-striped table-hover table-bordered custom-table table-dark text-center">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Perfil</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 6:  Permisos-->
         <div class="tab-pane fade" id="permisos" role="tabpanel" aria-labelledby="tab-permisos">
            <div class="row mt-5">
                <div class="col-md-12">
                    <form id="form-permisos">
                        <input type="hidden" id="idPermiso" value="">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="nombrePermiso" class="form-label">Nombre del permiso *</label>
                                <input type="text" class="form-control" id="nombrePermiso"name="nombrePermiso" placeholder="Ej: Permiso A, Permiso B..." required>
                            </div>

                            <div class="col-md-8">
                                <label for="descripcionPermiso" class="form-label">Descripción del permiso *</label>
                                <input type="text" class="form-control" id="descripcionPermiso"name="descripcionPermiso" placeholder="Ej: Descripción del permiso..." required>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 d-flex align-items-end justify-content-end" >
                                <div class="col-md-2">
                                    <button class="btn btn-primary btnRegistro w-100" id="btnRegistrarPermiso"type="submit">Registrar</button>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="table-responsive mt-4">
                        <table id="tablaPermisos" class="table table-striped table-hover table-bordered custom-table table-dark text-center">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Permiso</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 7:  PerfilUsuario-Permisos-->
         <div class="tab-pane fade" id="perfilUsuariosPermisos" role="tabpanel" aria-labelledby="tab-perfilUsuariosPermisos">
            <div class="row mt-5">
                <div class="col-md-12">
                    <form id="form-perfilUsuariosPermisos">
                        <div class="row">
                            <div class="col-md-10">
                                <label for="perfilUsuarioPermiso" class="form-label">Seleccionar Perfil *</label>
                                <select class="form-select" id="perfilUsuarioPermiso" name="perfilUsuarioPermiso" required>
                                    <option value="">Seleccione un perfil</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary btnRegistro w-100" id="btnAsignarPermisos" type="submit" disabled>Asignar</button>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-12">
                                <label class="form-label fw-bold mb-3">Seleccionar Permisos para este Perfil</label>
                                <div id="containerCheckboxPermisos" class="row gy-2" style="overflow-y:auto;">
                                    <div class="col-12 text-center text-muted" id="placeholderPermisos">Seleccione un perfil para ver los permisos disponibles</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="table-responsive mt-4">
                        <table id="tablaPerfilPermisos" class="table table-striped table-hover table-bordered custom-table table-dark text-center">
                            <thead class="text-center">
                                <tr>
                                    <th>Perfil</th>
                                    <th>Permisos Asignados</th>
                                    <th>Cantidad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>