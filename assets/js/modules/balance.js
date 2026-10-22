/**
 * Balance mensual por sede.
 * Capas: BalanceAPI (red) · BalanceView (DOM) · balanceModule (lógica).
 */

const BalanceAPI = {
    balanceClient: new SimpleAPI(CONFIG.AJAX.BALANCE),
    catalogoClient: new SimpleAPI(CONFIG.AJAX.CATALOGO),

    async obtenerSedes() {
        const response = await this.catalogoClient.get({ accion: "listadoSedes" });
        return response.data || [];
    },

    async obtenerBalance(params) {
        return await this.balanceClient.get({ accion: "balanceMensual", ...params });
    }
};

const BalanceView = {
    escapar(texto) {
        return $("<div>").text(texto ?? "").html();
    },

    money(valor) {
        return `$ ${FormatUtils.separaMiles(Math.round(parseFloat(valor || 0)))}`;
    },

    poblarSedes(sedes) {
        const $select = $("#filtroSedeBalance");
        $select.empty().append(new Option("Todas las sedes", ""));
        sedes.forEach(s => $select.append(new Option(s.nombre_sede, s.id_sede)));
    },

    mostrarTotales(t) {
        const queda = parseFloat(t.queda || 0);
        $("#balFacturado").text(this.money(t.facturado));
        $("#balGananciaBruta").text(this.money(t.ganancia_bruta));
        $("#balGastos").text(this.money(t.gastos));
        $("#balGananciaNeta").text(this.money(t.ganancia_neta));
        $("#balParaReinvertir").text(this.money(t.para_reinvertir));
        $("#balReinvertido").text(this.money(t.reinvertido));
        $("#balRecaudado").text(this.money(t.recaudado));
        $("#balPorCobrar").text(this.money(t.por_cobrar));
        $("#balQueda")
            .text(this.money(queda))
            .removeClass("text-success text-danger")
            .addClass(queda >= 0 ? "text-success" : "text-danger");
    },

    renderizarSedes(sedes = [], totales = {}) {
        const $foot = $("#tablaBalanceFoot");
        if (sedes.length) {
            const netaTotal = parseFloat(totales.ganancia_neta || 0);
            const quedaTotal = parseFloat(totales.queda || 0);
            $foot.html(`
                <tr class="fila-total-balance">
                    <td>Total</td>
                    <td>${totales.cantidad_ventas || 0}</td>
                    <td>${this.money(totales.facturado)}</td>
                    <td>${this.money(totales.costo_ventas)}</td>
                    <td>${this.money(totales.ganancia_bruta)}</td>
                    <td>${this.money(totales.gastos)}</td>
                    <td class="${netaTotal >= 0 ? "text-success" : "text-danger"}">${this.money(netaTotal)}</td>
                    <td>${this.money(totales.reinvertido)}</td>
                    <td class="${quedaTotal >= 0 ? "text-success" : "text-danger"}">${this.money(quedaTotal)}</td>
                </tr>
            `);
        } else {
            $foot.empty();
        }

        AppUI.initDataTable("#tablaBalance", {
            data: sedes,
            columns: [
                {
                    data: "nombre_sede",
                    render: (val) => `<span class="fw-bold text-warning">${this.escapar(val)}</span>`
                },
                { data: "cantidad_ventas" },
                {
                    data: "facturado",
                    render: (val) => this.money(val)
                },
                {
                    data: "costo_ventas",
                    render: (val) => this.money(val)
                },
                {
                    data: "ganancia_bruta",
                    render: (val) => this.money(val)
                },
                {
                    data: "gastos",
                    render: (val) => this.money(val)
                },
                {
                    data: "ganancia_neta",
                    render: (val) => {
                        const neta = parseFloat(val || 0);
                        const cls = neta >= 0 ? "text-success" : "text-danger";
                        return `<span class="fw-bold ${cls}">${this.money(neta)}</span>`;
                    }
                },
                {
                    data: "reinvertido",
                    render: (val) => this.money(val)
                },
                {
                    data: "queda",
                    render: (val) => {
                        const queda = parseFloat(val || 0);
                        const cls = queda >= 0 ? "text-success" : "text-danger";
                        return `<span class="fw-bold ${cls}">${this.money(queda)}</span>`;
                    }
                }
            ],
            order: [[0, "asc"]]
        });
    },

    renderizarGastosCategoria(categorias = []) {
        AppUI.initDataTable("#tablaGastosCategoria", {
            data: categorias,
            columns: [
                {
                    data: "categoria_gasto",
                    render: (val) => `<span class="fw-bold">${this.escapar(val)}</span>`
                },
                {
                    data: "total",
                    render: (val) => `<span class="fw-bold text-danger">${this.money(val)}</span>`
                }
            ],
            order: [[1, "desc"]]
        });
    }
};

const balanceModule = {
    async init() {
        $("#filtroMesBalance").val(new Date().toISOString().slice(0, 7));
        await this.cargarSedes();
        $("#btnActualizarBalance").on("click", () => this.cargar());
        $("#filtroSedeBalance, #filtroMesBalance").on("change", () => this.cargar());
        await this.cargar();
    },

    async cargarSedes() {
        try {
            BalanceView.poblarSedes(await BalanceAPI.obtenerSedes());
        } catch (error) {
            Logger.error("Error al cargar sedes del balance", error);
        }
    },

    async cargar() {
        const mes = $("#filtroMesBalance").val();
        if (!mes) {
            Alerts.warning("Mes requerido", "Selecciona el mes del balance");
            return;
        }
        try {
            const res = await BalanceAPI.obtenerBalance({ mes, idSede: $("#filtroSedeBalance").val() });
            if (!res.success) {
                Alerts.error("Error", res.message || "No se pudo calcular el balance");
                return;
            }
            const data = res.data;
            BalanceView.mostrarTotales(data.totales);
            BalanceView.renderizarSedes(data.sedes, data.totales);
            BalanceView.renderizarGastosCategoria(data.gastos_por_categoria || []);
        } catch (error) {
            Logger.error("Error al calcular balance mensual", error);
            Alerts.error("Error", "No se pudo calcular el balance");
        }
    }
};

window.balanceModule = balanceModule;
