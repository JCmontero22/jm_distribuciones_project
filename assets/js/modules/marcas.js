const MarcasAPI = {
    client: new SimpleAPI(CONFIG.AJAX.CATALOGO),

    async listar() {
        const response = await this.client.get({ accion: "listadoMarcas" });
        return response.data || [];
    },

    async registrar(formData) {
        formData.set("accion", "registroMarca");
        return await this.client.post(formData);
    },

    async actualizar(formData) {
        formData.set("accion", "actualizarMarca");
        return await this.client.post(formData);
    },

    async obtenerMarcaID(id) {
        const response = await this.client.get({ accion: "obtenerMarcaID", idMarca: id });
        return response.data || null;
    },

    async eliminar(id) {
        return await this.client.post({ accion: "eliminarMarca", idMarca: id });
    }
};

const MarcasView = {
    renderTabla(marcas) {
        AppUI.initDataTable("#tablaMarcas", {
            data: marcas || [],
            columns: [
                { data: "id_marca" },
                { data: "nombre_marca" },
                {
                    data: "img_marca",
                    render: (img, _type, row) => img
                        ? `<img src="${CONFIG.BASE_URL}assets/img/marcas/${img}" alt="${row.nombre_marca}" width="100">`
                        : "-"
                },
                { 
                    data: null,
                    title: "Acciones",
                    render(data, _type, row) {
                        const id = row.id_marca;
                        return `<button class="btn btn-sm btn-primary btn_editar" data-id="${id}"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-sm btn-danger btn_eliminar" data-id="${id}"><i class="fa-regular fa-trash-can"></i></button>`;

                    }
                }
            ],
            order: [[0, "desc"]]
        });
    }
};

const marcasModule = {
    init() {
        this.bindEvents();
        this.cargarMarcas();
    },

    bindEvents() {
        $("#formularioRegistroMarca").on("submit", (e) => this.registrarMarca(e));
        $("#formularioRegistroMarca").on("reset", () => {
            $("#id_marca").val("");
            $("#btnRegistrarMarca").text("Registrar Marca");
        });
        $(document).on('click', '.btn_editar', (e) => {
             const id = $(e.currentTarget).data("id");
             this.obtenerMarcaDetalle(id);
         });
        $(document).on('click', '.btn_eliminar', (e) => {
             const id = $(e.currentTarget).data("id");
             this.eliminarMarca(id);
         });
    },

    async registrarMarca(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const idMarca = formData.get("id_marca");
        const esEdicion = !!idMarca;
        const nombreMarca = (formData.get("nombreMarca") || "").toString().trim();
        const imagenMarca = formData.get("imagenMarca");

        if (!nombreMarca) {
            Alerts.error("Campos incompletos", "Por favor completa el nombre de la marca");
            return;
        }

        if (!esEdicion && (!imagenMarca || imagenMarca.size === 0)) {
            Alerts.error("Campos incompletos", "Por favor selecciona una imagen para la marca");
            return;
        }

        const confirmation = await Alerts.confirmation(
            esEdicion ? "Actualizar marca?" : "Registrar marca?",
            esEdicion ? "¿Deseas actualizar esta marca?" : "¿Deseas registrar esta nueva marca?"
        );
        if (!confirmation.isConfirmed) return;

        try {
            const response = esEdicion ? await MarcasAPI.actualizar(formData) : await MarcasAPI.registrar(formData);
            if (response.success) {
                AppUI.resetForm("#formularioRegistroMarca");
                Alerts.toasSuccess(response.message || (esEdicion ? "Marca actualizada" : "Marca registrada"));
                await this.cargarMarcas();
            } else {
                Alerts.error("Error", response.message || (esEdicion ? "No se pudo actualizar la marca" : "No se pudo registrar la marca"));
            }
        } catch (error) {
            Logger.error(esEdicion ? "Error al actualizar marca" : "Error al registrar marca", error);
            Alerts.error("Error", esEdicion ? "No se pudo actualizar la marca" : "No se pudo registrar la marca");
        }
    },

    async cargarMarcas() {
        try {
            const marcas = await MarcasAPI.listar();
            MarcasView.renderTabla(marcas);
        } catch (error) {
            Logger.error("Error al cargar marcas", error);
            Alerts.error("Error", "No se pudieron cargar las marcas");
        }
    },

    async obtenerMarcaDetalle(id) {
        try {
            const marca = await MarcasAPI.obtenerMarcaID(id);
            if (marca) {
                $("#id_marca").val(marca[0].id_marca);
                $("#nombreMarca").val(marca[0].nombre_marca);
                $("#btnRegistrarMarca").text("Actualizar Marca");
                document.getElementById("formularioRegistroMarca").scrollIntoView({ behavior: "smooth" });
            } else {
                Alerts.error("Error", "No se encontró la marca");
            }
        } catch (error) {
            Logger.error("Error al obtener detalles de la marca", error);
            Alerts.error("Error", "No se pudieron obtener los detalles de la marca");
        }
    },

    async eliminarMarca(id) {
        const confirmation = await Alerts.confirmation(
            "Eliminar marca?",
            "¿Estás seguro de que deseas eliminar esta marca?"
        );
        if (!confirmation.isConfirmed) return;

        try {
            const response = await MarcasAPI.eliminar(id);
            if (response.success) {
                Alerts.toasSuccess(response.message || "Marca eliminada");
                await this.cargarMarcas();
            } else {
                Alerts.error("Error", response.message || "No se pudo eliminar la marca");
            }
        } catch (error) {
            Logger.error("Error al eliminar marca", error);
            Alerts.error("Error", "No se pudo eliminar la marca");
        }
    }
};

window.marcasModule = marcasModule;