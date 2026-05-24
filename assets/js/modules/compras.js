/**
 * CAPA API — Solo responsable de comunicarse con el servidor
 * Los métodos aquí devuelven datos del servidor sin modificar
 */
const ComprasAPI = {
    obtenerProductosCompra() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/productosAjax.php",
                method: "GET",
                data: { accion: "productosCompra" },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                },
            });
        });
    },

    obtenerProveedores() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/proveedorAjax.php",
                method: "GET",
                data: { accion: "listadoProveedoresSelect" },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                },
            });
        });
    },

    registrarCompra(formData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/comprasAjax.php",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos);
                },
                error(error) {
                    reject(error);
                },
            });
        });
    },

    obtenerSedes() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/catalogoAjax.php",
                method: "GET",
                data: { accion: "listadoSedes" },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                },
            });
        });
    },

    obtenerCompras() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/comprasAjax.php",
                method: "GET",
                data: { accion: "listarCompras" },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                },
            });
        });
    }
};

/**
 * CAPA VIEW — Solo responsable de renderizar HTML y manipular el DOM
 * Recibe datos y los convierte en HTML, sin hacer lógica de negocio
 */
const ComprasView = {
    poblarSelectProductos(productos) {
        let listado = '<option value="">Seleccione producto</option>';
        productos.forEach((producto) => {
            listado += `<option value="${producto.id_presentacion}"> ${producto.nombre_categoria} / ${producto.nombre_producto} - ${producto.nombre_presentacion}</option>`;
        });
        $("#selectProducto").html(listado);
    },


    poblarSelect(selectId, items, valueKey, textKey) {
        const select = $(selectId);
        items.forEach(item => {
            select.append(new Option(item[textKey], item[valueKey]));
        });
    },

    renderizarCompras(compras) {
        const tbody = $("#comprasTableBody");

        if (!compras || compras.length === 0) {
            tbody.html(`<tr><td colspan="6" class="text-center text-muted">No hay compras registradas</td></tr>`);
            return;
        }

        const rows = compras.map(compra => `
            <tr>
                <td>${compra.id_compras}</td>
                <td>${compra.presentaciones || 'N/A'}</td>
                <td class="text-center">${compra.cantidad_detalles || 0}</td>
                <td class="text-right">$ ${FormatUtils.separaMiles(compra.total_compra || 0)}</td>
                <td>$ ${FormatUtils.separaMiles(Math.round(compra.total_compra / (compra.cantidad_detalles || 1)) || 0)}</td>
                <td class="text-muted">${new Date().toLocaleDateString()}</td>
            </tr>
        `).join('');

        tbody.html(rows);
    }
};

/**
 * MÓDULO COMPRAS — Coordina API y View, maneja estado y lógica de negocio
 */
const comprasModule = {
    idCompra: null,
    detallesAcumulados: [],
    init() {
        this.bindEvents();
        this.cargarCompras();
        this.cargarReporteCapacidad();

        //Modales
        this.modalCompra = new bootstrap.Modal(document.getElementById("modalRegistroCompra"));
        this.modalDetalleCompra = new bootstrap.Modal(document.getElementById("modalRegistroDetalleCompra"));
    },

    bindEvents() {

        $("#btnModalCompra").on("click", () => {
            this.cargarDatos();
        });

        $("#btnModalDetalleCompra").on("click", () => {
            this.cargarDatos();
            this.limpiarAcumulador();
        });

        $("[data-format-miles]").on("input", (e) => FormatUtils.formatearMiles(e));
        $("#registroDeCompra").on("submit", (e) => this.registrarCompra(e));

        $("#costoUnitario").on("keyup", (e) => {
            let subTotal = e.target.value.replace(/\D/g, "") * $("#cantidad").val();
            $("#subtotal").val(FormatUtils.separaMiles(subTotal));
        });

        $("#btnAgregarDetalle").on("click", () => this.agregarDetalleAlAcumulador());
        $("#btnRegistrarDetalles").on("click", (e) => this.registrarDetalles(e));

        $('#modalRegistroDetalleCompra').on('hidden.bs.modal', () => {
            this.limpiarAcumulador();
        });

    },

    async cargarDatos() {
        try {
            await Promise.all([
                this.cargarProductos(),
                this.cargarProveedores(),
                this.cargarSedes(),
            ]);
        } catch (error) {
            Alerts.error("Error", "No se pudieron cargar los datos");
            console.error(error);
        }
    },

    async cargarProductos() {
        try {
            const productos = await ComprasAPI.obtenerProductosCompra();
            ComprasView.poblarSelectProductos(productos);
        } catch (error) {
            console.error("Error al cargar productos:", error);
        }
    },

    async cargarProveedores() {
        try {
            const proveedores = await ComprasAPI.obtenerProveedores();
            ComprasView.poblarSelect('#selectProveedor', proveedores, 'id_proveedor', 'nombre_proveedor');
        } catch (error) {
            console.error("Error al cargar proveedores:", error);
        }
    },

    async cargarSedes() {
        try {
            const sedes = await ComprasAPI.obtenerSedes();
            ComprasView.poblarSelect('#selectSede', sedes, 'id_sede', 'nombre_sede');
        } catch (error) {
            console.error("Error al cargar sedes:", error);
        }
    },

    async registrarCompra(e) {
        e.preventDefault();
        const resultado = await Alerts.confirmation("Confirmar compra", "¿Desea registrar esta compra?");

        if(!resultado.isConfirmed) return;

        try {
            const formData = new FormData(e.target);
            formData.append("accion", "registrarCompra")
            const respuesta = await ComprasAPI.registrarCompra(formData);

            if (respuesta.success) {
                Alerts.success("Compra registrada", respuesta.message);
                this.idCompra = respuesta.data.id_compras;
                $("#registroDeCompra")[0].reset();
                this.modalCompra.hide();
                this.modalDetalleCompra.show();
            } else {
                Alerts.error("Error", respuesta.message || "No se pudo registrar la compra");
            }

        } catch (error) {
            Alerts.error("Error", "No se pudo registrar la compra");
            console.error(error);
        }
    },

    agregarDetalleAlAcumulador() {
        const idSede = $("#selectSede").val();
        const nombreSede = $("#selectSede option:selected").text();
        const idProductoPresentacion = $("#selectProducto").val();
        const nombreProducto = $("#selectProducto option:selected").text();
        const cantidad = $("#cantidad").val();
        const costoUnitario = $("#costoUnitario").val();

        if (!idSede || !idProductoPresentacion || !cantidad || !costoUnitario) {
            Alerts.error("Campos incompletos", "Por favor completa: sede, producto, cantidad y costo unitario");
            return;
        }

        const costoUnitarioLimpio = FormatUtils.obtenerValorLimpio($("#costoUnitario"));
        const subTotal = parseInt(costoUnitarioLimpio) * parseInt(cantidad);

        const detalle = {
            id: Date.now(),
            idSede: idSede,
            nombreSede: nombreSede,
            idProducto: idProductoPresentacion,
            nombreProducto: nombreProducto,
            cantidad: parseInt(cantidad),
            costoUnitario: parseInt(costoUnitarioLimpio),
            subTotal: subTotal
        };

        this.detallesAcumulados.push(detalle);
        this.mostrarDetallesEnTabla();
        this.limpiarFormulario();
        Alerts.toasSuccess("Detalle agregado correctamente");
    },

    mostrarDetallesEnTabla() {
        const container = $("#detallesAcumuladosContainer");
        const tabla = $("#tablaDetalles");

        if (this.detallesAcumulados.length === 0) {
            container.hide();
            return;
        }

        container.show();
        tabla.html(this.detallesAcumulados.map(d => `
            <tr>
                <td>${d.nombreSede}</td>
                <td>${d.nombreProducto}</td>
                <td>${d.cantidad}</td>
                <td>$ ${FormatUtils.separaMiles(d.costoUnitario)}</td>
                <td>$ ${FormatUtils.separaMiles(d.subTotal)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-eliminar-detalle" data-id="${d.id}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join(''));

        $(document).off("click", ".btn-eliminar-detalle").on("click", ".btn-eliminar-detalle", (e) => {
            const id = $(e.currentTarget).data("id");
            this.eliminarDetalle(id);
        });
    },

    eliminarDetalle(id) {
        this.detallesAcumulados = this.detallesAcumulados.filter(d => d.id !== id);
        this.mostrarDetallesEnTabla();
        Alerts.toasSuccess("Detalle eliminado");
    },

    limpiarFormulario() {
        $("#formDetalleCompra")[0].reset();
        $("#subtotal").val("");
    },

    limpiarAcumulador() {
        this.detallesAcumulados = [];
        $("#detallesAcumuladosContainer").hide();
        $("#tablaDetalles").html("");
        this.limpiarFormulario();
    },

    async registrarDetalles(e) {
        e.preventDefault();

        if (this.detallesAcumulados.length === 0) {
            Alerts.error("Sin detalles", "Debes agregar al menos un detalle de compra");
            return;
        }

        const resultado = await Alerts.confirmation(
            "Registrar detalles?",
            `¿Estás seguro de que deseas registrar ${this.detallesAcumulados.length} detalle(s)?`
        );

        if (!resultado.isConfirmed) return;

        try {
            const formData = new FormData();
            formData.append("accion", "registrarDetallesCompra");
            formData.append("idCompra", this.idCompra);
            formData.append("detalles", JSON.stringify(this.detallesAcumulados));

            const respuesta = await ComprasAPI.registrarCompra(formData);

            if (respuesta.success) {
                Alerts.success("Detalles registrados", respuesta.message);
                this.limpiarAcumulador();
                $("#modalRegistroDetalleCompra").modal("hide");
                // Recargar compras y reporte de capacidad después de registrar
                setTimeout(() => {
                    this.cargarCompras();
                    this.cargarReporteCapacidad();
                }, 500);
            } else {
                Alerts.error("Error", respuesta.message || "No se pudieron registrar los detalles");
            }
        } catch (error) {
            Alerts.error("Error", "No se pudieron registrar los detalles");
            console.error(error);
        }
    },

    async cargarCompras() {
        try {
            const compras = await ComprasAPI.obtenerCompras();
            ComprasView.renderizarCompras(compras);
        } catch (error) {
            console.error("Error al cargar compras:", error);
        }
    },

    async cargarReporteCapacidad() {
        try {
            const response = await fetch("/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/capacidadAjax.php?accion=capacidadProduccion");
            const datos = await response.json();

            if (datos.success && datos.data && datos.data.length > 0) {
                this.renderizarReporte(datos.data);
            } else {
                $("#reporteCapacidadContainer").hide();
                $("#sinReporteMessage").show();
            }
        } catch (error) {
            console.error("Error al cargar reporte de capacidad:", error);
            $("#sinReporteMessage").show();
        }
    },

    renderizarReporte(esencias) {
        const container = $("#reporteCapacidad");

        // Guardar esencias globalmente para búsqueda
        window.todasLasEsencias = esencias;

        // HTML con buscador global
        const html = `
            <div class="col-md-12 mb-4">
                <input type="text" id="buscador-esencias" placeholder="🔍 Buscar esencia..."
                    style="
                        width: 100%;
                        padding: 12px 16px;
                        background-color: #18181b;
                        border: 2px solid #27272a;
                        color: #f4f4f5;
                        border-radius: 6px;
                        font-size: 14px;
                        margin-bottom: 20px;
                    ">
            </div>
            <div id="container-esencias"></div>
        `;

        container.html(html);

        // Renderizar esencias
        this.renderizarEsencias(esencias);

        // Evento de búsqueda
        $(document).off('keyup', '#buscador-esencias').on('keyup', '#buscador-esencias', function() {
            const searchText = $(this).val().toLowerCase();
            const esenciasFiltridas = window.todasLasEsencias.filter(e =>
                e.nombre_esencia.toLowerCase().includes(searchText) ||
                e.nombre_presentacion.toLowerCase().includes(searchText)
            );
            comprasModule.renderizarEsencias(esenciasFiltridas);
        });

        $("#reporteCapacidadContainer").show();
        $("#sinReporteMessage").hide();
    },

    renderizarEsencias(esencias) {
        const containerEsencias = $("#container-esencias");
        containerEsencias.html("");

        esencias.forEach((esencia, idx) => {
            const esenciaId = `esencia-${idx}`;

            const card = `
                <div class="col-md-12 mb-3">
                    <div style="
                        border: 1px solid #27272a;
                        background-color: #18181b;
                        border-left: 5px solid #eab308;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
                        border-radius: 8px;
                        overflow: hidden;
                    ">
                        <!-- HEADER ACORDEÓN -->
                        <div class="acordeon-header" data-target="#${esenciaId}" style="
                            padding: 15px 20px;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            background-color: #09090b;
                            border-bottom: 1px solid #27272a;
                            transition: background-color 0.2s;
                        " onmouseover="this.style.backgroundColor='#18181b'" onmouseout="this.style.backgroundColor='#09090b'">
                            <div style="display: flex; align-items: center; flex: 1;">
                                <i class="fa-solid fa-droplet" style="color: #eab308; font-size: 20px; margin-right: 12px;"></i>
                                <div>
                                    <div style="color: #f4f4f5; font-weight: 700; font-size: 16px;">
                                        ${esencia.nombre_esencia}
                                    </div>
                                    <div style="color: #a1a1aa; font-size: 12px; margin-top: 3px;">
                                        ${FormatUtils.separaMiles(esencia.cantidad_gramos.toFixed(0))} gramos • $${FormatUtils.separaMiles(esencia.costo_por_gramo.toFixed(0))}/gramo
                                    </div>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-down" style="color: #eab308; margin-left: 15px; transition: transform 0.3s; font-size: 18px;"></i>
                        </div>

                        <!-- CONTENIDO ACORDEÓN -->
                        <div id="${esenciaId}" class="acordeon-content" style="display: none; padding: 20px;">
                            <table style="width: 100%; font-size: 13px; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #27272a;">
                                        <th style="color: #a1a1aa; text-align: left; padding: 10px 0; font-weight: 600;">
                                            Preparación
                                        </th>
                                        <th style="color: #a1a1aa; text-align: center; padding: 10px 0; font-weight: 600; width: 120px;">
                                            Cantidad
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${esencia.capacidad_por_formula.map(item => `
                                        <tr style="
                                            border-bottom: 1px solid #27272a;
                                            opacity: ${item.cantidad_puede_producir > 0 ? '1' : '0.5'};
                                            background-color: ${item.cantidad_puede_producir === 0 ? '#09090b' : 'transparent'};
                                        ">
                                            <td style="color: ${item.cantidad_puede_producir > 0 ? '#f4f4f5' : '#71717a'}; padding: 10px 0;">
                                                ${item.nombre_locion}
                                            </td>
                                            <td style="text-align: center; padding: 10px 0;">
                                                <span style="
                                                    background-color: ${item.cantidad_puede_producir > 0 ? '#22c55e' : '#3f3f46'};
                                                    color: ${item.cantidad_puede_producir > 0 ? '#000000' : '#a1a1aa'};
                                                    padding: 6px 12px;
                                                    border-radius: 4px;
                                                    font-size: 12px;
                                                    font-weight: 600;
                                                    display: inline-block;
                                                ">
                                                    ${item.cantidad_puede_producir} unid.
                                                </span>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;

            containerEsencias.append(card);
        });

        // Eventos de acordeón (después de renderizar)
        $(document).off('click', '.acordeon-header').on('click', '.acordeon-header', function(e) {
            e.stopPropagation();
            const target = $(this).data('target');
            const content = $(target);
            const icon = $(this).find('i.fa-chevron-down');
            const isVisible = content.is(':visible');

            // Rotar el icono
            icon.css('transform', isVisible ? 'rotate(0deg)' : 'rotate(180deg)');

            // Animar el contenido
            content.slideToggle(300);
        });
    }
};
