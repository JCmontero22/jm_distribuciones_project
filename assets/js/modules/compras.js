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

    poblarSelectProveedores(proveedores) {
        let listado = '<option value="">Seleccione proveedor</option>';
        proveedores.forEach((proveedor) => {
            console.log(proveedor.id_proveedor);
            
            listado += `<option value="${proveedor.id_proveedor}">${proveedor.nombre_proveedor}</option>`;
        });
        $("#selectProveedor").html(listado);
    },
};

/**
 * MÓDULO COMPRAS — Coordina API y View, maneja estado y lógica de negocio
 */
const comprasModule = {
    idCompra: null,
    detallesAcumulados: [],
    init() {
        this.bindEvents();
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
            ComprasView.poblarSelectProveedores(proveedores);
        } catch (error) {
            console.error("Error al cargar proveedores:", error);
        }
    },

    agregarDetalleAlAcumulador() {
        const idProducto = $("#selectProducto").val();
        const nombreProducto = $("#selectProducto option:selected").text();
        const cantidad = $("#cantidad").val();
        const costoUnitario = $("#costoUnitario").val();
        const subtotalFormato = $("#subtotal").val();

        if (!idProducto || !cantidad || !costoUnitario) {
            Alerts.error("Campos incompletos", "Por favor completa producto, cantidad y costo unitario");
            return;
        }

        const costoUnitarioLimpio = FormatUtils.obtenerValorLimpio($("#costoUnitario"));
        const subTotal = parseInt(costoUnitarioLimpio) * parseInt(cantidad);

        const detalle = {
            id: Date.now(),
            idProducto: idProducto,
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
            } else {
                Alerts.error("Error", respuesta.message || "No se pudieron registrar los detalles");
            }
        } catch (error) {
            Alerts.error("Error", "No se pudieron registrar los detalles");
            console.error(error);
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
                this.idCompra = respuesta.data.id_compra;
                console.log(this.idCompra);

                $("#registroDeCompra")[0].reset();
                $("#modalCompra").modal("hide");
            } else {
                Alerts.error("Error", respuesta.message || "No se pudo registrar la compra");
            }

        } catch (error) {
            Alerts.error("Error", "No se pudo registrar la compra");
            console.error(error);
        }
    }
};
