/**
 * CAPA API — Solo responsable de comunicarse con el servidor
 */
const VentasAPI = {
    ventasClient: new SimpleAPI(CONFIG.AJAX.VENTAS),
    catalogoClient: new SimpleAPI(CONFIG.AJAX.CATALOGO),

    async obtenerSedes() {
        const response = await this.catalogoClient.get({ accion: "listadoSedes" });
        return response.data || [];
    },

    async obtenerVentas(filtros = {}) {
        const response = await this.ventasClient.get({
            accion: "listarVentas",
            ...filtros
        });
        return response.data || { ventas: [], resumen: {} };
    },

    async obtenerCatalogosVenta(idSede = 0) {
        const response = await this.ventasClient.get({
            accion: "catalogosVenta",
            idSede
        });
        return response.data || {};
    },

    async obtenerProductosPorSede(idSede) {
        const response = await this.ventasClient.get({
            accion: "productosVentaPorSede",
            idSede
        });
        return response.data || {};
    },

    async obtenerDetalleVenta(idVenta) {
        const response = await this.ventasClient.get({
            accion: "detalleVenta",
            idVenta
        });
        return response.data || {};
    },

    async registrarVenta(formData) {
        return await this.ventasClient.post(formData);
    },

    async registrarAbono(formData) {
        return await this.ventasClient.post(formData);
    },

    async anularVenta(idVenta) {
        return await this.ventasClient.post({
            accion: "anularVenta",
            idVenta
        });
    },

    async registrarCliente(formData) {
        return await this.ventasClient.post(formData);
    }
};

/**
 * CAPA VIEW — Solo responsable de renderizar HTML y manipular el DOM
 */
const VentasView = {
    renderizarKPIs(resumen = {}) {
        const ventasHoy = parseFloat(resumen.ventas_hoy || 0);
        const cantidadHoy = parseInt(resumen.cantidad_hoy || 0, 10);
        const totalRecaudado = parseFloat(resumen.total_recaudado || 0);
        const totalFacturado = parseFloat(resumen.total_facturado || 0);
        const porCobrar = parseFloat(resumen.por_cobrar || 0);
        const pendientesCount = parseInt(resumen.ventas_pendientes || 0, 10);
        const gananciaTotal = parseFloat(resumen.ganancia_estimada || 0);
        const costoTotal = parseFloat(resumen.costo_total || 0);

        $("#kpiVentasHoy").text(`$ ${FormatUtils.separaMiles(Math.round(ventasHoy))}`);
        $("#kpiCantidadHoy").text(`${cantidadHoy} venta(s) registrada(s) hoy`);

        $("#kpiTotalRecaudado").text(`$ ${FormatUtils.separaMiles(Math.round(totalRecaudado))}`);
        $("#kpiTotalFacturado").text(`Facturado: $ ${FormatUtils.separaMiles(Math.round(totalFacturado))}`);

        $("#kpiPorCobrar").text(`$ ${FormatUtils.separaMiles(Math.round(porCobrar))}`);
        $("#kpiVentasPendientes").text(`${pendientesCount} venta(s) pendiente(s)`);

        $("#kpiGananciaTotal").text(`$ ${FormatUtils.separaMiles(Math.round(gananciaTotal))}`);
        $("#kpiCostoTotal").text(`Costo: $ ${FormatUtils.separaMiles(Math.round(costoTotal))}`);
    },

    obtenerBadgeEstado(estado) {
        const est = (estado || "pendiente").toLowerCase();
        if (est === "pagada") {
            return `<span class="badge-venta badge-venta-pagada"><i class="fa-solid fa-circle-check me-1"></i>Pagada</span>`;
        }
        if (est === "anulada") {
            return `<span class="badge-venta badge-venta-anulada"><i class="fa-solid fa-ban me-1"></i>Anulada</span>`;
        }
        return `<span class="badge-venta badge-venta-pendiente"><i class="fa-solid fa-clock me-1"></i>Pendiente</span>`;
    },

    renderizarTablaVentas(ventas = []) {
        AppUI.initDataTable("#tablaVentas", {
            data: ventas,
            columns: [
                { data: "id_venta" },
                {
                    data: "numero_venta",
                    render: (val) => `<span class="fw-bold text-warning">${val || "N/A"}</span>`
                },
                { data: "nombre_sede", defaultContent: "Principal" },
                {
                    data: "nombre_cliente",
                    render: (val) => val || '<span class="text-muted">Consumidor Final</span>'
                },
                {
                    data: "total_venta",
                    render: (val) => `$ ${FormatUtils.separaMiles(Math.round(parseFloat(val || 0)))}`
                },
                {
                    data: "total_pagado",
                    render: (val) => `<span class="text-success">$ ${FormatUtils.separaMiles(Math.round(parseFloat(val || 0)))}</span>`
                },
                {
                    data: "saldo_pendiente",
                    render: (val, type, row) => {
                        if (row.estado_venta === "anulada") return `<span class="text-muted">$ 0</span>`;
                        const saldo = Math.max(0, Math.round(parseFloat(val || 0)));
                        const cls = saldo > 0 ? "text-danger" : "text-success";
                        return `<span class="${cls}">$ ${FormatUtils.separaMiles(saldo)}</span>`;
                    }
                },
                {
                    data: "estado_venta",
                    render: (val) => VentasView.obtenerBadgeEstado(val)
                },
                {
                    data: "fecha_venta",
                    render: (val, type) => {
                        if (type === "sort" || type === "type") return val || "";
                        return FormatUtils.formatearFechaTabla(val);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render(data, type, row) {
                        const id = row.id_venta;
                        const esPendiente = row.estado_venta === "pendiente";
                        const esAnulada = row.estado_venta === "anulada";

                        let botones = `
                            <button class="btn btn-sm btn-primary btn-ver-venta" data-id="${id}" title="Ver detalle">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        `;

                        if (esPendiente) {
                            botones += `
                                <button class="btn btn-sm btn-success btn-abonar-venta" data-id="${id}" title="Registrar abono">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                </button>
                            `;
                        }

                        if (!esAnulada) {
                            botones += `
                                <button class="btn btn-sm btn-danger btn-anular-venta" data-id="${id}" title="Anular venta">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            `;
                        }

                        return `<div class="acciones-tabla">${botones}</div>`;
                    }
                }
            ],
            order: [[0, "desc"]]
        });
    },

    poblarSedes(sedes = []) {
        AppUI.fillSelect("#filtroSedeVentas", sedes, "id_sede", "nombre_sede");
        AppUI.fillSelect("#selectSedeVenta", sedes, "id_sede", "nombre_sede");
    },

    poblarCategorias(categorias = []) {
        AppUI.fillSelect("#selectCategoriaVenta", categorias, "id_categoria", "nombre_categoria");
        if (categorias.length > 0) {
            $("#selectCategoriaVenta").append(new Option("Lociones", "0"));
        }
    },

    /** Sin sede no hay categorias ni productos: el usuario no puede vender nada hasta elegir sede. */
    limpiarCatalogoSede() {
        this.poblarCategorias([]);
        this.poblarProductosVenta([]);
        $("#numeroVenta").val("");
        this.mostrarIndicadorProducto(null);
    },

    aplicarCatalogoSede(data) {
        this.poblarCategorias(data.categorias || []);
        this.poblarProductosVenta(data.productos || []);
        if (data.siguienteNumero) {
            $("#numeroVenta").val(data.siguienteNumero);
        }
    },

    poblarClientes(clientes = [], idSeleccionar = null) {
        const $select = $("#selectClienteVenta");
        $select.html('<option value="">Consumidor Final (Opcional)</option>');
        clientes.forEach((c) => {
            const doc = c.identificacion_cliente ? ` (${c.identificacion_cliente})` : "";
            $select.append(new Option(`${c.nombre_cliente}${doc}`, c.id_cliente));
        });
        if (idSeleccionar) {
            $select.val(idSeleccionar);
        }
    },

    poblarMetodosPago(metodos = []) {
        AppUI.fillSelect("#selectMetodoPagoVenta", metodos, "id_metodo_pago", "nombre_metodo_pago");
        AppUI.fillSelect("#selectMetodoPagoAbono", metodos, "id_metodo_pago", "nombre_metodo_pago");
        if (metodos.length > 0) {
            $("#selectMetodoPagoVenta").val(metodos[0].id_metodo_pago);
            $("#selectMetodoPagoAbono").val(metodos[0].id_metodo_pago);
        }
    },

    poblarProductosVenta(productos = [], categoriaSeleccionada = "") {
        const $select = $("#selectProductoVenta");
        let html = '<option value="">Seleccione producto / presentación</option>';

        const filtrados = categoriaSeleccionada
            ? productos.filter((p) => String(p.id_categoria) === String(categoriaSeleccionada))
            : productos;


        filtrados.forEach((p) => {
            const stock = parseFloat(p.stock_disponible || 0);
            const unidad = p.unidad_venta || "und";
            const precioFinal = Math.round(parseFloat(p.precio_venta_final || p.precio_venta_base || 0));
            const etiqueta = p.venta_bloqueada
                ? ` [${p.motivo_bloqueo}]`
                : stock <= 0 ? " [SIN STOCK]" : ` [Stock: ${stock} ${unidad}]`;
            html += `<option value="${p.clave}">${p.nombre_categoria} / ${p.nombre_producto} - ${p.nombre_presentacion} ($ ${FormatUtils.separaMiles(precioFinal)})${etiqueta}</option>`;
        });

        $select.html(html);
    },

    mostrarIndicadorProducto(producto) {
        const $container = $("#infoStockProductoSeleccionado");
        const $badgeStock = $("#badgeStockDisponible");
        const $badgeDesc = $("#badgeDescuentoProducto");

        if (!producto) {
            $container.hide();
            return;
        }

        const stock = parseFloat(producto.stock_disponible || 0);
        const unidad = producto.unidad_venta || "unidades";
        $container.show();

        if (producto.venta_bloqueada) {
            $badgeStock
                .addClass("stock-pill-danger")
                .html(`<i class="fa-solid fa-triangle-exclamation me-1"></i>${producto.motivo_bloqueo}`);
        } else if (stock > 0) {
            $badgeStock
                .removeClass("stock-pill-danger")
                .html(`<i class="fa-solid fa-boxes-stacked me-1"></i>Disponible: ${stock} ${unidad}`);
        } else {
            $badgeStock
                .addClass("stock-pill-danger")
                .html(`<i class="fa-solid fa-triangle-exclamation me-1"></i>${producto.motivo_sin_stock || "Sin stock disponible en esta sede"}`);
        }

        const desc = parseFloat(producto.porcentaje_descuento || 0);
        if (desc > 0) {
            $badgeDesc.show().html(`<i class="fa-solid fa-tag me-1"></i>Descuento activo: -${desc}%`);
        } else {
            $badgeDesc.hide();
        }
    },

    renderizarItemsCarrito(items = []) {
        const $container = $("#detallesVentaContainer");
        const $tbody = $("#tablaItemsVenta");

        if (!items.length) {
            $container.hide();
            $tbody.empty();
            return;
        }

        $container.show();
        const rows = items
            .map(
                (item) => `
            <tr>
                <td class="text-start">${item.nombreCompleto}</td>
                <td>${item.cantidad}</td>
                <td>$ ${FormatUtils.separaMiles(Math.round(item.precioUnitario))}</td>
                <td class="fw-bold text-warning">$ ${FormatUtils.separaMiles(Math.round(item.subTotal))}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-quitar-item-venta" data-id="${item.id}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `
            )
            .join("");

        $tbody.html(rows);
    },

    renderizarDetalleModal(venta, detalles = [], pagos = []) {
        $("#detalleNumeroVentaTitulo").text(`#${venta.numero_venta || venta.id_venta}`);
        $("#detalleVentaSede").text(venta.nombre_sede || "Principal");
        $("#detalleVentaCliente").text(venta.nombre_cliente || "Consumidor Final");
        $("#detalleVentaUsuario").text(venta.nombre_usuario || "N/A");
        $("#detalleVentaFecha").text(venta.fecha_venta || "N/A");
        $("#detalleVentaEstado").html(this.obtenerBadgeEstado(venta.estado_venta));

        const subtotal = Math.round(parseFloat(venta.subtotal_venta || 0));
        const descuento = Math.round(parseFloat(venta.descuento_venta || 0));
        const total = Math.round(parseFloat(venta.total_venta || 0));
        const pagado = Math.round(parseFloat(venta.total_pagado || 0));
        const saldo = venta.estado_venta === "anulada" ? 0 : Math.max(0, total - pagado);

        $("#detalleVentaSubtotal").text(`$ ${FormatUtils.separaMiles(subtotal)}`);
        $("#detalleVentaDescuento").text(`$ ${FormatUtils.separaMiles(descuento)}`);
        $("#detalleVentaTotal").text(`$ ${FormatUtils.separaMiles(total)}`);
        $("#detalleVentaPagado").text(`$ ${FormatUtils.separaMiles(pagado)}`);
        $("#detalleVentaSaldo").text(`$ ${FormatUtils.separaMiles(saldo)}`);

        if (venta.observacion_venta) {
            $("#contenedorDetalleObservacion").show();
            $("#detalleVentaObservacion").text(venta.observacion_venta);
        } else {
            $("#contenedorDetalleObservacion").hide();
        }

        const datosItems = (detalles || []).map((d, idx) => ({
            ...d,
            indice: idx + 1
        }));

        AppUI.initDataTable("#tablaDetalleItemsVentaTable", {
            data: datosItems,
            pageLength: 5,
            lengthMenu: [5, 10, 25],
            columns: [
                { data: "indice" },
                {
                    data: null,
                    render: (_data, _type, row) =>
                        `${row.nombre_categoria || ""} / ${row.nombre_producto || ""} - ${row.nombre_presentacion || ""}`
                },
                { data: "cantidad_detalle_venta" },
                {
                    data: "precio_unitario_detalle_venta",
                    render: (val) => `$ ${FormatUtils.separaMiles(Math.round(parseFloat(val || 0)))}`
                },
                {
                    data: "subtotal_detalle_venta",
                    render: (val) => `<span class="fw-bold text-warning">$ ${FormatUtils.separaMiles(Math.round(parseFloat(val || 0)))}</span>`
                }
            ],
            order: [[0, "asc"]]
        });

        const datosPagos = (pagos || []).map((p, idx) => ({
            ...p,
            indice: idx + 1
        }));

        AppUI.initDataTable("#tablaDetallePagosVentaTable", {
            data: datosPagos,
            pageLength: 5,
            lengthMenu: [5, 10, 25],
            columns: [
                { data: "indice" },
                { data: "nombre_metodo_pago", defaultContent: "N/A" },
                {
                    data: "monto_pago",
                    render: (val) => `<span class="text-success fw-bold">$ ${FormatUtils.separaMiles(Math.round(parseFloat(val || 0)))}</span>`
                },
                { data: "nombre_usuario", defaultContent: "N/A" },
                {
                    data: "fecha_pago",
                    render: (val, type) => {
                        if (type === "sort" || type === "type") return val || "";
                        return FormatUtils.formatearFechaTabla(val);
                    }
                }
            ],
            order: [[0, "asc"]]
        });
    }
};

/**
 * MÓDULO VENTAS — Coordina API y View, maneja estado y lógica de negocio en cliente
 */
const ventasModule = {
    productosDisponibles: [],
    itemsCarrito: [],
    ventaActualAbono: null,

    init() {
        this.modalVenta = AppUI.createModal("modalRegistroVenta");
        this.modalDetalle = AppUI.createModal("modalDetalleVenta");
        this.modalAbono = AppUI.createModal("modalAbonoVenta");
        this.modalCliente = AppUI.createModal("modalRegistroCliente");

        this.bindEvents();
        this.cargarCatalogosIniciales();
        this.cargarVentas();
    },

    bindEvents() {
        // Filtros listado principal
        $("#filtroSedeVentas, #filtroEstadoVentas").on("change", () => this.cargarVentas());
        $("#btnRefrescarVentas").on("click", () => this.cargarVentas());

        // Apertura modal Nueva Venta
        $("#btnModalVenta").on("click", () => {
            this.prepararNuevaVenta();
        });

        // Nuevo cliente rápido desde modal de venta
        $("#btnNuevoClienteRapido").on("click", () => {
            this.modalCliente.show();
        });

        // Cambio de sede en Nueva Venta recarga productos y consecutivo de esa sede
        $("#selectSedeVenta").on("change", (e) => {
            const idSede = $(e.target).val();
            if (idSede) {
                this.cambiarSedeEnModal(idSede);
            } else {
                this.productosDisponibles = [];
                VentasView.limpiarCatalogoSede();
            }
        });

        // Filtro por categoría en Nueva Venta
        $("#selectCategoriaVenta").on("change", (e) => {
            const idCategoria = $(e.target).val();
            VentasView.poblarProductosVenta(this.productosDisponibles, idCategoria);
            VentasView.mostrarIndicadorProducto(null);
            $("#precioUnitarioVenta").val("");
        });

        // Selección de producto llena precio sugerido y muestra stock
        $("#selectProductoVenta").on("change", (e) => {
            const idPresentacion = $(e.target).val();
            this.seleccionarProducto(idPresentacion);
        });

        // Formateo de miles
        $(document).on("input", "#precioUnitarioVenta, #inputDescuentoVenta, #montoPagoInicial, #montoAbonoVenta", (e) => {
            FormatUtils.formatearMiles(e);
            this.recalcularTotalesCarrito();
        });

        // Cambio de modalidad (Contado / Crédito)
        $("#selectModalidadVenta").on("change", () => {
            this.actualizarModalidadPago();
        });

        // Agregar ítem al carrito
        $("#btnAgregarItemVenta").on("click", () => this.agregarItemAlCarrito());

        // Quitar ítem del carrito
        $(document).on("click", ".btn-quitar-item-venta", (e) => {
            const id = $(e.currentTarget).data("id");
            this.quitarItemDelCarrito(id);
        });

        // Submit Nueva Venta
        $("#formRegistroVenta").on("submit", (e) => this.guardarVenta(e));

        // Submit Nuevo Cliente
        $("#formRegistroCliente").on("submit", (e) => this.guardarCliente(e));

        // Submit Abono
        $("#formAbonoVenta").on("submit", (e) => this.guardarAbono(e));

        // Acciones tabla de ventas
        $(document).on("click", ".btn-ver-venta", (e) => {
            const id = $(e.currentTarget).data("id");
            this.verDetalleVenta(id);
        });

        $(document).on("click", ".btn-abonar-venta", (e) => {
            const id = $(e.currentTarget).data("id");
            this.abrirModalAbono(id);
        });

        $(document).on("click", ".btn-anular-venta", (e) => {
            const id = $(e.currentTarget).data("id");
            this.anularVenta(id);
        });
    },

    async cargarCatalogosIniciales() {
        try {
            const data = await VentasAPI.obtenerCatalogosVenta(0);
            VentasView.poblarSedes(data.sedes || []);
            VentasView.poblarClientes(data.clientes || []);
            VentasView.poblarMetodosPago(data.metodosPago || []);

            if (data.sedeFija) {
                // Vendedor: su sede es fija
                // La sede viene de la sesion: el usuario no la elige, se oculta el selector
                this.sedeFija = data.sedeFija;
                $("#selectSedeVenta").val(data.sedeFija);
                $("#wrapperSedeVenta, #wrapperFiltroSedeVentas").hide();
                $("#wrapperFiltroEstadoVentas").removeClass("col-md-4 col-md-5").addClass("col-md-9");
                $("#wrapperBtnRefrescarVentas").removeClass("col-md-4 col-md-2").addClass("col-md-3");
                $("#wrapperNumeroVenta").removeClass("col-md-3").addClass("col-md-4");
                $("#wrapperClienteVenta").removeClass("col-md-4").addClass("col-md-5");
                $("#wrapperModalidadVenta").removeClass("col-md-2").addClass("col-md-3");
                this.productosDisponibles = data.productos || [];
                VentasView.aplicarCatalogoSede(data);
            } else {
                // Administrador: debe elegir la sede antes de ver categorias o productos
                $("#wrapperSedeVenta, #wrapperFiltroSedeVentas").show();
                $("#wrapperFiltroEstadoVentas").removeClass("col-md-9").addClass("col-md-5");
                $("#wrapperBtnRefrescarVentas").removeClass("col-md-3").addClass("col-md-2");
                $("#wrapperNumeroVenta").removeClass("col-md-4").addClass("col-md-3");
                $("#wrapperClienteVenta").removeClass("col-md-5").addClass("col-md-4");
                $("#wrapperModalidadVenta").removeClass("col-md-3").addClass("col-md-2");
                $("#selectSedeVenta").val("").prop("disabled", false);
                this.productosDisponibles = [];
                VentasView.limpiarCatalogoSede();
            }
        } catch (error) {
            Logger.error("Error al cargar catálogos de ventas", error);
        }
    },

    async cargarSedes() {
        try {
            const sedes = await VentasAPI.obtenerSedes();
            VentasView.poblarSedes(sedes);
        } catch (error) {
            Logger.error("Error al cargar sedes", error);
        }
    },

    async cargarVentas() {
        try {
            const filtros = {
                idSede: $("#filtroSedeVentas").val() || "",
                estado: $("#filtroEstadoVentas").val() || ""
            };
            const { ventas, resumen } = await VentasAPI.obtenerVentas(filtros);
            VentasView.renderizarKPIs(resumen);
            VentasView.renderizarTablaVentas(ventas);
        } catch (error) {
            Logger.error("Error al cargar listado de ventas", error);
        }
    },

    async prepararNuevaVenta() {
        this.itemsCarrito = [];
        VentasView.renderizarItemsCarrito([]);
        VentasView.mostrarIndicadorProducto(null);
        $("#inputDescuentoVenta").val("0").data("valor-limpio", "0");
        $("#observacionVenta").val("");
        $("#selectModalidadVenta").val("contado");
        $("#cantidadVenta").val("1");
        $("#precioUnitarioVenta").val("");
        $("#selectCategoriaVenta").val("");

        const idSede = this.sedeFija || $("#selectSedeVenta").val();
        if (idSede) {
            await this.cambiarSedeEnModal(idSede);
        } else {
            this.productosDisponibles = [];
            VentasView.limpiarCatalogoSede();
        }
    },

    async cambiarSedeEnModal(idSede) {
        try {
            const data = await VentasAPI.obtenerCatalogosVenta(idSede);
            this.productosDisponibles = data.productos || [];
            VentasView.aplicarCatalogoSede(data);
            VentasView.mostrarIndicadorProducto(null);
        } catch (error) {
            Logger.error("Error al actualizar productos de la sede", error);
        }
    },

    seleccionarProducto(idPresentacion) {
        const prod = this.productosDisponibles.find((p) => p.clave === String(idPresentacion));
        VentasView.mostrarIndicadorProducto(prod || null);

        if (prod) {
            const precioSugerido = Math.round(parseFloat(prod.precio_venta_final || prod.precio_venta_base || 0));
            $("#precioUnitarioVenta")
                .val(FormatUtils.separaMiles(precioSugerido))
                .data("valor-limpio", String(precioSugerido));
        } else {
            $("#precioUnitarioVenta").val("").data("valor-limpio", "0");
        }
    },

    agregarItemAlCarrito() {
        const idSede = $("#selectSedeVenta").val();
        const idPresentacion = $("#selectProductoVenta").val();
        const cantidad = parseInt($("#cantidadVenta").val(), 10) || 0;
        const precioUnitario = parseFloat(FormatUtils.obtenerValorLimpio($("#precioUnitarioVenta"))) || 0;

        if (!idSede) {
            Alerts.warning("Sede requerida", "Selecciona primero la sede de la venta");
            return;
        }
        if (!idPresentacion || cantidad <= 0 || precioUnitario <= 0) {
            Alerts.warning("Datos incompletos", "Selecciona un producto, cantidad válida y precio unitario");
            return;
        }

        const prod = this.productosDisponibles.find((p) => p.clave === String(idPresentacion));
        if (!prod) return;

        // Verificar cantidad total acumulada en el carrito para este producto
        const existente = this.itemsCarrito.find((i) => i.clave === String(idPresentacion));
        const cantidadEnCarrito = existente ? existente.cantidad : 0;
        const stockDisponible = parseFloat(prod.stock_disponible || 0);

        if (cantidad + cantidadEnCarrito > stockDisponible) {
            Alerts.error(
                "Stock insuficiente",
                `Disponible en sede: ${stockDisponible} ${prod.unidad_venta || "und"}. Intentas agregar: ${cantidad + cantidadEnCarrito}.`
            );
            return;
        }

        if (existente) {
            existente.cantidad += cantidad;
            existente.precioUnitario = precioUnitario;
            existente.subTotal = existente.cantidad * precioUnitario;
        } else {
            this.itemsCarrito.push({
                id: Date.now(),
                clave: String(idPresentacion),
                nombreCompleto: `${prod.nombre_categoria} / ${prod.nombre_producto} - ${prod.nombre_presentacion}`,
                cantidad,
                precioUnitario,
                subTotal: cantidad * precioUnitario
            });
        }

        VentasView.renderizarItemsCarrito(this.itemsCarrito);
        this.actualizarModalidadPago();

        // Limpiar fila de captura
        $("#selectProductoVenta").val("");
        $("#cantidadVenta").val("1");
        $("#precioUnitarioVenta").val("").data("valor-limpio", "0");
        VentasView.mostrarIndicadorProducto(null);
        Alerts.toasSuccess("Producto agregado a la venta");
    },

    quitarItemDelCarrito(id) {
        this.itemsCarrito = this.itemsCarrito.filter((i) => i.id !== id);
        VentasView.renderizarItemsCarrito(this.itemsCarrito);
        this.actualizarModalidadPago();
    },

    obtenerSubtotalCarrito() {
        return this.itemsCarrito.reduce((acc, item) => acc + item.subTotal, 0);
    },

    actualizarModalidadPago() {
        const modalidad = $("#selectModalidadVenta").val();
        const subtotal = this.obtenerSubtotalCarrito();
        const descuento = parseFloat(FormatUtils.obtenerValorLimpio($("#inputDescuentoVenta"))) || 0;
        const total = Math.max(0, subtotal - descuento);

        if (modalidad === "contado") {
            $("#labelMontoPagoInicial").text("Monto Pagado (Contado) *");
            $("#montoPagoInicial")
                .val(FormatUtils.separaMiles(Math.round(total)))
                .data("valor-limpio", String(Math.round(total)))
                .prop("readonly", true);
        } else {
            $("#labelMontoPagoInicial").text("Abono Inicial (Opcional)");
            $("#montoPagoInicial").prop("readonly", false);
        }

        this.recalcularTotalesCarrito();
    },

    recalcularTotalesCarrito() {
        const subtotal = this.obtenerSubtotalCarrito();
        let descuento = parseFloat(FormatUtils.obtenerValorLimpio($("#inputDescuentoVenta"))) || 0;
        if (descuento > subtotal) {
            descuento = subtotal;
        }
        const total = Math.max(0, subtotal - descuento);

        const modalidad = $("#selectModalidadVenta").val();
        let montoPagado = parseFloat(FormatUtils.obtenerValorLimpio($("#montoPagoInicial"))) || 0;

        if (modalidad === "contado") {
            montoPagado = total;
            $("#montoPagoInicial")
                .val(FormatUtils.separaMiles(Math.round(total)))
                .data("valor-limpio", String(Math.round(total)));
        } else if (montoPagado > total) {
            montoPagado = total;
        }

        const saldo = Math.max(0, total - montoPagado);

        $("#resumenSubtotalVenta").text(`$ ${FormatUtils.separaMiles(Math.round(subtotal))}`);
        $("#resumenTotalVenta").text(`$ ${FormatUtils.separaMiles(Math.round(total))}`);
        $("#resumenSaldoPendiente")
            .text(`$ ${FormatUtils.separaMiles(Math.round(saldo))}`)
            .toggleClass("text-danger", saldo > 0)
            .toggleClass("text-success", saldo === 0);
    },

    async guardarVenta(e) {
        e.preventDefault();

        if (!this.itemsCarrito.length) {
            Alerts.warning("Carrito vacío", "Debes agregar al menos un producto a la venta");
            return;
        }

        const idSede = $("#selectSedeVenta").val();
        const numeroVenta = $("#numeroVenta").val().trim();
        const modalidad = $("#selectModalidadVenta").val();
        const idCliente = $("#selectClienteVenta").val();
        const idMetodoPago = $("#selectMetodoPagoVenta").val();

        const subtotal = this.obtenerSubtotalCarrito();
        const descuento = Math.min(subtotal, parseFloat(FormatUtils.obtenerValorLimpio($("#inputDescuentoVenta"))) || 0);
        const total = Math.max(0, subtotal - descuento);
        const montoPagoInicial = modalidad === "contado"
            ? total
            : (parseFloat(FormatUtils.obtenerValorLimpio($("#montoPagoInicial"))) || 0);

        if (modalidad === "credito" && !idCliente) {
            Alerts.warning("Cliente requerido", "Para ventas a crédito debes seleccionar o registrar un cliente");
            return;
        }

        if (montoPagoInicial > 0 && !idMetodoPago) {
            Alerts.warning("Método de pago", "Selecciona el método de pago para el monto recibido");
            return;
        }

        const confirm = await Alerts.confirmation("Confirmar Venta", `¿Deseas registrar la venta por $ ${FormatUtils.separaMiles(Math.round(total))}?`);
        if (!confirm.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append("accion", "registrarVenta");
            formData.append("idSede", idSede);
            formData.append("numeroVenta", numeroVenta);
            formData.append("idCliente", idCliente || "");
            formData.append("subtotalVenta", subtotal);
            formData.append("descuentoVenta", descuento);
            formData.append("totalVenta", total);
            formData.append("idMetodoPago", idMetodoPago || "");
            formData.append("montoPagoInicial", montoPagoInicial);
            formData.append("observacionVenta", $("#observacionVenta").val().trim());
            formData.append("detalles", JSON.stringify(this.itemsCarrito));

            const resp = await VentasAPI.registrarVenta(formData);
            if (resp.success) {
                Alerts.success("Venta Registrada", resp.message || "La venta se registró exitosamente");
                this.modalVenta.hide();
                this.cargarVentas();
            } else {
                Alerts.error("Error", resp.message || "No se pudo registrar la venta");
            }
        } catch (error) {
            Alerts.error("Error", error.message || "No se pudo registrar la venta");
        }
    },

    async verDetalleVenta(idVenta) {
        try {
            const { venta, detalles, pagos } = await VentasAPI.obtenerDetalleVenta(idVenta);
            if (!venta) {
                Alerts.error("No encontrada", "No se encontró la información de la venta");
                return;
            }
            VentasView.renderizarDetalleModal(venta, detalles, pagos);
            this.modalDetalle.show();
        } catch (error) {
            Alerts.error("Error", error.message || "No se pudo obtener el detalle de la venta");
        }
    },

    async abrirModalAbono(idVenta) {
        try {
            const { venta } = await VentasAPI.obtenerDetalleVenta(idVenta);
            if (!venta) return;

            this.ventaActualAbono = venta;
            const total = Math.round(parseFloat(venta.total_venta || 0));
            const pagado = Math.round(parseFloat(venta.total_pagado || 0));
            const saldo = Math.max(0, total - pagado);

            $("#abonoIdVenta").val(venta.id_venta);
            $("#abonoNumeroVenta").text(venta.numero_venta);
            $("#abonoClienteVenta").text(venta.nombre_cliente || "Consumidor Final");
            $("#abonoTotalVenta").text(`$ ${FormatUtils.separaMiles(total)}`);
            $("#abonoPagadoVenta").text(`$ ${FormatUtils.separaMiles(pagado)}`);
            $("#abonoSaldoPendiente").text(`$ ${FormatUtils.separaMiles(saldo)}`);
            $("#montoAbonoVenta")
                .val(FormatUtils.separaMiles(saldo))
                .data("valor-limpio", String(saldo));

            this.modalAbono.show();
        } catch (error) {
            Alerts.error("Error", "No se pudo cargar la información para el abono");
        }
    },

    async guardarAbono(e) {
        e.preventDefault();
        const idVenta = $("#abonoIdVenta").val();
        const idMetodoPago = $("#selectMetodoPagoAbono").val();
        const montoPago = parseFloat(FormatUtils.obtenerValorLimpio($("#montoAbonoVenta"))) || 0;

        if (!idMetodoPago || montoPago <= 0) {
            Alerts.warning("Datos inválidos", "Selecciona un método de pago e ingresa un monto mayor a 0");
            return;
        }

        try {
            const formData = new FormData();
            formData.append("accion", "registrarAbono");
            formData.append("idVenta", idVenta);
            formData.append("idMetodoPago", idMetodoPago);
            formData.append("montoPago", montoPago);

            const resp = await VentasAPI.registrarAbono(formData);
            if (resp.success) {
                Alerts.success("Abono Registrado", resp.message || "El abono fue registrado correctamente");
                this.modalAbono.hide();
                this.cargarVentas();
            } else {
                Alerts.error("Error", resp.message || "No se pudo registrar el abono");
            }
        } catch (error) {
            Alerts.error("Error", error.message || "Error al registrar el abono");
        }
    },

    async anularVenta(idVenta) {
        const confirm = await Alerts.confirmation(
            "¿Anular esta venta?",
            "Se devolverá el stock a la sede correspondiente y se revertirá el ingreso en caja. Esta acción no se puede deshacer."
        );
        if (!confirm.isConfirmed) return;

        try {
            const resp = await VentasAPI.anularVenta(idVenta);
            if (resp.success) {
                Alerts.success("Venta Anulada", resp.message || "La venta ha sido anulada y el stock restaurado");
                this.cargarVentas();
            } else {
                Alerts.error("Error", resp.message || "No se pudo anular la venta");
            }
        } catch (error) {
            Alerts.error("Error", error.message || "Error al anular la venta");
        }
    },

    async guardarCliente(e) {
        e.preventDefault();
        const nombre = $("#nombreCliente").val().trim();
        if (!nombre) {
            Alerts.warning("Nombre obligatorio", "Ingresa el nombre del cliente");
            return;
        }

        try {
            const formData = new FormData(e.target);
            formData.append("accion", "registrarCliente");

            const resp = await VentasAPI.registrarCliente(formData);
            if (resp.success) {
                Alerts.toasSuccess("Cliente registrado");
                $("#formRegistroCliente")[0].reset();
                this.modalCliente.hide();

                const clientes = resp.data?.clientes || [];
                const nuevoId = resp.data?.id_cliente || null;
                VentasView.poblarClientes(clientes, nuevoId);
            } else {
                Alerts.error("Error", resp.message || "No se pudo registrar el cliente");
            }
        } catch (error) {
            Alerts.error("Error", error.message || "No se pudo registrar el cliente");
        }
    }
};

window.ventasModule = ventasModule;

