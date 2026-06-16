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
    },

    async obtenerDetalleFormula(id) {
        const response = await this.client.get({ id_formula: id, accion: "obtenerFormula" });
        return response.data || null;
    },

    async editarFormula(formData) {
        return await this.client.post(formData);
    },

    async eliminarFormula(id) {
        return await this.client.post({ id_formula: id, accion: "eliminarFormula" });
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
                },
                {
                    data: null,
                    render: (data) => `
                        <button class="btn btn-sm btn-primary editar-formula" data-id="${data.id_formula}"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button class="btn btn-sm btn-danger eliminar-formula" data-id="${data.id_formula}"><i class="fa-regular fa-trash-can"></i></button>
                    `
                }
            ],
            order: [[0, "desc"]]
        });

        $("#cantidad-formulas").text((formulas || []).length);
    },

    renderizarFormulaDetalle(formula) {
        if (!formula) return;
        
        $("#nombreFormula").val(formula[0].nombre_formula || "-");
        $("#cantidadEsencia").val(formula[0].cantidad_esencia || 0);
        $("#insumo").val(formula[0].id_insumo_formula).trigger("change");
        $("#concentracion").val(formula[0].id_tipo_concentracion).trigger("change");
        $("#btnRegistro").text("Actualizar Fórmula").data("id", formula[0].id_formula);
    }   
};

const formulasModule = {
    init() {
        this.bindEvents();
        this.cargarDatos();
        $("#btnRegistro").text("Registrar Fórmula").data("id", null);
    },

    bindEvents() {
        $("#formularioRegistroFormula").on("submit", (e) => {
            const idFormula = $("#btnRegistro").data("id");
            if (idFormula) {
                this.editarFormula(e, idFormula);
            } else {    
                this.registrarFormula(e);
            }
        });

        $("#tablaFormulas").on("click", ".editar-formula", (e) => {
            const id = $(e.currentTarget).data("id");
            this.obtenerDetalleFormula(id);
        });

        $("#tablaFormulas").on("click", ".eliminar-formula", async (e) => {
            const id = $(e.currentTarget).data("id");
            this.eliminarFormula(id);
        });
                
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
            
            Alerts.error("Error", "No se pudo registrar la formula");
        }
    },

    async obtenerDetalleFormula(id) {
        dataFormula = await InventarioFormulasAPI.obtenerDetalleFormula(id);
        if (dataFormula) {
            InventarioFormulasView.renderizarFormulaDetalle(dataFormula);
        } else {
            Alerts.error("Error", "No se pudo obtener el detalle de la fórmula");
        }
    },

    async editarFormula(e, id) {
        e.preventDefault();

        const formData = new FormData(e.target);
        formData.set("id_formula", id);
        formData.set("accion", "editarFormula");

        if (!formData.get("nombreFormula") || !formData.get("cantidadEsencia") || !formData.get("insumo") || !formData.get("concentracion")) {
            Alerts.warning("Campos incompletos", "Todos los campos son obligatorios");
            return;
        }

        const confirmation = await Alerts.confirmation(
            "Actualizar fórmula?",
            `${formData.get("nombreFormula")} - ${formData.get("cantidadEsencia")}g`
        );
        if (!confirmation.isConfirmed) return;

        try {
            const response = await InventarioFormulasAPI.editarFormula(formData);
            if (response.success) {
                AppUI.resetForm("#formularioRegistroFormula");
                $("#btnRegistro").text("Registrar Fórmula").data("id", null);
                Alerts.toasSuccess(response.message || "Fórmula actualizada");
                await this.cargarDatos();
            } else {
                Alerts.error("Error", response.message || "No se pudo actualizar la fórmula");
            }
        } catch (error) {
            Alerts.error("Error", "No se pudo actualizar la fórmula");
        }
    },

    async eliminarFormula(id) {
        const confirmation = await Alerts.confirmation(
            "Eliminar fórmula?",
            "¿Estás seguro de que deseas eliminar esta fórmula?"
        );
        if (!confirmation.isConfirmed) return;

        try {
            const response = await InventarioFormulasAPI.eliminarFormula(id);
            if (response.success) {
                Alerts.toasSuccess(response.message || "Fórmula eliminada");
                await this.cargarDatos();
            } else {
                Alerts.error("Error", response.message || "No se pudo eliminar la fórmula");
            }
        } catch (error) {
            Alerts.error("Error", "No se pudo eliminar la fórmula");
        }
    }
};

window.formulasModule = formulasModule;
