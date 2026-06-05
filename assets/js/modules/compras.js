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

    obtenerCategorias() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/catalogoAjax.php",
                method: "GET",
                data: { accion: "listadoCategorias" },
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
    },

    obtenerDetalleCompra(idCompra) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/comprasAjax.php",
                method: "GET",
                data: { accion: "detalleCompra", idCompra },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || {});
                },
                error(error) { reject(error); },
            });
        });
    },

    actualizarCompra(formData) {
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
                error(error) { reject(error); },
            });
        });
    },

    eliminarCompra(idCompra) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/comprasAjax.php",
                method: "POST",
                data: { accion: "eliminarCompra", idCompra },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos);
                },
                error(error) { reject(error); },
            });
        });
    },
};

/**
 * CAPA VIEW — Solo responsable de renderizar HTML y manipular el DOM
 * Recibe datos y los convierte en HTML, sin hacer lógica de negocio
 */
const ComprasView = {
    poblarSelectCategorias(categorias) {
        ComprasView.poblarSelect('#selectCategoria', categorias, 'id_categoria', 'nombre_categoria');
        ComprasView.poblarSelect('#editSelectCategoria', categorias, 'id_categoria', 'nombre_categoria');
    },

    poblarSelectProductos(productos, categoriaSeleccionada = null) {
        let listado = '<option value="">Seleccione producto</option>';

        const productosFiltrados = categoriaSeleccionada
            ? productos.filter(p => p.id_categoria == categoriaSeleccionada)
            : productos;

        productosFiltrados.forEach((producto) => {
            listado += `<option value="${producto.id_presentacion}">${producto.nombre_producto} - ${producto.nombre_presentacion}</option>`;
        });

        $("#selectProducto").html(listado);
    },

    poblarSelectProductosEdit(productos, categoriaSeleccionada = null) {
        let listado = '<option value="">Seleccione producto</option>';

        const productosFiltrados = categoriaSeleccionada
            ? productos.filter(p => p.id_categoria == categoriaSeleccionada)
            : productos;

        productosFiltrados.forEach((producto) => {
            listado += `<option value="${producto.id_presentacion}">${producto.nombre_producto} - ${producto.nombre_presentacion}</option>`;
        });
        $("#editSelectProducto").html(listado);
    },

    poblarSelect(selectId, items, valueKey, textKey) {
        const select = $(selectId);
        items.forEach(item => {
            select.append(new Option(item[textKey], item[valueKey]));
        });
    },

    renderizarDetalleCompra(compra, detalles) {
        $("#detalleProveedor").text(compra.nombre_proveedor || "N/A");
        $("#detalleFactura").text(compra.numero_factura_compra || "N/A");
        $("#detalleTotal").text(`$ ${FormatUtils.separaMiles(compra.total_compra || 0)}`);

        if (!detalles || detalles.length === 0) {
            $("#tablaDetalleCompra").html(`<tr><td colspan="6" class="text-center text-muted">Sin ítems</td></tr>`);
            return;
        }
        const rows = detalles.map((d, i) => `
            <tr>
                <td>${i + 1}</td>
                <td>${d.nombre_sede || "N/A"}</td>
                <td>${d.nombre_categoria || ""} / ${d.nombre_producto || ""} - ${d.nombre_presentacion || ""}</td>
                <td class="text-center">${d.cantidad_detalle_compra}</td>
                <td class="text-end">$ ${FormatUtils.separaMiles(d.costo_unitario_detalle_compra || 0)}</td>
                <td class="text-end">$ ${FormatUtils.separaMiles(d.subtotal_detalle_compra || 0)}</td>
            </tr>
        `).join("");
        $("#tablaDetalleCompra").html(rows);
    },

    renderizarTablaEdicion(detalles) {
        const container = $("#editDetallesAcumuladosContainer");
        const tabla = $("#editTablaDetalles");
        if (!detalles || detalles.length === 0) {
            container.hide();
            return;
        }
        container.show();
        tabla.html(detalles.map(d => `
            <tr>
                <td>${d.nombreSede}</td>
                <td>${d.nombreProducto}</td>
                <td>${d.cantidad}</td>
                <td>$ ${FormatUtils.separaMiles(d.precioCompra)}</td>
                <td>$ ${FormatUtils.separaMiles(d.subTotal)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger btn-eliminar-detalle-edit" data-id="${d.id}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join(""));
    },

    renderizarCompras(compras) {
        if ($.fn.DataTable.isDataTable("#tablaCompras")) {
            $("#tablaCompras").DataTable().destroy();
        }

        $("#tablaCompras").DataTable({
            data: compras || [],
            columns: [
                { data: "id_compras" },
                { data: "numero_factura_compra", defaultContent: "N/A" },
                { data: "nombre_proveedor", defaultContent: "N/A" },
                {
                    data: "total_compra",
                    render: val => `$ ${FormatUtils.separaMiles(val || 0)}`
                },
                {
                    data: "fecha_compra",
                    defaultContent: "N/A"
                },
                {
                    data: null,
                    orderable: false,
                    render(data, type, row) {
                        const id = row.id_compras;
                        return `
                            <button class="btn btn-sm btn-primary btn-ver-detalles" data-id="${id}">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-secondary btn-editar-compra" data-id="${id}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar-compra" data-id="${id}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            order: [[0, "desc"]],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron compras",
                emptyTable: "No hay compras registradas",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                infoEmpty: "Mostrando registros del 0 al 0 de un total de 0",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });
    }
};

/**
 * MÓDULO COMPRAS — Coordina API y View, maneja estado y lógica de negocio
 */
const comprasModule = {
    productos: [],
    detallesAcumulados: [],
    editDetallesAcumulados: [],
    init() {
        this.bindEvents();
        this.cargarCompras();
        this.modalCompra = new bootstrap.Modal(document.getElementById("modalRegistroCompra"));
        this.modalDetalle = new bootstrap.Modal(document.getElementById("modalDetalleCompra"));
        this.modalEditar = new bootstrap.Modal(document.getElementById("modalEditarCompra"));
    },
    bindEvents() {
        $("#btnModalCompra").on("click", () => {
            this.cargarDatos();
            this.limpiarAcumulador();
        });
        $("[data-format-miles]").on("input", (e) => FormatUtils.formatearMiles(e));
        $("[data-format-miles-edit]").on("input", (e) => FormatUtils.formatearMiles(e));
        $("#registroDeCompra").on("submit", (e) => this.registrarCompra(e));
        $("#costoUnitario, #cantidad").on("input", () => this.calcularSubtotal());
        $("#btnAgregarDetalle").on("click", () => this.agregarDetalleAlAcumulador());

        // Filtrado de categorías
        $("#selectCategoria").on("change", (e) => {
            const categoriaSeleccionada = $(e.target).val();
            ComprasView.poblarSelectProductos(this.productos, categoriaSeleccionada);
        });

        $("#editSelectCategoria").on("change", (e) => {
            const categoriaSeleccionada = $(e.target).val();
            ComprasView.poblarSelectProductosEdit(this.productos, categoriaSeleccionada);
        });

        // Editar
        $("#editCostoUnitario, #editCantidad").on("input", () => this.calcularSubtotalEdicion());
        $("#editBtnAgregarDetalle").on("click", () => this.agregarDetalleEdicion());
        $("#formEditarCompra").on("submit", (e) => this.guardarEdicion(e));

        // Tabla principal — delegación de eventos
        $(document).on("click", ".btn-ver-detalles", (e) => {
            const id = $(e.currentTarget).data("id");
            this.abrirDetalle(id);
        });
        $(document).on("click", ".btn-editar-compra", (e) => {
            const id = $(e.currentTarget).data("id");
            this.abrirEdicion(id);
        });
        $(document).on("click", ".btn-eliminar-compra", (e) => {
            const id = $(e.currentTarget).data("id");
            this.eliminarCompra(id);
        });
    },
    async cargarDatos() {
        try {
            await Promise.all([
                this.cargarProductos(),
                this.cargarProveedores(),
                this.cargarSedes(),
                this.cargarCategorias(),
            ]);
        } catch (error) {
            Alerts.error("Error", "No se pudieron cargar los datos");
            console.error(error);
        }
    },
    async cargarProductos() {
        try {
            const productos = await ComprasAPI.obtenerProductosCompra();
            this.productos = productos;
            ComprasView.poblarSelectProductos(productos);
        } catch (error) {
            console.error("Error al cargar productos:", error);
        }
    },


    async cargarCategorias() {
        try {
            const categorias = await ComprasAPI.obtenerCategorias();
            ComprasView.poblarSelectCategorias(categorias);
        } catch (error) {
            console.error("Error al cargar categorías:", error);
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
    calcularSubtotal() {
        const cantidad = parseInt($("#cantidad").val()) || 0;
        const costoUnitario = FormatUtils.obtenerValorLimpio($("#costoUnitario"));
        const subtotal = cantidad * costoUnitario;
        $("#subtotal").val(FormatUtils.separaMiles(subtotal));
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
            idProductoPresentacion: idProductoPresentacion,
            nombreProducto: nombreProducto,
            cantidad: parseInt(cantidad),
            precioCompra: parseInt(costoUnitarioLimpio),
            costoUnitario: parseInt(costoUnitarioLimpio),
            subTotal: subTotal
        };
        this.detallesAcumulados.push(detalle);
        this.mostrarDetallesEnTabla();
        this.limpiarFormularioDetalle();
        Alerts.toasSuccess("Detalle agregado correctamente");
    },
    mostrarDetallesEnTabla() {
        const container = $("#detallesAcumuladosContainer");
        const tabla = $("#tablaDetalles");
        if (this.detallesAcumulados.length === 0) {
            container.hide();
            $("#totalCompra").val("");
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
        this.actualizarTotalCompra();
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
    limpiarFormularioDetalle() {
        $("#cantidad").val("");
        $("#costoUnitario").val("");
        $("#subtotal").val("");
        // $("#selectSede").val(""); // No limpiar sede para que se mantenga seleccionada
        $("#selectCategoria").val("");
        $("#selectProducto").val("");
    },
    limpiarAcumulador() {
        this.detallesAcumulados = [];
        $("#detallesAcumuladosContainer").hide();
        $("#tablaDetalles").html("");
        this.limpiarFormularioDetalle();
        $("#totalCompra").val("");
    },
    actualizarTotalCompra() {
        const total = this.detallesAcumulados.reduce((acc, d) => acc + d.subTotal, 0);
        $("#totalCompra").val(FormatUtils.separaMiles(total));
    },
    async registrarCompra(e) {
        e.preventDefault();
        if (this.detallesAcumulados.length === 0) {
            Alerts.error("Sin detalles", "Debes agregar al menos un detalle de compra");
            return;
        }
        const resultado = await Alerts.confirmation("Confirmar compra", "¿Desea registrar esta compra?");
        if(!resultado.isConfirmed) return;
        try {
            const formData = new FormData(e.target);
            formData.append("accion", "registrarCompra");
            formData.append("detalles", JSON.stringify(this.detallesAcumulados));
            // El total se calcula en JS y se envía
            const total = this.detallesAcumulados.reduce((acc, d) => acc + d.subTotal, 0);
            formData.set("totalCompra", total);
            const respuesta = await ComprasAPI.registrarCompra(formData);
            if (respuesta.success) {
                Alerts.success("Compra registrada", respuesta.message);
                $("#registroDeCompra")[0].reset();
                this.limpiarAcumulador();
                this.modalCompra.hide();
                this.cargarCompras();
            } else {
                Alerts.error("Error", respuesta.message || "No se pudo registrar la compra");
            }
        } catch (error) {
            Alerts.error("Error", "No se pudo registrar la compra");
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

    async abrirDetalle(idCompra) {
        try {
            const { compra, detalles } = await ComprasAPI.obtenerDetalleCompra(idCompra);
            ComprasView.renderizarDetalleCompra(compra, detalles);
            this.modalDetalle.show();
        } catch (error) {
            Alerts.error("Error", "No se pudo cargar el detalle de la compra");
        }
    },

    async abrirEdicion(idCompra) {
        try {
            const { compra, detalles } = await ComprasAPI.obtenerDetalleCompra(idCompra);
            await this.cargarDatosEdicion();

            $("#editIdCompra").val(compra.id_compras);
            $("#editNumeroFactura").val(compra.numero_factura_compra);

            // Seleccionar proveedor
            $("#editSelectProveedor").val(compra.id_proveedor);

            // Cargar detalles existentes al acumulador de edición
            this.editDetallesAcumulados = detalles.map(d => ({
                id: d.id_detalle_compra,
                idSede: d.id_sede,
                nombreSede: d.nombre_sede,
                idProductoPresentacion: d.id_presentacion,
                nombreProducto: `${d.nombre_categoria} / ${d.nombre_producto} - ${d.nombre_presentacion}`,
                cantidad: d.cantidad_detalle_compra,
                precioCompra: d.costo_unitario_detalle_compra,
                costoUnitario: d.costo_unitario_detalle_compra,
                subTotal: d.subtotal_detalle_compra,
            }));

            this.mostrarDetallesEdicion();
            this.modalEditar.show();
        } catch (error) {
            Alerts.error("Error", "No se pudo cargar la compra para editar");
        }
    },

    async cargarDatosEdicion() {
        await Promise.all([
            ComprasAPI.obtenerProveedores().then(proveedores => {
                const select = $("#editSelectProveedor");
                select.html('<option value="">Seleccione proveedor</option>');
                proveedores.forEach(p => select.append(new Option(p.nombre_proveedor, p.id_proveedor)));
            }),
            ComprasAPI.obtenerProductosCompra().then(productos => {
                let listado = '<option value="">Seleccione producto</option>';
                productos.forEach(p => {
                    listado += `<option value="${p.id_presentacion}">${p.nombre_categoria} / ${p.nombre_producto} - ${p.nombre_presentacion}</option>`;
                });
                $("#editSelectProducto").html(listado);
            }),
            ComprasAPI.obtenerSedes().then(sedes => {
                const select = $("#editSelectSede");
                select.html('<option value="">Seleccione sede</option>');
                sedes.forEach(s => select.append(new Option(s.nombre_sede, s.id_sede)));
            }),
        ]);
    },

    calcularSubtotalEdicion() {
        const cantidad = parseInt($("#editCantidad").val()) || 0;
        const costoUnitario = FormatUtils.obtenerValorLimpio($("#editCostoUnitario"));
        $("#editSubtotal").val(FormatUtils.separaMiles(cantidad * costoUnitario));
    },

    agregarDetalleEdicion() {
        const idSede = $("#editSelectSede").val();
        const nombreSede = $("#editSelectSede option:selected").text();
        const idProductoPresentacion = $("#editSelectProducto").val();
        const nombreProducto = $("#editSelectProducto option:selected").text();
        const cantidad = $("#editCantidad").val();
        const costoUnitarioLimpio = FormatUtils.obtenerValorLimpio($("#editCostoUnitario"));

        if (!idSede || !idProductoPresentacion || !cantidad || !costoUnitarioLimpio) {
            Alerts.error("Campos incompletos", "Completa sede, producto, cantidad y costo unitario");
            return;
        }

        const subTotal = parseInt(costoUnitarioLimpio) * parseInt(cantidad);
        this.editDetallesAcumulados.push({
            id: Date.now(),
            idSede, nombreSede,
            idProductoPresentacion, nombreProducto,
            cantidad: parseInt(cantidad),
            precioCompra: parseInt(costoUnitarioLimpio),
            costoUnitario: parseInt(costoUnitarioLimpio),
            subTotal,
        });

        this.mostrarDetallesEdicion();
        $("#editCantidad, #editCostoUnitario, #editSubtotal").val("");
        $("#editSelectCategoria").val("");
        $("#editSelectProducto").val("");
        Alerts.toasSuccess("Ítem agregado");
    },

    mostrarDetallesEdicion() {
        ComprasView.renderizarTablaEdicion(this.editDetallesAcumulados);

        if (this.editDetallesAcumulados.length > 0) {
            const total = this.editDetallesAcumulados.reduce((acc, d) => acc + d.subTotal, 0);
            $("#editTotalCompra").val(FormatUtils.separaMiles(total));
            $("#editDetallesAcumuladosContainer").show();
        } else {
            $("#editTotalCompra").val("");
            $("#editDetallesAcumuladosContainer").hide();
        }

        $(document).off("click", ".btn-eliminar-detalle-edit").on("click", ".btn-eliminar-detalle-edit", (e) => {
            const id = $(e.currentTarget).data("id");
            this.editDetallesAcumulados = this.editDetallesAcumulados.filter(d => d.id != id);
            this.mostrarDetallesEdicion();
        });
    },

    async guardarEdicion(e) {
        e.preventDefault();
        if (this.editDetallesAcumulados.length === 0) {
            Alerts.error("Sin ítems", "Debes tener al menos un ítem en la compra");
            return;
        }
        const resultado = await Alerts.confirmation("Guardar cambios", "¿Deseas guardar los cambios de esta compra?");
        if (!resultado.isConfirmed) return;

        try {
            const formData = new FormData(e.target);
            formData.append("accion", "actualizarCompra");
            formData.append("detalles", JSON.stringify(this.editDetallesAcumulados));
            const total = this.editDetallesAcumulados.reduce((acc, d) => acc + d.subTotal, 0);
            formData.set("totalCompra", total);

            const respuesta = await ComprasAPI.actualizarCompra(formData);
            if (respuesta.success) {
                Alerts.success("Actualizado", respuesta.message);
                this.modalEditar.hide();
                this.cargarCompras();
            } else {
                Alerts.error("Error", respuesta.message || "No se pudo actualizar la compra");
            }
        } catch (error) {
            Alerts.error("Error", "No se pudo actualizar la compra");
        }
    },

    async eliminarCompra(idCompra) {
        const resultado = await Alerts.confirmation("Eliminar compra", "¿Deseas eliminar esta compra y todos sus ítems? Esta acción no se puede deshacer.");
        if (!resultado.isConfirmed) return;

        try {
            const respuesta = await ComprasAPI.eliminarCompra(idCompra);
            if (respuesta.success) {
                Alerts.toasSuccess("Compra eliminada");
                this.cargarCompras();
            } else {
                Alerts.error("Error", respuesta.message || "No se pudo eliminar la compra");
            }
        } catch (error) {
            Alerts.error("Error", "No se pudo eliminar la compra");
        }
    },
};
