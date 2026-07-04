const ConfiguracionAPI = {
    catalogoClient: new SimpleAPI(CONFIG.AJAX.CATALOGO),
    usuariosClient: new SimpleAPI(CONFIG.AJAX.USUARIOS),

    // CATEGORIAS DE PRODUCTOS
    async categoriasProductos() {
        const response = await this.catalogoClient.get({ accion: 'listadoCategorias' });
        return await response && response.data ? response.data : [];
    },

    async crearCategoriasProductos(formData) {
        return await this.catalogoClient.post(formData);
    },

    async actualizarCategoriasProductos(formData) {
        return await this.catalogoClient.post(formData);
    },

    async eliminarCategoriasProductos(formData) {
        return await this.catalogoClient.post(formData);
    },

    // TIPOS DE PRODUCTOS
    async obtenerTiposProductos() {
        const response = await this.catalogoClient.get({ accion: 'listadoTiposProductos' });
        return await response && response.data ? response.data : [];
    },

    async crearTiposProductos(formData) {
        return await this.catalogoClient.post(formData);
    },

    async actualizarTiposProductos(formData) {
        return await this.catalogoClient.post(formData);
    },

    async eliminarTiposProductos(formData) {
        return await this.catalogoClient.post(formData);
    },

    // SEDES
    async obtenerSedes() {
        const response = await this.catalogoClient.get({ accion: 'listadoSedes' });
        return await response && response.data ? response.data : [];
    },

    async crearSedes(formData) {
        return await this.catalogoClient.post(formData);
    },

    async actualizarSedes(formData) {
        return await this.catalogoClient.post(formData);
    },

    async eliminarSedes(formData) {
        return await this.catalogoClient.post(formData);
    },

    // PERMISOS
    async obtenerPermisos() {
        const response = await this.usuariosClient.get({ accion: 'listadoPermisos' });
        return await response && response.data ? response.data : [];
    },

    async crearPermisos(formData) {
        return await this.usuariosClient.post(formData);
    },

    async actualizarPermisos(formData) {
        return await this.usuariosClient.post(formData);
    },

    async eliminarPermisos(formData) {
        return await this.usuariosClient.post(formData);
    },

    // PERFIL DE USUARIO
    async obtenerPerfilUsuario() {
        const response = await this.usuariosClient.get({ accion: 'listadoPerfiles' });
        return await response && response.data ? response.data : [];
    },

    async crearPerfilUsuario(formData) {
        return await this.usuariosClient.post(formData);
    },

    async actualizarPerfilUsuario(formData) {
        return await this.usuariosClient.post(formData);
    },

    async eliminarPerfilUsuario(formData) {
        return await this.usuariosClient.post(formData);
    },

    // USUARIOS
    async obtenerUsuarios() {
        const response = await this.usuariosClient.get({ accion: 'listadoUsuarios' });
        return await response && response.data ? response.data : [];
    },

    async registrarUsuario(formData) {
        return await this.usuariosClient.post(formData);
    },

    async actualizarUsuarios(formData) {
        return await this.usuariosClient.post(formData);
    },

    async eliminarUsuarios(formData) {
        return await this.usuariosClient.post(formData);
    },

    // PERFIL - PERMISOS
    async obtenerPermisosDelPerfil(idPerfil) {
        const response = await this.usuariosClient.get({
            accion: 'obtenerPermisosDelPerfil',
            idPerfil: idPerfil
        });
        return await response && response.data ? response.data : [];
    },

    async listadoPerfilPermisos() {
        const response = await this.usuariosClient.get({ accion: 'listadoPerfilPermisos' });
        return await response && response.data ? response.data : [];
    },

    async asignarPermisosAlPerfil(formData) {
        return await this.usuariosClient.post(formData);
    },

    async eliminarPermisosDelPerfil(formData) {
        return await this.usuariosClient.post(formData);
    }
};

const ConfiguracionView = {

    // CATEGORIAS DE PRODUCTOS
    renderCategoriasProductos(categorias) {
        const tbody = document.querySelector('#tablaCategoriaProductos tbody');
        tbody.innerHTML = '';

        if (categorias.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">No hay categorías registradas.</td></tr>';
            return;
        }

        categorias.forEach(categoria => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${categoria.id_categoria}</td>
                <td>${categoria.nombre_categoria}</td>
                <td>
                    <button class="btn btn-sm btn-warning btnEditar" data-id="${categoria.id_categoria}"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-sm btn-danger btnEliminar" data-id="${categoria.id_categoria}"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    // TIPOS DE PRODUCTOS
    renderTiposProductos(tipos) {
        const tbody = document.querySelector('#tablaTipoProductos tbody');
        tbody.innerHTML = '';

        if (tipos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">No hay tipos de productos registrados.</td></tr>';
            return;
        }

        tipos.forEach(tipo => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${tipo.id_tipo_producto}</td>
                <td>${tipo.descripcion_tipo_producto}</td>
                <td>
                    <button class="btn btn-sm btn-warning btnEditar" data-id="${tipo.id_tipo_producto}"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-sm btn-danger btnEliminar" data-id="${tipo.id_tipo_producto}"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    // SEDES
    renderSedes(sedes) {
        const tbody = document.querySelector('#tablaSedes tbody');
        tbody.innerHTML = '';

        if (sedes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No hay sedes registradas.</td></tr>';
            return;
        }

        sedes.forEach(sede => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${sede.id_sede}</td>
                <td>${sede.nombre_sede}</td>
                <td>${sede.direccion_sede}</td>
                <td>${sede.responsable_sede}</td>
                <td>${sede.telefono_sede}</td>
                <td>
                    <button class="btn btn-sm btn-warning btnEditar" data-id="${sede.id_sede}"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-sm btn-danger btnEliminar" data-id="${sede.id_sede}"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    renderFormularioEditarSede(sede) {
        $("#idSede").val(sede ? sede.id_sede : '');
        $("#nombreSede").val(sede ? sede.nombre_sede : '');
        $("#direccionSede").val(sede ? sede.direccion_sede : '');
        $("#responsableSede").val(sede ? sede.responsable_sede : '');
        $("#telefonoSede").val(sede ? sede.telefono_sede : '');
        $("#btnRegistrarSede").html(sede ? 'Actualizar' : 'Registrar');
    },

    // PERMISOS
    renderPermisos(permisos) {
        const tbody = document.querySelector('#tablaPermisos tbody');
        tbody.innerHTML = '';
        
        if (permisos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">No hay permisos registrados.</td></tr>';
            return;
        }

        permisos.forEach(permiso => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${permiso.id_permiso}</td>
                <td>${permiso.nombre_permiso}</td>
                <td>${permiso.descripcion_permiso}</td>
                <td>
                    <button class="btn btn-sm btn-warning btnEditar" data-id="${permiso.id_permiso}"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-sm btn-danger btnEliminar" data-id="${permiso.id_permiso}"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    // TIPOS USUARIO
    renderPerfiles(tiposUsuario) {
        const tbody = document.querySelector('#tablaPerfiles tbody');
        tbody.innerHTML = '';
        if (tiposUsuario.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">No hay tipos de usuario registrados.</td></tr>';
            return;
        }

        tiposUsuario.forEach(perfil => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${perfil.id_perfil_usuario}</td>
                <td>${perfil.nombre_perfil_usuario}</td>
                <td>${perfil.descripcion_perfil_usuario || ''}</td>
                <td>
                    <button class="btn btn-sm btn-warning btnEditar" data-id="${perfil.id_perfil_usuario}"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-sm btn-danger btnEliminar" data-id="${perfil.id_perfil_usuario}"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    // USUARIOS
    renderUsuarios(usuarios) {
        const tbody = document.querySelector('#tablaUsuarios tbody');
        tbody.innerHTML = '';
        if (usuarios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7">No hay usuarios registrados.</td></tr>';
            return;
        }
        usuarios.forEach(usuario => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${usuario.id_usuario}</td>
                <td>${usuario.nombre_usuario}</td>
                <td>${usuario.user_usuario}</td>
                <td>${usuario.pass_usaurio}</td>
                <td>${usuario.nombre_perfil_usuario}</td>
                <td>${usuario.nombre_estado}</td>
                <td>
                    <button class="btn btn-sm btn-warning btnEditar" data-id="${usuario.id_usuario}"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-sm btn-danger btnEliminar" data-id="${usuario.id_usuario}"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    },

    renderFormularioEditarUsuario(usuario) {
        $("#idUsuario").val(usuario ? usuario.id_usuario : '');
        $("#nombreUsuario").val(usuario ? usuario.nombre_usuario : '');
        $("#userUsuario").val(usuario ? usuario.user_usuario : '');
        $("#perfilUsuario").val(usuario ? usuario.id_perfil_usuario : '');
        $("#estadoUsuario").val(usuario ? usuario.id_estado : '');
        $("#btnRegistrarUsuario").html(usuario ? 'Actualizar' : 'Registrar');
    },

    renderSeleccionPerfil(perfiles) {
        const select = document.querySelector('#perfilUsuario');
        select.innerHTML = '<option value="">Seleccione un perfil</option>';

        if (perfiles.length === 0) {
            console.warn('No hay perfiles disponibles');
            return;
        }

        perfiles.forEach(perfil => {
            const option = document.createElement('option');
            option.value = perfil.id_perfil_usuario || perfil.id_perfil || '';
            option.textContent = perfil.nombre_perfil_usuario || perfil.nombre_perfil || 'Sin nombre';
            console.log('Agregando perfil:', option.value, option.textContent);
            select.appendChild(option);
        });
    },

    // Agregar métodos al ConfiguracionView para perfil-permisos
    renderCheckboxPermisos(permisos, idsAsignados) {
        const container = document.getElementById('containerCheckboxPermisos');
        container.innerHTML = '';

        if (!Array.isArray(permisos) || permisos.length === 0) {
            container.innerHTML = '<p class="text-muted text-center pt-5">No hay permisos disponibles</p>';
            return;
        }

        console.log('Renderizando permisos:', permisos);
        console.log('IDs asignados:', idsAsignados);

        // Usar grid de 3 columnas (responsive con Bootstrap)
        const placeholder = document.getElementById('placeholderPermisos');
        if (placeholder) placeholder.remove();

        let html = '<div class="row">';

        permisos.forEach(permiso => {
            const idPermiso = permiso.id_permiso || '';
            const nombrePermiso = permiso.nombre_permiso || 'Sin nombre';
            const descripcionPermiso = permiso.descripcion_permiso || '';
            const isChecked = idsAsignados.includes(Number(idPermiso)) ? 'checked' : '';

            html += `
                <div class="col-12 col-md-4 mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="permiso_${idPermiso}"
                            value="${idPermiso}" ${isChecked}>
                        <label class="form-check-label" for="permiso_${idPermiso}">
                            <strong>${nombrePermiso}</strong>
                            <div class="text-muted small">${descripcionPermiso}</div>
                        </label>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
        // Añadir separación para empujar la tabla abajo solo cuando se muestran permisos
        container.classList.add('mt-3');
    },

    renderSeleccionPerfilPermisos(perfiles) {
        const select = document.querySelector('#perfilUsuarioPermiso');
        select.innerHTML = '<option value="">Seleccione un perfil</option>';

        if (perfiles.length === 0) {
            console.warn('No hay perfiles disponibles');
            return;
        }

        perfiles.forEach(perfil => {
            const option = document.createElement('option');
            option.value = perfil.id_perfil_usuario || perfil.id_perfil || '';
            option.textContent = perfil.nombre_perfil_usuario || perfil.nombre_perfil || 'Sin nombre';
            select.appendChild(option);
        });
    },

    renderTablaPerfilPermisos(datos) {
    const tbody = document.querySelector('#tablaPerfilPermisos tbody');
    tbody.innerHTML = '';

    if (!Array.isArray(datos) || datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay relaciones perfil-permisos registradas.</td></tr>';
        return;
    }

    datos.forEach(item => {
        const idPerfil = item.id_perfil_usuario || item.id_perfil || '';
        const nombrePerfil = item.nombre_perfil_usuario || item.nombre_perfil || 'Sin nombre';
        const permisosAsignados = item.permisos_asignados || 'Sin permisos asignados';
        const cantidad = item.cantidad_permisos || 0;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><strong>${nombrePerfil}</strong></td>
            <td style="text-align: left; font-size: 1.6rem; max-width: 400px;">
                <small>${permisosAsignados}</small>
            </td>
            <td>
                <span class="badge bg-primary">${cantidad}</span>
            </td>
            <td>
                <button class="btn btn-sm btn-danger btnEliminarPermisos"
                        data-id="${idPerfil}" title="Eliminar todos los permisos">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}
};

const ConfiguracionModule = {
    init() {
        this.cargarDatosIniciales();
        this.bindEvents();
    },

    bindEvents() {
        const self = this;

        /* Categorías de Productos */
        $("#tab-categoriaProductos").on("click", async () => this.listadoCategoriasProductos());
        $("#form-categoria-productos").on("submit", async (e) => this.crearCategoriaProducto(e));
        $("#tablaCategoriaProductos").on("click", ".btnEditar", (e) => {
            const idCategoria = $(e.currentTarget).data("id");
            this.obtenerCategoriaProducto(idCategoria);
        });
        $("#tablaCategoriaProductos").on("click", ".btnEliminar", (e) => {
            const idCategoria = $(e.currentTarget).data("id");
            this.eliminarCategoriaProducto(idCategoria);
        });

        /* Tipos de Productos */
        $("#tab-tiposProductos").on("click", async () => this.listadoTiposProductos());
        $("#form-tipo-productos").on("submit", async (e) => this.crearTipoProducto(e));
        $("#tablaTipoProductos").on("click", ".btnEditar", (e) => {
            const idTipoProducto = $(e.currentTarget).data("id");
            this.obtenerTipoProducto(idTipoProducto);
        });
        $("#tablaTipoProductos").on("click", ".btnEliminar", (e) => {
            const idTipoProducto = $(e.currentTarget).data("id");
            this.eliminarTipoProducto(idTipoProducto);
        });

        /* Sedes */
        $("#tab-sedes").on("click", async () => this.listadoSedes());
        $("#form-sedes").on("submit", async (e) => this.crearSede(e));
        $("#tablaSedes").on("click", ".btnEditar", (e) => {
            const idSede = $(e.currentTarget).data("id");
            this.obtenerSede(idSede);
        });
        $("#tablaSedes").on("click", ".btnEliminar", (e) => {
            const idSede = $(e.currentTarget).data("id");
            this.eliminarSede(idSede);
        });

        /* Permisos */
        $("#tab-permisos").on("click", async () => this.listadoPermisos());
        $("#form-permisos").on("submit", async (e) => this.crearPermiso(e));
        $("#tablaPermisos").on("click", ".btnEditar", (e) => {
            const idPermiso = $(e.currentTarget).data("id");
            this.obtenerPermiso(idPermiso);
        });
        $("#tablaPermisos").on("click", ".btnEliminar", (e) => {
            const idPermiso = $(e.currentTarget).data("id");
            this.eliminarPermiso(idPermiso);
        });

        /* Perfiles de Usuario */
        $("#tab-perfilUsuarios").on("click", async () => this.listadoPerfiles());
        $("#form-perfiles").on("submit", async (e) => this.crearPerfil(e));
        $("#tablaPerfiles").on("click", ".btnEditar", (e) => {
            const idPerfil = $(e.currentTarget).data("id");
            this.obtenerPerfil(idPerfil);
        });
        $("#tablaPerfiles").on("click", ".btnEliminar", (e) => {
            const idPerfil = $(e.currentTarget).data("id");
            this.eliminarPerfil(idPerfil);
        });

        /* Usuarios */
        $("#tab-usuarios").on("click", async () => this.listadoUsuarios());
        $("#form-usuarios").on("submit", async (e) => this.crearUsuario(e));
        $("#tablaUsuarios").on("click", ".btnEditar", (e) => {
            const idUsuario = $(e.currentTarget).data("id");
            this.obtenerUsuario(idUsuario);
        });
        $("#tablaUsuarios").on("click", ".btnEliminar", (e) => {
            const idUsuario = $(e.currentTarget).data("id");
            this.eliminarUsuario(idUsuario);
        });

        /* Perfil Usuario - Permisos */
        $("#tab-perfilUsuariosPermisos").on("click", async () => {
            await this.listadoPerfilPermisos();
        });

        $(document).on("change", "#perfilUsuarioPermiso", async function() {
            const idPerfil = $(this).val();
            await self.cargarPermisosDelPerfil(idPerfil);
        });

        $("#form-perfilUsuariosPermisos").on("submit", async (e) => {
            await this.asignarPermisosAlPerfil(e);
        });

        $(document).on("click", ".btnEliminarPermisos", async function() {
            const idPerfil = $(this).data("id");
            await self.eliminarPermisoDelPerfil(idPerfil);
        });
    },

    cargarDatosIniciales() {
        this.listadoCategoriasProductos();

    },

    //TODO: implemetacion de lasas funciones para categorias de productos
    async listadoCategoriasProductos() {
        try {
            $("#form-categoria-productos")[0].reset();
            $("#btnRegistrarCategoria").html('Registrar');
            const categorias = await ConfiguracionAPI.categoriasProductos();
            ConfiguracionView.renderCategoriasProductos(categorias);
        } catch (error) {
            console.error('Error al cargar categorías de productos:', error);
        }
    }, 

    async crearCategoriaProducto(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'registrarCategoria');
        if (!formData.get('nombreCategoria')) {
            Alerts.warning("Cuidado", "El nombre de la categoría es obligatorio");
            return;
        }
        try {
            const response = await ConfiguracionAPI.crearCategoriasProductos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Categoría creada correctamente");
                this.listadoCategoriasProductos();
                e.target.reset();
            } else {
                Alerts.error("Error", "No se pudo crear la categoría");
            }
        } catch (error) {
            console.error('Error al crear categoría de producto:', error);
            Alerts.error("Error", "Ocurrió un error al crear la categoría");
        }
    },

    async obtenerCategoriaProducto(idCategoria) {
        try {
            const categorias = await ConfiguracionAPI.categoriasProductos();
            const dataCategoria = categorias.find(categoria => categoria.id_categoria === idCategoria) || null;
            $("#idCategoria").val(dataCategoria ? dataCategoria.id_categoria : '');
            $("#nombreCategoria").val(dataCategoria ? dataCategoria.nombre_categoria : '');
            $("#btnRegistrarCategoria").html('Actualizar');
            $("#form-categoria-productos").off("submit").on("submit", async (e) => this.actualizarCategoriaProducto(e));
            return dataCategoria;
        } catch (error) {
            console.error('Error al obtener categoría de producto:', error);
            return null;
        }
    },

    async actualizarCategoriaProducto(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'actualizarCategoria');
        formData.append('idCategoria', $("#idCategoria").val());
        if (!formData.get('nombreCategoria')) {
            Alerts.warning("Cuidado", "El nombre de la categoría es obligatorio");
            return;
        }
        try {
            const result = await Alerts.confirmation("Actualizar categoría", "¿Está seguro de que desea actualizar esta categoría?");
            if (!result.isConfirmed) return;

            const response = await ConfiguracionAPI.actualizarCategoriasProductos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Categoría actualizada correctamente");
                this.listadoCategoriasProductos();
                e.target.reset();
                $("#btnRegistrarCategoria").html('Registrar');
            } else {
                Alerts.error("Error", "No se pudo actualizar la categoría");
            }
        } catch (error) {
            console.error('Error al actualizar categoría de producto:', error);
            Alerts.error("Error", "Ocurrió un error al actualizar la categoría");
        }
    },

    async eliminarCategoriaProducto(idCategoria) {
        try {
            const result = await Alerts.confirmation("Eliminar categoría", "¿Está seguro de que desea eliminar esta categoría?");
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('accion', 'eliminarCategoria');
            formData.append('idCategoria', idCategoria);
            const response = await ConfiguracionAPI.eliminarCategoriasProductos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Categoría eliminada correctamente");
                this.listadoCategoriasProductos();
            } else {
                Alerts.error("Error", "No se pudo eliminar la categoría");
            }
        } catch (error) {
            console.error('Error al eliminar categoría de producto:', error);
            Alerts.error("Error", "Ocurrió un error al eliminar la categoría");
        }
    },

    //TODO: Implementación de funciones para tipos de productos
    async listadoTiposProductos() {
        try {
            $("#form-tipo-productos")[0].reset();
            $("#btnRegistrarTipoProducto").html('Registrar');
            const tipos = await ConfiguracionAPI.obtenerTiposProductos();
            ConfiguracionView.renderTiposProductos(tipos);
        } catch (error) {
            console.error('Error al cargar tipos de productos:', error);
        }
    },

    async crearTipoProducto(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'registrarTipoProducto');
        if (!formData.get('descripcionTipoProducto')) {
            Alerts.warning("Cuidado", "El nombre del tipo de producto es obligatorio");
            return;
        }
        try {
            const response = await ConfiguracionAPI.crearTiposProductos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Tipo de producto creado correctamente");
                this.listadoTiposProductos();
                e.target.reset();
            } else {
                Alerts.error("Error", "No se pudo crear el tipo de producto");
            }
        } catch (error) {
            console.error('Error al crear tipo de producto:', error);
            Alerts.error("Error", "Ocurrió un error al crear el tipo de producto");
        }
    },

    async obtenerTipoProducto(idTipoProducto) {
        try {
            const tipos = await ConfiguracionAPI.obtenerTiposProductos();
            const dataTipo = tipos.find(tipo => tipo.id_tipo_producto === idTipoProducto) || null;
            $("#idTipoProducto").val(dataTipo ? dataTipo.id_tipo_producto : '');
            $("#descripcionTipoProducto").val(dataTipo ? dataTipo.descripcion_tipo_producto : '');
            $("#btnRegistrarTipoProducto").html('Actualizar');
            $("#form-tipo-productos").off("submit").on("submit", async (e) => this.actualizarTiposProductos(e));
            return dataTipo;
        } catch (error) {
            console.error('Error al obtener tipo de producto:', error);
            return null;
        }
    },

    async actualizarTiposProductos(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'actualizarTipoProducto');
        formData.append('idTipoProducto', $("#idTipoProducto").val());
        if (!formData.get('descripcionTipoProducto')) {
            Alerts.warning("Cuidado", "El nombre del tipo de producto es obligatorio");
            return;
        }
        try {
            const result = await Alerts.confirmation("Actualizar tipo de producto", "¿Está seguro de que desea actualizar este tipo de producto?");
            if (!result.isConfirmed) return;

            const response = await ConfiguracionAPI.actualizarTiposProductos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Tipo de producto actualizado correctamente");
                this.listadoTiposProductos();
                e.target.reset();
                $("#btnRegistrarTipoProducto").html('Registrar');
            } else {
                Alerts.error("Error", "No se pudo actualizar el tipo de producto");
            }
        } catch (error) {
            console.error('Error al actualizar tipo de producto:', error);
            Alerts.error("Error", "Ocurrió un error al actualizar el tipo de producto");
        }
    },

    async eliminarTipoProducto(idTipoProducto) {
        try {
            const result = await Alerts.confirmation("Eliminar tipo de producto", "¿Está seguro de que desea eliminar este tipo de producto?");
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('accion', 'eliminarTipoProducto');
            formData.append('idTipoProducto', idTipoProducto);
            const response = await ConfiguracionAPI.eliminarTiposProductos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Tipo de producto eliminado correctamente");
                this.listadoTiposProductos();
            } else {
                Alerts.error("Error", "No se pudo eliminar el tipo de producto");
            }
        } catch (error) {
            console.error('Error al eliminar tipo de producto:', error);
            Alerts.error("Error", "Ocurrió un error al eliminar el tipo de producto");
        }
    },

    //TODO: Implementacion de funciones para sedes
    async listadoSedes() {
        try {
            $("#form-sedes")[0].reset();
            $("#btnRegistrarSede").html('Registrar');
            const sedes = await ConfiguracionAPI.obtenerSedes();
            ConfiguracionView.renderSedes(sedes);
        } catch (error) {
            console.error('Error al cargar sedes:', error);
        }
    },

    async crearSede(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'registrarSede');
        if (!formData.get('nombreSede') || !formData.get('direccionSede') || !formData.get('responsableSede')) {
            Alerts.warning("Cuidado", "Los campos de nombre, dirección y responsable de la sede son obligatorios");
            return;
        }
        try {
            const response = await ConfiguracionAPI.crearSedes(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Sede creada correctamente");
                this.listadoSedes();
                e.target.reset();
            } else {
                Alerts.error("Error", "No se pudo crear la sede");
            }
        } catch (error) {
            console.error('Error al crear sede:', error);
            Alerts.error("Error", "Ocurrió un error al crear la sede");
        }
    }, 

    async obtenerSede(idSede) {
        try {
            const sedes = await ConfiguracionAPI.obtenerSedes();
            const dataSede = sedes.find(sede => sede.id_sede === idSede) || null;
            ConfiguracionView.renderFormularioEditarSede(dataSede);
            $("#form-sedes").off("submit").on("submit", async (e) => this.actualizarSedes(e));
            return dataSede;
            
        } catch (error) {
            console.error('Error al obtener sede:', error);
            return null;
        }
    },

    async actualizarSedes(e) {
        e.preventDefault();
        const formData = new FormData(e.target);

        formData.append('accion', 'actualizarSede');
        formData.append('idSede', $("#idSede").val());
        if (!formData.get('nombreSede')) {
            Alerts.warning("Cuidado", "El nombre de la sede es obligatorio");
            return;
        }
        try {
            const result = await Alerts.confirmation("Actualizar sede", "¿Está seguro de que desea actualizar esta sede?");
            if (!result.isConfirmed) return;

            const response = await ConfiguracionAPI.actualizarSedes(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Sede actualizada correctamente");
                this.listadoSedes();
                e.target.reset();
                $("#btnRegistrarSede").html('Registrar');
            } else {
                Alerts.error("Error", "No se pudo actualizar la sede");
            }
        } catch (error) {
            console.error('Error al actualizar sede:', error);
            Alerts.error("Error", "Ocurrió un error al actualizar la sede");
        }
    },

    async eliminarSede(idSede) {
        try {
            const result = await Alerts.confirmation("Eliminar sede", "¿Está seguro de que desea eliminar esta sede?");
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('accion', 'eliminarSede');
            formData.append('idSede', idSede);
            const response = await ConfiguracionAPI.eliminarSedes(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Sede eliminada correctamente");
                this.listadoSedes();
            } else {
                Alerts.error("Error", "No se pudo eliminar la sede");
            }
        } catch (error) {
            console.error('Error al eliminar sede:', error);
            Alerts.error("Error", "Ocurrió un error al eliminar la sede");
        }
    },

    //TODO: Implementación de funciones para permisos
    async listadoPermisos() {
        try {
            $("#form-permisos")[0].reset();
            $("#btnRegistrarPermiso").html('Registrar');
            const permisos = await ConfiguracionAPI.obtenerPermisos();
            ConfiguracionView.renderPermisos(permisos);
            
        } catch (error) {
            console.error('Error al cargar permisos:', error);
        }
    },

    async crearPermiso(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'registrarPermiso');
        if (!formData.get('nombrePermiso')) {
            Alerts.warning("Cuidado", "El nombre del permiso es obligatorio");
            return;
        }
        try {
            const response = await ConfiguracionAPI.crearPermisos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Permiso creado correctamente");
                this.listadoPermisos();
                e.target.reset();
            } else {
                Alerts.error("Error", "No se pudo crear el permiso");
            }
        } catch (error) {
            console.error('Error al crear permiso:', error);
            Alerts.error("Error", "Ocurrió un error al crear el permiso");
        }
    },

    async obtenerPermiso(idPermiso) {
        try {
            const permisos = await ConfiguracionAPI.obtenerPermisos();
            const dataPermiso = permisos.find(permiso => permiso.id_permiso === idPermiso) || null;
            $("#idPermiso").val(dataPermiso ? dataPermiso.id_permiso : '');
            $("#nombrePermiso").val(dataPermiso ? dataPermiso.nombre_permiso : '');
            $("#descripcionPermiso").val(dataPermiso ? dataPermiso.descripcion_permiso : '');
            $("#btnRegistrarPermiso").html('Actualizar');
            $("#form-permisos").off("submit").on("submit", async (e) => this.actualizarPermisos(e));
            return dataPermiso;
        } catch (error) {
            console.error('Error al obtener permiso:', error);
            return null;
        }
    },

    async actualizarPermisos(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'actualizarPermiso');
        formData.append('idPermiso', $("#idPermiso").val());
        if (!formData.get('nombrePermiso')) {
            Alerts.warning("Cuidado", "El nombre del permiso es obligatorio");
            return;
        }
        try {
            const result = await Alerts.confirmation("Actualizar permiso", "¿Está seguro de que desea actualizar este permiso?");
            if (!result.isConfirmed) return;

            const response = await ConfiguracionAPI.actualizarPermisos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Permiso actualizado correctamente");
                this.listadoPermisos();
                e.target.reset();
                $("#btnRegistrarPermiso").html('Registrar');
            } else {
                Alerts.error("Error", "No se pudo actualizar el permiso");
            }
        } catch (error) {
            console.error('Error al actualizar permiso:', error);
            Alerts.error("Error", "Ocurrió un error al actualizar el permiso");
        }
    },

    async eliminarPermiso(idPermiso) {
        try {
            const result = await Alerts.confirmation("Eliminar permiso", "¿Está seguro de que desea eliminar este permiso?");
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('accion', 'eliminarPermiso');
            formData.append('idPermiso', idPermiso);
            const response = await ConfiguracionAPI.eliminarPermisos(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Permiso eliminado correctamente");
                this.listadoPermisos();
            } else {
                Alerts.error("Error", "No se pudo eliminar el permiso");
            }
        } catch (error) {
            console.error('Error al eliminar permiso:', error);
            Alerts.error("Error", "Ocurrió un error al eliminar el permiso");
        }
    },

    //TODO: Implementación de funciones para perfiles de usuario
    async listadoPerfiles() {
        try {
            $("#form-perfiles")[0].reset();
            $("#btnRegistrarPerfil").html('Registrar');
            const perfiles = await ConfiguracionAPI.obtenerPerfilUsuario();
            ConfiguracionView.renderPerfiles(perfiles);
        } catch (error) {
            console.error('Error al cargar perfiles de usuario:', error);
        }
    },

    async crearPerfil(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'registrarPerfil');
        if (!formData.get('nombrePerfil')) {
            Alerts.warning("Cuidado", "El nombre del perfil es obligatorio");
            return;
        }
        try {
            const response = await ConfiguracionAPI.crearPerfilUsuario(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Perfil de usuario creado correctamente");
                this.listadoPerfiles();
                e.target.reset();
            } else {
                Alerts.error("Error", "No se pudo crear el perfil de usuario");
            }
        } catch (error) {
            console.error('Error al crear perfil de usuario:', error);
            Alerts.error("Error", "Ocurrió un error al crear el perfil de usuario");
        }
    },

    async obtenerPerfil(idPerfil) {
        try {
            const perfiles = await ConfiguracionAPI.obtenerPerfilUsuario();
            const dataPerfil = perfiles.find(perfil => perfil.id_perfil_usuario === idPerfil) || null;
            $("#idPerfil").val(dataPerfil ? dataPerfil.id_perfil_usuario : '');
            $("#nombrePerfil").val(dataPerfil ? dataPerfil.nombre_perfil_usuario : '');
            $("#descripcionPerfil").val(dataPerfil ? dataPerfil.descripcion_perfil_usuario : '');
            $("#btnRegistrarPerfil").html('Actualizar');
            $("#form-perfiles").off("submit").on("submit", async (e) => this.actualizarPerfiles(e));
            return dataPerfil;
        } catch (error) {
            console.error('Error al obtener perfil de usuario:', error);
            return null;
        }
    },

    async actualizarPerfiles(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'actualizarPerfil');
        formData.append('idPerfil', $("#idPerfil").val());
        if (!formData.get('nombrePerfil')) {
            Alerts.warning("Cuidado", "El nombre del perfil es obligatorio");
            return;
        }
        try {
            const result = await Alerts.confirmation("Actualizar perfil", "¿Está seguro de que desea actualizar este perfil de usuario?");
            if (!result.isConfirmed) return;

            const response = await ConfiguracionAPI.actualizarPerfilUsuario(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Perfil de usuario actualizado correctamente");
                this.listadoPerfiles();
                e.target.reset();
                $("#btnRegistrarPerfil").html('Registrar');
            } else {
                Alerts.error("Error", "No se pudo actualizar el perfil de usuario");
            }
        } catch (error) {
            console.error('Error al actualizar perfil de usuario:', error);
            Alerts.error("Error", "Ocurrió un error al actualizar el perfil de usuario");
        }
    },

    async eliminarPerfil(idPerfil) {
        try {
            const result = await Alerts.confirmation("Eliminar perfil", "¿Está seguro de que desea eliminar este perfil de usuario?");
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('accion', 'eliminarPerfil');
            formData.append('idPerfil', idPerfil);
            const response = await ConfiguracionAPI.eliminarPerfilUsuario(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Perfil de usuario eliminado correctamente");
                this.listadoPerfiles();
            } else {
                Alerts.error("Error", "No se pudo eliminar el perfil de usuario");
            }
        } catch (error) {
            console.error('Error al eliminar perfil de usuario:', error);
            Alerts.error("Error", "Ocurrió un error al eliminar el perfil de usuario");
        }
    },

    //TODO: Implementación de funciones para usuarios
    async listadoUsuarios() {
        try {
            $("#form-usuarios")[0].reset();
            $("#btnRegistrarUsuario").html('Registrar');
            const usuarios = await ConfiguracionAPI.obtenerUsuarios();
            ConfiguracionView.renderUsuarios(usuarios);
            await this.obtenerPerfilesSelect();
        } catch (error) {
            console.error('Error al cargar usuarios:', error);
        }
    },

    async obtenerPerfilesSelect() {
        try {
            const perfiles = await ConfiguracionAPI.obtenerPerfilUsuario();
            ConfiguracionView.renderSeleccionPerfil(perfiles);
        } catch (error) {
            console.error('Error al obtener perfiles de usuario:', error);
            return [];
        }
    },

    async crearUsuario(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'registrarUsuario');
        if (!formData.get('nombreUsuario') || !formData.get('userUsuario') || !formData.get('perfilUsuario') || !formData.get('passwordUsuario')) {
            Alerts.warning("Cuidado", "Los campos de nombre, usuario y perfil de usuario son obligatorios");
            return;
        }
        try {
            const response = await ConfiguracionAPI.registrarUsuario(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Usuario creado correctamente");
                this.listadoUsuarios();
                e.target.reset();
            } else {
                Alerts.error("Error", "No se pudo crear el usuario");
            }
        } catch (error) {
            console.error('Error al crear usuario:', error);
            Alerts.error("Error", "Ocurrió un error al crear el usuario");
        }
    },
    
    async obtenerUsuario(idUsuario) {
        try {
            const usuarios = await ConfiguracionAPI.obtenerUsuarios();
            const dataUsuario = usuarios.find(usuario => usuario.id_usuario === idUsuario) || null;
            ConfiguracionView.renderFormularioEditarUsuario(dataUsuario);
            $("#form-usuarios").off("submit").on("submit", async (e) => this.actualizarUsuario(e));
            return dataUsuario;
        } catch (error) {
            console.error('Error al obtener usuario:', error);
            return null;
        }
    },
    
    async actualizarUsuario(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('accion', 'actualizarUsuario');
        formData.append('idUsuario', $("#idUsuario").val());
        if (!formData.get('nombreUsuario') || !formData.get('userUsuario') || !formData.get('perfilUsuario')) {
            Alerts.warning("Cuidado", "Los campos de nombre, usuario y perfil de usuario son obligatorios");
            return;
        }
        try {
            const result = await Alerts.confirmation("Actualizar usuario", "¿Está seguro de que desea actualizar este usuario?");
            if (!result.isConfirmed) return;
            
            const response = await ConfiguracionAPI.actualizarUsuarios(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Usuario actualizado correctamente");
                this.listadoUsuarios();
                e.target.reset();
                $("#btnRegistrarUsuario").html('Registrar');
            }
        } catch (error) {
            console.error('Error al actualizar usuario:', error);
            Alerts.error("Error", "Ocurrió un error al actualizar el usuario");
        }
    },

    async eliminarUsuario(idUsuario) {
        try {
            const result = await Alerts.confirmation("Eliminar usuario", "¿Está seguro de que desea eliminar este usuario?");
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('accion', 'eliminarUsuario');
            formData.append('idUsuario', idUsuario);
            const response = await ConfiguracionAPI.eliminarUsuarios(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Usuario eliminado correctamente");
                this.listadoUsuarios();
            } else {
                Alerts.error("Error", "No se pudo eliminar el usuario");
            }
        } catch (error) {
            console.error('Error al eliminar usuario:', error);
            Alerts.error("Error", "Ocurrió un error al eliminar el usuario");
        }
    },

    //TODO: Implementación de funciones para perfil-permisos
    async listadoPerfilPermisos() {
        try {
            $("#form-perfilUsuariosPermisos")[0].reset();
            $("#btnAsignarPermisos").prop('disabled', true);
            const perfiles = await ConfiguracionAPI.obtenerPerfilUsuario();
            ConfiguracionView.renderSeleccionPerfilPermisos(perfiles);
            await this.cargarTablaPerfilPermisos();
        } catch (error) {
            console.error('Error al cargar perfil-permisos:', error);
        }
    },

    async cargarTablaPerfilPermisos() {
        try {
            const response = await ConfiguracionAPI.usuariosClient.get({
                accion: 'listadoPerfilPermisos'
            });
            const datos = response && response.data ? response.data : [];
            ConfiguracionView.renderTablaPerfilPermisos(datos);
        } catch (error) {
            console.error('Error al cargar tabla perfil-permisos:', error);
        }
    },

    async cargarPermisosDelPerfil(idPerfil) {
        try {
            console.log('Cargando permisos para perfil:', idPerfil);
            if (!idPerfil) {
                const container = document.getElementById('containerCheckboxPermisos');
                container.innerHTML = '<p class="text-muted">Seleccione un perfil para ver los permisos disponibles</p>';
                container.classList.remove('mt-3');
                $("#btnAsignarPermisos").prop('disabled', true);
                return;
            }

            const permisos = await ConfiguracionAPI.obtenerPermisos();
            
            const response = await ConfiguracionAPI.usuariosClient.get({
                accion: 'obtenerPermisosDelPerfil',
                idPerfil: idPerfil
            });
            
            const permisosAsignados = response && response.data ? response.data : [];
            const idsAsignados = permisosAsignados.map(p => p.id_permiso);
            ConfiguracionView.renderCheckboxPermisos(permisos, idsAsignados);
            $("#btnAsignarPermisos").prop('disabled', false);
        } catch (error) {
            console.error('Error al cargar permisos del perfil:', error);
            Alerts.error("Error", "No se pudieron cargar los permisos");
        }
    },

    async asignarPermisosAlPerfil(e) {
        e.preventDefault();
        const idPerfil = document.getElementById('perfilUsuarioPermiso').value;

        if (!idPerfil) {
            Alerts.warning("Cuidado", "Debe seleccionar un perfil");
            return;
        }

        const checkboxes = document.querySelectorAll('#containerCheckboxPermisos input[type="checkbox"]');
        const permisosSeleccionados = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (permisosSeleccionados.length === 0) {
            Alerts.warning("Cuidado", "Debe seleccionar al menos un permiso");
            return;
        }

        try {
            const result = await Alerts.confirmation(
                "Asignar Permisos",
                `¿Está seguro de que desea asignar ${permisosSeleccionados.length} permiso(s) a este perfil?`
            );
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('accion', 'asignarPermisosAlPerfil');
            formData.append('idPerfil', idPerfil);
            formData.append('permisos', JSON.stringify(permisosSeleccionados));

            const response = await ConfiguracionAPI.usuariosClient.post(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Permisos asignados correctamente");
                this.listadoPerfilPermisos();
                this.cargarPermisosDelPerfil('');
                e.target.reset();
            } else {
                Alerts.error("Error", response?.message || "No se pudieron asignar los permisos");
            }
        } catch (error) {
            console.error('Error al asignar permisos:', error);
            Alerts.error("Error", "Ocurrió un error al asignar los permisos");
        }
    },

    async eliminarPermisoDelPerfil(idPerfil) {
        try {
            const result = await Alerts.confirmation(
                "Eliminar Permisos",
                "¿Está seguro de que desea eliminar todos los permisos de este perfil?"
            );
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('accion', 'eliminarPermisosDelPerfil');
            formData.append('idPerfil', idPerfil);

            const response = await ConfiguracionAPI.usuariosClient.post(formData);
            if (response && response.success) {
                Alerts.success("Éxito", "Permisos eliminados correctamente");
                this.listadoPerfilPermisos();
            } else {
                Alerts.error("Error", "No se pudieron eliminar los permisos");
            }
        } catch (error) {
            console.error('Error al eliminar permisos:', error);
            Alerts.error("Error", "Ocurrió un error al eliminar los permisos");
        }
    }

};







// Exponer el módulo en `window` para que `main.js` lo encuentre
window.ConfiguracionModule = ConfiguracionModule;