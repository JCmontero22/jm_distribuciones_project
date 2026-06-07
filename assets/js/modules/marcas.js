const MarcasAPI = {
    client: new SimpleAPI(CONFIG.AJAX.CATALOGO),

    async listar() {
        const response = await this.client.get({ accion: "listadoMarcas" });
        return response.data || [];
    },

    async registrar(formData) {
        formData.set("accion", "registroMarca");
        return await this.client.post(formData);
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
    },

    async registrarMarca(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const nombreMarca = (formData.get("nombreMarca") || "").toString().trim();
        const imagenMarca = formData.get("imagenMarca");

        if (!nombreMarca || !(imagenMarca instanceof File) || imagenMarca.size === 0) {
            Alerts.error("Campos incompletos", "Por favor completa todos los campos");
            return;
        }

        const confirmation = await Alerts.confirmation(
            "Registrar marca?",
            "Se registrara una nueva marca"
        );
        if (!confirmation.isConfirmed) return;

        try {
            const response = await MarcasAPI.registrar(formData);
            if (response.success) {
                AppUI.resetForm("#formularioRegistroMarca");
                Alerts.toasSuccess(response.message || "Marca registrada");
                await this.cargarMarcas();
            } else {
                Alerts.error("Error", response.message || "No se pudo registrar la marca");
            }
        } catch (error) {
            Logger.error("Error al registrar marca", error);
            Alerts.error("Error", "No se pudo registrar la marca");
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
    }
};

window.marcasModule = marcasModule;