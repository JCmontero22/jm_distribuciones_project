/**
 * Historial de ventas (administración).
 * Capas: HistorialAPI (red) · HistorialView (DOM) · historialModule (lógica).
 */

const HistorialAPI = {
    historialClient: new SimpleAPI(CONFIG.AJAX.HISTORIAL),
    catalogoClient: new SimpleAPI(CONFIG.AJAX.CATALOGO),

    async obtenerSedes() {
        const response = await this.catalogoClient.get({ accion: "listadoSedes" });
        return response.data || [];
    },

    async listar(params) {
        const response = await this.historialClient.get({ accion: "listarVentas", ...params });
        return response.data || { ventas: [] };
    },

    async detalle(idVenta) {
        const response = await this.historialClient.get({ accion: "detalleVenta", idVenta });
        return response.data || {};
    }
};

const HistorialView = {
    escapar(texto) {
        return $("<div>").text(texto ?? "").html();
    },

    money(valor) {
        return `$ ${FormatUtils.separaMiles(Math.round(parseFloat(valor || 0)))}`;
    },

    poblarSedes(sedes) {
        const $select = $("#filtroSedeHistorial");
        $select.empty().append(new Option("Todas las sedes", ""));
        sedes.forEach(s => $select.append(new Option(s.nombre_sede, s.id_sede)));
    },

    renderizarTabla(ventas = []) {
        AppUI.initDataTable("#tablaHistorial", {
            data: ventas,
            columns: [
                {
                    data: "numero_venta",
                    render: (val) => `<span class="fw-bold text-warning">${this.escapar(val || "N/A")}</span>`
                },
                {
                    data: "fecha_venta",
                    render: (val, type) => {
                        if (type === "sort" || type === "type") return val || "";
                        return FormatUtils.formatearFechaTabla(val);
                    }
                },
                {
                    data: "nombre_sede",
                    defaultContent: "Principal"
                },
                {
                    data: "nombre_cliente",
                    render: (val) => this.escapar(val || "Consumidor final")
                },
                {
                    data: "nombre_usuario",
                    defaultContent: "-"
                },
                {
                    data: "total_venta",
                    render: (val) => `<span class="fw-bold">${this.money(val)}</span>`
                },
                {
                    data: "costo_total_venta",
                    render: (val) => this.money(val)
                },
                {
                    data: null,
                    render: (_data, _type, row) => {
                        const anulada = row.estado_venta === "anulada";
                        const ganancia = anulada ? 0 : parseFloat(row.total_venta || 0) - parseFloat(row.costo_total_venta || 0);
                        const cls = ganancia >= 0 ? "text-success" : "text-danger";
                        return `<span class="${cls} fw-bold">${this.money(ganancia)}</span>`;
                    }
                },
                {
                    data: "total_pagado",
                    render: (val) => `<span class="text-info fw-bold">${this.money(val)}</span>`
                },
                {
                    data: "saldo_pendiente",
                    render: (val, _type, row) => {
                        const anulada = row.estado_venta === "anulada";
                        const saldo = anulada ? 0 : parseFloat(val || 0);
                        const cls = saldo > 0 ? "text-warning fw-bold" : "";
                        return `<span class="${cls}">${this.money(saldo)}</span>`;
                    }
                },
                {
                    data: "estado_venta",
                    render: (val) => {
                        const claseEstado = {
                            pagada: "badge-venta badge-venta-pagada",
                            pendiente: "badge-venta badge-venta-pendiente",
                            anulada: "badge-venta badge-venta-anulada"
                        }[val] || "badge-venta";
                        return `<span class="${claseEstado}">${this.escapar(val)}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: (_data, _type, row) => {
                        return `<button class="btn btn-sm btn-primary btn-ver-venta" data-id="${row.id_venta}" data-numero="${this.escapar(row.numero_venta)}" title="Ver detalle"><i class="fa-solid fa-eye"></i></button>`;
                    }
                }
            ],
            order: [[1, "desc"]],
            createdRow(row, data) {
                if (data.estado_venta === "anulada") {
                    $(row).addClass("fila-venta-anulada");
                }
            }
        });
    },

    mostrarResumen(ventas) {
        const activas = ventas.filter(v => v.estado_venta !== "anulada");
        const suma = (campo) => activas.reduce((acc, v) => acc + parseFloat(v[campo] || 0), 0);
        const facturado = suma("total_venta");
        const costo = suma("costo_total_venta");

        $("#resCantidadHistorial").text(activas.length);
        $("#resFacturadoHistorial").text(this.money(facturado));
        $("#resCostoHistorial").text(this.money(costo));
        $("#resGananciaHistorial").text(this.money(facturado - costo));
        $("#resCobradoHistorial").text(this.money(suma("total_pagado")));
        $("#resPorCobrarHistorial").text(this.money(suma("saldo_pendiente")));
    },

    renderizarDetalle(venta, detalles = [], pagos = []) {
        $("#tituloDetalleHistorial").text(`Venta ${venta.numero_venta}`);

        const pagosHtml = pagos.length
            ? pagos.map(p => `<li>${this.escapar(String(p.fecha_pago).slice(0, 16))} · <strong>${this.escapar(p.nombre_metodo_pago || "-")}</strong> · <span class="text-success fw-bold">${this.money(p.monto_pago)}</span> <span class="subtitulo-seccion">(${this.escapar(p.nombre_usuario)})</span></li>`).join("")
            : '<li class="subtitulo-seccion">Sin pagos registrados</li>';

        $("#cuerpoDetalleHistorial").html(`
            <div class="ventas-resumen-box mb-3">
                <p class="mb-1"><strong>Sede:</strong> ${this.escapar(venta.nombre_sede)} · <strong>Cliente:</strong> ${this.escapar(venta.nombre_cliente || "Consumidor final")} · <strong>Vendedor:</strong> ${this.escapar(venta.nombre_usuario)}</p>
                <p class="mb-0"><strong>Fecha:</strong> ${this.escapar(String(venta.fecha_venta).slice(0, 16))} · <strong>Estado:</strong> <span class="text-warning fw-bold text-uppercase">${this.escapar(venta.estado_venta)}</span></p>
            </div>
            <div class="table-responsive">
                <table id="tablaDetalleHistorialItems" class="table table-dark table-striped table-bordered table-sm custom-table text-center align-middle">
                    <thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th><th>Costo</th><th>Ganancia</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <h5 class="mt-4 mb-2">Pagos registrados</h5>
            <ul class="lista-explicacion-balance mb-0">${pagosHtml}</ul>
        `);

        AppUI.initDataTable("#tablaDetalleHistorialItems", {
            data: detalles,
            pageLength: 5,
            lengthMenu: [5, 10, 25],
            columns: [
                {
                    data: null,
                    render: (_data, _type, d) =>
                        `${this.escapar(d.nombre_categoria)} / ${this.escapar(d.nombre_producto)} - ${this.escapar(d.nombre_presentacion)}`
                },
                { data: "cantidad_detalle_venta" },
                {
                    data: "precio_unitario_detalle_venta",
                    render: (val) => this.money(val)
                },
                {
                    data: "subtotal_detalle_venta",
                    render: (val) => `<span class="fw-bold">${this.money(val)}</span>`
                },
                {
                    data: "costo_total_detalle_venta",
                    render: (val) => this.money(val)
                },
                {
                    data: null,
                    render: (_data, _type, d) => {
                        const ganancia = parseFloat(d.subtotal_detalle_venta || 0) - parseFloat(d.costo_total_detalle_venta || 0);
                        return `<span class="fw-bold text-success">${this.money(ganancia)}</span>`;
                    }
                }
            ],
            order: [[0, "asc"]]
        });
    }
};

const historialModule = {
    async init() {
        this.configurarFechasIniciales();
        await this.cargarSedes();

        $("#btnFiltrarHistorial").on("click", () => this.listar());
        $("#filtroTextoHistorial").on("keydown", (e) => { if (e.key === "Enter") this.listar(); });
        $(document).on("click", ".btn-ver-venta", (e) => this.verDetalle($(e.currentTarget).data("id")));

        await this.listar();
    },

    configurarFechasIniciales() {
        const hoy = new Date();
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        $("#filtroDesdeHistorial").val(primerDia.toISOString().slice(0, 10));
        $("#filtroHastaHistorial").val(hoy.toISOString().slice(0, 10));
    },

    async cargarSedes() {
        try {
            HistorialView.poblarSedes(await HistorialAPI.obtenerSedes());
        } catch (error) {
            Logger.error("Error al cargar sedes del historial", error);
        }
    },

    async listar() {
        try {
            const data = await HistorialAPI.listar({
                idSede: $("#filtroSedeHistorial").val(),
                estado: $("#filtroEstadoHistorial").val(),
                desde: $("#filtroDesdeHistorial").val(),
                hasta: $("#filtroHastaHistorial").val(),
                texto: $("#filtroTextoHistorial").val().trim()
            });
            HistorialView.renderizarTabla(data.ventas || []);
            HistorialView.mostrarResumen(data.ventas || []);
        } catch (error) {
            Logger.error("Error al listar historial de ventas", error);
            Alerts.error("Error", "No se pudo cargar el historial de ventas");
        }
    },

    async verDetalle(idVenta) {
        try {
            const data = await HistorialAPI.detalle(idVenta);
            if (!data.venta) {
                Alerts.error("Error", "No se encontró la venta");
                return;
            }
            HistorialView.renderizarDetalle(data.venta, data.detalles || [], data.pagos || []);
            bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDetalleHistorial")).show();
        } catch (error) {
            Logger.error("Error al cargar detalle de venta", error);
            Alerts.error("Error", "No se pudo cargar el detalle de la venta");
        }
    }
};

window.historialModule = historialModule;
