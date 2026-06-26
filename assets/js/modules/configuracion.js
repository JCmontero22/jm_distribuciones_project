const ConfiguracionAPI = {
    catalogoClient: new SimpleAPI(CONFIG.AJAX.CATALOGO),

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
    }
};

const ConfiguracionView = {
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
    }
};

const ConfiguracionModule = {
    init() {
        this.cargarDatosIniciales();
        this.bindEvents();
    },

    bindEvents() {

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
        console.log(formData);
        
        formData.append('accion', 'actualizarSede');
        formData.append('idSede', $("#idSede").val());
        if (!formData.get('nombreSede')) {
            Alerts.warning("Cuidado", "El nombre de la sede es obligatorio");
            return;
        }
        try {
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
    }
};

// Exponer el módulo en `window` para que `main.js` lo encuentre
window.ConfiguracionModule = ConfiguracionModule;