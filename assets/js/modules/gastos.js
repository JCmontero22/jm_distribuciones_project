/**
 * Módulo de gastos por sede.
 * Capas: GastosAPI (red) · GastosView (DOM) · gastosModule (lógica).
 */

const GastosAPI = {
    client: new SimpleAPI(CONFIG.AJAX.GASTOS),
    ventasClient: new SimpleAPI(CONFIG.AJAX.VENTAS),

    async obtenerCatalogos() {
        // catalogosVenta trae sedes y métodos de pago activos
        const response = await this.ventasClient.get({ accion: "catalogosVenta", idSede: 1 });
        return response.data || {};
    },

    async listar(params) {
        const response = await this.client.get({ accion: "listarGastos", ...params });
        return response.data || [];
    },

    async registrar(payload) {
        return await this.client.post({ accion: "registrarGasto", ...payload });
    }
};

const GastosView = {
    escapar(texto) {
        return $("<div>").text(texto ?? "").html();
    },

    poblarSelect(selector, items, valor, texto) {
        const $select = $(selector);
        $select.empty();
        items.forEach(item => $select.append(new Option(item[texto], item[valor])));
    },

    renderizarTabla(gastos = []) {
        AppUI.initDataTable("#tablaGastos", {
            data: gastos,
            columns: [
                {
                    data: "fecha_gasto",
                    render: (val, type) => {
                        if (type === "sort" || type === "type") return val || "";
                        return FormatUtils.formatearFechaTabla(val);
                    }
                },
                {
                    data: "nombre_sede",
                    defaultContent: "N/A"
                },
                {
                    data: "categoria_gasto",
                    render: (val) => `<span class="fw-bold text-warning">${this.escapar(val || "-")}</span>`
                },
                {
                    data: "descripcion_gasto",
                    render: (val) => this.escapar(val || "-")
                },
                {
                    data: "nombre_metodo_pago",
                    render: (val) => this.escapar(val || "-")
                },
                {
                    data: "monto_gasto",
                    render: (val) => `<span class="fw-bold text-danger">$ ${FormatUtils.separaMiles(Math.round(parseFloat(val || 0)))}</span>`
                },
                {
                    data: "nombre_usuario",
                    defaultContent: "-"
                }
            ],
            order: [[0, "desc"]]
        });
    },

    mostrarTotal(gastos) {
        const total = gastos.reduce((acc, g) => acc + parseFloat(g.monto_gasto || 0), 0);
        $("#totalGastosPeriodo").text(`$ ${FormatUtils.separaMiles(total.toFixed(0))}`);
    },

    limpiarFormulario() {
        $("#formRegistroGasto")[0].reset();
        $("#fechaGasto").val(new Date().toISOString().slice(0, 10));
    }
};

const gastosModule = {
    async init() {
        this.configurarFechasIniciales();
        await this.cargarCatalogos();

        $("#formRegistroGasto").on("submit", (e) => this.registrar(e));
        $("#btnFiltrarGastos").on("click", () => this.listar());

        await this.listar();
    },

    configurarFechasIniciales() {
        const hoy = new Date();
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        $("#filtroDesdeGastos").val(primerDia.toISOString().slice(0, 10));
        $("#filtroHastaGastos").val(hoy.toISOString().slice(0, 10));
        $("#fechaGasto").val(hoy.toISOString().slice(0, 10));
    },

    async cargarCatalogos() {
        try {
            const catalogos = await GastosAPI.obtenerCatalogos();
            const sedes = catalogos.sedes || [];
            GastosView.poblarSelect("#filtroSedeGastos", sedes, "id_sede", "nombre_sede");
            GastosView.poblarSelect("#sedeGasto", sedes, "id_sede", "nombre_sede");
            GastosView.poblarSelect("#metodoPagoGasto", catalogos.metodosPago || [], "id_metodo_pago", "nombre_metodo_pago");
        } catch (error) {
            Logger.error("Error al cargar catálogos de gastos", error);
            Alerts.error("Error", "No se pudieron cargar las sedes y métodos de pago");
        }
    },

    async listar() {
        const idSede = $("#filtroSedeGastos").val();
        if (!idSede) {
            GastosView.renderizarTabla([]);
            return;
        }

        try {
            const gastos = await GastosAPI.listar({
                idSede,
                desde: $("#filtroDesdeGastos").val(),
                hasta: $("#filtroHastaGastos").val()
            });
            GastosView.renderizarTabla(gastos);
            GastosView.mostrarTotal(gastos);
        } catch (error) {
            Logger.error("Error al listar gastos", error);
            Alerts.error("Error", "No se pudieron cargar los gastos");
        }
    },

    async registrar(e) {
        e.preventDefault();

        const $form = $("#formRegistroGasto");
        const datos = {
            idSede: $("#sedeGasto").val(),
            fechaGasto: $("#fechaGasto").val(),
            categoriaGasto: $("#categoriaGasto").val().trim(),
            montoGasto: parseFloat($("#montoGasto").val()) || 0,
            idMetodoPago: $("#metodoPagoGasto").val(),
            descripcionGasto: $("#descripcionGasto").val().trim()
        };

        if (!datos.idSede || !datos.fechaGasto || !datos.categoriaGasto || datos.montoGasto <= 0) {
            Alerts.warning("Campos incompletos", "Sede, fecha, categoría y monto (mayor a 0) son obligatorios");
            return;
        }

        const confirmacion = await Alerts.confirmation(
            "¿Registrar gasto?",
            `${datos.categoriaGasto}: $ ${FormatUtils.separaMiles(datos.montoGasto)}`
        );
        if (!confirmacion.isConfirmed) return;

        try {
            const res = await GastosAPI.registrar(datos);
            if (!res.success) {
                Alerts.error("Error", res.message || "No se pudo registrar el gasto");
                return;
            }
            Alerts.success("Gasto registrado", res.message || "");
            GastosView.limpiarFormulario();
            $form.find("#sedeGasto").val(datos.idSede);
            await this.listar();
        } catch (error) {
            Logger.error("Error al registrar gasto", error);
            Alerts.error("Error", "No se pudo registrar el gasto");
        }
    }
};

window.gastosModule = gastosModule;
