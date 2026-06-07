const ProveedoresAPI = {
    client: new SimpleAPI(CONFIG.AJAX.PROVEEDORES),

    async listar() {
        const response = await this.client.get({ accion: "listadoProveedoresSelect" });
        return response.data || [];
    },

    async registrar(formData) {
        formData.append("accion", "registrarProveedor");
        return await this.client.post(formData);
    }
};

const ProveedoresView = {
    renderTabla(proveedores) {
        AppUI.initDataTable("#tablaProveedores", {
            data: proveedores || [],
            columns: [
                { data: "id_proveedor" },
                { data: "nombre_proveedor" },
                { data: "contacto_proveedor" },
                { data: "telefono_proveedor" }
            ],
            order: [[0, "desc"]]
        });
    }
};

const proveedoresModule = {
    modalProveedor: null,

    init() {
        this.modalProveedor = AppUI.createModal("modalRegistroProveedor");
        this.bindEvents();
        this.cargarProveedores();
    },

    bindEvents() {
        $("#registroDeProveedor").on("submit", (e) => this.registrarProveedor(e));
        $("#modalRegistroProveedor").on("hidden.bs.modal", () => AppUI.resetForm("#registroDeProveedor"));
    },

    async cargarProveedores() {
        try {
            const proveedores = await ProveedoresAPI.listar();
            ProveedoresView.renderTabla(proveedores);
        } catch (error) {
            Logger.error("Error al cargar proveedores", error);
            Alerts.error("Error", "No se pudieron cargar los proveedores");
        }
    },

    async registrarProveedor(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        if (!formData.get("nombre") || !formData.get("contacto") || !formData.get("telefono")) {
            Alerts.warning("Campos incompletos", "Por favor completa todos los campos");
            return;
        }

        const confirmation = await Alerts.confirmation(
            "Registrar este proveedor?",
            "¿Estas seguro de que deseas registrar este proveedor?"
        );
        if (!confirmation.isConfirmed) return;

        try {
            const response = await ProveedoresAPI.registrar(formData);

            if (response.success) {
                AppUI.resetForm("#registroDeProveedor");
                if (this.modalProveedor) this.modalProveedor.hide();
                await this.cargarProveedores();
                Alerts.toasSuccess(response.message || "Proveedor registrado");
            } else {
                Alerts.error("Error", response.message || "No se pudo registrar");
            }
        } catch (error) {
            Logger.error("Error al registrar proveedor", error);
            Alerts.error("Error", "Error al registrar el proveedor");
        }
    }
};

window.proveedoresModule = proveedoresModule;
