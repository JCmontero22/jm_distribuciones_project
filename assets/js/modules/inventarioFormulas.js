const InventarioFormulasAPI = {
    client: new SimpleAPI(CONFIG.AJAX.FORMULAS),

    async listarFormulas() {
        const response = await this.client.get({ accion: "listarFormulas" });
        return response.data || [];
    },

    async listarInsumos() {
        const response = await this.client.get({ accion: "listarInsumos" });
        return response.data || response;
    },

    async listarConcentraciones() {
        const response = await this.client.get({ accion: "listarConcentraciones" });
        return response.data || response;
    },

    async registrarFormula(payload) {
        return await this.client.post(payload);
    }
};

const InventarioFormulasView = {
    poblarSelect(selectId, items, valueKey, textKey) {
        AppUI.fillSelect(selectId, items, valueKey, textKey, true);
    },

    renderizarTabla(formulas) {
        AppUI.initDataTable("#tablaFormulas", {
            data: formulas || [],
            columns: [
                { data: "id_formula" },
                { data: "nombre_formula" },
                {
                    data: "cantidad_esencia",
                    render: (value) => `${value || 0} g`
                },
                {
                    data: "nombre_insumo",
                    render: (value) => value || "-"
                },
                {
                    data: "nombre_concentracion",
                    render: (value) => value || "-"
                }
            ],
            order: [[0, "desc"]]
        });

        $("#cantidad-formulas").text((formulas || []).length);
    }
};

const formulasModule = {
    init() {
        this.bindEvents();
        this.cargarDatos();
    },

    bindEvents() {
        $("#formularioRegistroFormula").on("submit", (e) => this.registrarFormula(e));
    },

    async cargarDatos() {
        try {
            const [insumos, concentraciones, formulas] = await Promise.all([
                InventarioFormulasAPI.listarInsumos(),
                InventarioFormulasAPI.listarConcentraciones(),
                InventarioFormulasAPI.listarFormulas()
            ]);

            InventarioFormulasView.poblarSelect("#insumo", insumos, "id_insumo_formula", "nombre_insumo");
            InventarioFormulasView.poblarSelect("#concentracion", concentraciones, "id_tipo_concentracion", "nombre_concentracion");
            InventarioFormulasView.renderizarTabla(formulas);
        } catch (error) {
            Logger.error("Error al cargar datos de formulas", error);
            Alerts.error("Error", "No se pudieron cargar los datos de formulas");
        }
    },

    async registrarFormula(e) {
        e.preventDefault();

        const payload = {
            accion: "registrarFormula",
            nombreFormula: $("#nombreFormula").val().trim(),
            cantidadEsencia: $("#cantidadEsencia").val(),
            insumo: $("#insumo").val(),
            concentracion: $("#concentracion").val()
        };

        if (!payload.nombreFormula || !payload.cantidadEsencia || !payload.insumo || !payload.concentracion) {
            Alerts.warning("Campos incompletos", "Todos los campos son obligatorios");
            return;
        }

        const confirmation = await Alerts.confirmation(
            "Registrar formula?",
            `${payload.nombreFormula} - ${payload.cantidadEsencia}g`
        );
        if (!confirmation.isConfirmed) return;

        try {
            const response = await InventarioFormulasAPI.registrarFormula(payload);
            if (response.success) {
                AppUI.resetForm("#formularioRegistroFormula");
                Alerts.toasSuccess(response.message || "Formula registrada");
                await this.cargarDatos();
            } else {
                Alerts.error("Error", response.message || "No se pudo registrar la formula");
            }
        } catch (error) {
            Logger.error("Error al registrar formula", error);
            Alerts.error("Error", "No se pudo registrar la formula");
        }
    }
};

window.formulasModule = formulasModule;
