const DescuentosAPI = {
    client: new SimpleAPI(CONFIG.AJAX.DESCUENTOS),

    async registrarDescuento(formData) {
        formData.set("accion", "registrarDescuento");
        return await this.client.post(formData);
    },

    async obtenerDescuentos() {
        const response = await this.client.get({ accion: "obtenerDescuentos" });
        return response.data || [];
    },

    async aplicarAProductos(payload) {
        payload.accion = "aplicarAProductosEspecificos";
        return await this.client.post(payload);
    },

    async aplicarAMarca(payload) {
        payload.accion = "aplicarAMarca";
        return await this.client.post(payload);
    },

    async aplicarAProductoPorGenero(payload) {
        payload.accion = "aplicarAProductoPorGenero";
        return await this.client.post(payload);
    },

    async aplicarAGenero(payload) {
        payload.accion = "aplicarAGenero";
        return await this.client.post(payload);
    },

    async aplicarATodos(payload) {
        payload.accion = "aplicarATodos";
        return await this.client.post(payload);
    },

    async removerDescuento(payload) {
        payload.accion = "removerDescuento";
        return await this.client.post(payload);
    }
};

const CatalogoAPI = {
    client: new SimpleAPI(CONFIG.AJAX.CATALOGO),
    productosClient: new SimpleAPI(CONFIG.AJAX.PRODUCTOS),

    async obtenerProductos() {
        const response = await this.productosClient.get({ accion: "listarProductosActivos" });
        return response.data || [];
    },

    async obtenerMarcas() {
        const response = await this.client.get({ accion: "listadoMarcas" });
        return response.data || [];
    },

    async obtenerGeneros() {
        const response = await this.client.get({ accion: "listadoGeneros" });
        return response.data || [];
    }
};

const DescuentosView = {
    renderTablaDescuentos(descuentos) {
        AppUI.initDataTable("#tablaDescuentos", {
            data: descuentos || [],
            columns: [
                { data: "id_descuento" },
                { data: "nombre_descuento" },
                { data: "porcentaje_descuento", render: (data) => `${data}%` },
                { data: "fecha_inicio", render: (data) => this.formatDate(data) },
                { data: "fecha_fin", render: (data) => this.formatDate(data) },
                {
                    data: null,
                    render: (data, _type, row) => {
                        return `<button class="btn btn-sm btn-danger btn_eliminar" data-id="${row.id_descuento}">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>`;
                    }
                }
            ],
            order: [[0, "desc"]]
        });
    },

    llenarSelectDescuentos() {
        const selectores = document.querySelectorAll(".selectDescuento");
        selectores.forEach(select => {
            select.innerHTML = '<option value="">-- Selecciona un descuento --</option>';
        });
    },

    formatDate(date) {
        return new Date(date).toLocaleDateString("es-ES");
    }
};

const DescuentosModule = {
    modalDescuento: null,
    descuentos: [],
    productos: [],
    marcas: [],
    generos: [],

    init() {
        this.modalDescuento = AppUI.createModal("modalRegistrarDescuento");
        this.bindEvents();
        this.cargarDatos();
    },

    bindEvents() {
        // Registro
        $("#registroDeDescuento").on("submit", (e) => this.registrarDescuento(e));

        // Reset modal
        $("#modalRegistrarDescuento").on("hidden.bs.modal", () => {
            AppUI.resetForm("#registroDeDescuento");
            $("#id_descuento").val("");
            $("#btnRegistrarDetalles").text("Registrar Descuento");
        });

        // Eliminar
        $(document).on("click", ".btn_eliminar", (e) => {
            const id = $(e.currentTarget).data("id");
            this.removerDescuento(id);
        });

        // Aplicar descuentos
        $("#formAplicarProductos").on("submit", (e) => this.aplicarAProductos(e));
        $("#formAplicarMarca").on("submit", (e) => this.aplicarAMarca(e));
        $("#formAplicarProductoGenero").on("submit", (e) => this.aplicarAProductoPorGenero(e));
        $("#formAplicarGenero").on("submit", (e) => this.aplicarAGenero(e));
        $("#formAplicarTodos").on("submit", (e) => this.aplicarATodos(e));
    },

    async cargarDatos() {
        try {
            const [descuentosResp, productosResp, marcasResp, generosResp] = await Promise.allSettled([
                DescuentosAPI.obtenerDescuentos(),
                CatalogoAPI.obtenerProductos(),
                CatalogoAPI.obtenerMarcas(),
                CatalogoAPI.obtenerGeneros()
            ]);

            this.descuentos = descuentosResp.status === "fulfilled" ? descuentosResp.value : [];
            this.productos = productosResp.status === "fulfilled" ? productosResp.value : [];
            this.marcas = marcasResp.status === "fulfilled" ? marcasResp.value : [];
            this.generos = generosResp.status === "fulfilled" ? generosResp.value : [];

            if (descuentosResp.status === "rejected") Logger.error("Error cargando descuentos", descuentosResp.reason);
            if (productosResp.status === "rejected") Logger.error("Error cargando productos", productosResp.reason);
            if (marcasResp.status === "rejected") Logger.error("Error cargando marcas", marcasResp.reason);
            if (generosResp.status === "rejected") Logger.error("Error cargando géneros", generosResp.reason);

            DescuentosView.renderTablaDescuentos(this.descuentos);
            this.llenarSelectsDescuentos();
            this.llenarProductos();
            this.llenarMarcas();
            this.llenarGeneros();

            if ([descuentosResp, productosResp, marcasResp, generosResp].some(resp => resp.status === "rejected")) {
                Alerts.warning("Datos incompletos", "Algunos catálogos no se pudieron cargar. Revisa la consola para más detalle.");
            }
        } catch (error) {
            Logger.error("Error cargando datos descuentos", error);
            Alerts.error("Error", "No se pudieron cargar los datos");
        }
    },

    llenarSelectsDescuentos() {
        $(".selectDescuento").each((idx, select) => {
            const $select = $(select);
            const value = $select.val();
            $select.html('<option value="">-- Selecciona un descuento --</option>');

            this.descuentos.forEach(desc => {
                const option = `<option value="${desc.id_descuento}">${desc.nombre_descuento} (${desc.porcentaje_descuento}%)</option>`;
                $select.append(option);
            });

            if (value) $select.val(value);
        });
    },

    llenarProductos() {
        const $selectProductoGenero = $("#selectProductoGenero");
        const $productosCheckbox = $("#productosCheckbox");

        // Select para "Género de Producto"
        $selectProductoGenero.html('<option value="">-- Selecciona un producto --</option>');
        this.productos.forEach(prod => {
            $selectProductoGenero.append(
                `<option value="${prod.id_producto}">${prod.nombre_producto}</option>`
            );
        });

        // Checkboxes para "Productos Específicos"
        $productosCheckbox.html("");
        if (!this.productos.length) {
            $productosCheckbox.html('<p class="descuentos-empty mb-0">No hay productos activos para aplicar descuentos.</p>');
            return;
        }

        this.productos.forEach(prod => {
            const div = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${prod.id_producto}" id="check_${prod.id_producto}">
                    <label class="form-check-label" for="check_${prod.id_producto}">
                        ${prod.nombre_producto}
                    </label>
                </div>
            `;
            $productosCheckbox.append(div);
        });
    },

    llenarMarcas() {
        const $selectMarca = $("#selectMarca");
        $selectMarca.html('<option value="">-- Selecciona una marca --</option>');
        if (!this.marcas.length) {
            $selectMarca.append('<option value="" disabled>No hay marcas registradas</option>');
            return;
        }

        this.marcas.forEach(marca => {
            $selectMarca.append(
                `<option value="${marca.id_marca}">${marca.nombre_marca}</option>`
            );
        });
    },

    llenarGeneros() {
        const $selectGenero = $("#selectGenero");
        const $selectGeneroProducto = $("#selectGeneroProducto");

        [$selectGenero, $selectGeneroProducto].forEach($select => {
            $select.html('<option value="">-- Selecciona un género --</option>');
            if (!this.generos.length) {
                $select.append('<option value="" disabled>No hay géneros registrados</option>');
                return;
            }

            this.generos.forEach(genero => {
                $select.append(
                    `<option value="${genero.id_genero}">${genero.nombre_genero}</option>`
                );
            });
        });
    },

    async registrarDescuento(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const nombreDescuento = formData.get("nombreDescuento");

        // Validación
        if (!nombreDescuento || !formData.get("porcentajeDescuento") ||
            !formData.get("fechaInicio") || !formData.get("fechaFin")) {
            Alerts.warning("Campos incompletos", "Por favor complete todos los campos.");
            return;
        }

        // Confirmación
        const confirmacion = await Alerts.confirmation(
            "Registrar descuento?",
            `¿Estás seguro de registrar el descuento "${nombreDescuento}"?`
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await DescuentosAPI.registrarDescuento(formData);

            if (response.success) {
                Alerts.success("Descuento Registrado", response.message || "El descuento se registró exitosamente");
                if (this.modalDescuento) this.modalDescuento.hide();
                await this.cargarDatos();
            } else {
                Alerts.error("Error", response.message || "No se pudo registrar el descuento");
            }
        } catch (error) {
            Logger.error("Error registrando descuento", error);
            Alerts.error("Error", "No se pudo registrar el descuento");
        }
    },

    async aplicarAProductos(e) {
        e.preventDefault();
        const idDescuento = $("#selectDescuentoProductos").val();
        const idsProductos = [];

        $("#productosCheckbox input:checked").each(function() {
            idsProductos.push(parseInt($(this).val()));
        });

        if (!idDescuento) {
            Alerts.warning("Selecciona", "Selecciona un descuento");
            return;
        }

        if (idsProductos.length === 0) {
            Alerts.warning("Selecciona", "Selecciona al menos un producto");
            return;
        }

        try {
            const response = await DescuentosAPI.aplicarAProductos({
                idDescuento: parseInt(idDescuento),
                idsProductos: JSON.stringify(idsProductos)
            });

            if (response.success) {
                Alerts.success("Éxito", `Descuento aplicado a ${idsProductos.length} productos`);
                document.getElementById("formAplicarProductos").reset();
            } else {
                Alerts.error("Error", response.message);
            }
        } catch (error) {
            Logger.error("Error aplicando descuento", error);
            Alerts.error("Error", "No se pudo aplicar el descuento");
        }
    },

    async aplicarAMarca(e) {
        e.preventDefault();
        const idDescuento = $("#selectDescuentoMarca").val();
        const idMarca = $("#selectMarca").val();

        if (!idDescuento || !idMarca) {
            Alerts.warning("Selecciona", "Selecciona descuento y marca");
            return;
        }

        try {
            const response = await DescuentosAPI.aplicarAMarca({
                idDescuento: parseInt(idDescuento),
                idMarca: parseInt(idMarca)
            });

            if (response.success) {
                Alerts.success("Éxito", "Descuento aplicado a la marca");
                document.getElementById("formAplicarMarca").reset();
            } else {
                Alerts.error("Error", response.message);
            }
        } catch (error) {
            Logger.error("Error aplicando descuento", error);
            Alerts.error("Error", "No se pudo aplicar el descuento");
        }
    },

    async aplicarAProductoPorGenero(e) {
        e.preventDefault();
        const idDescuento = $("#selectDescuentoProductoGenero").val();
        const idProducto = $("#selectProductoGenero").val();
        const idGenero = $("#selectGeneroProducto").val();

        if (!idDescuento || !idProducto || !idGenero) {
            Alerts.warning("Selecciona", "Completa todos los campos");
            return;
        }

        try {
            const response = await DescuentosAPI.aplicarAProductoPorGenero({
                idDescuento: parseInt(idDescuento),
                idProducto: parseInt(idProducto),
                idGenero: parseInt(idGenero)
            });

            if (response.success) {
                Alerts.success("Éxito", "Descuento aplicado al género del producto");
                document.getElementById("formAplicarProductoGenero").reset();
            } else {
                Alerts.error("Error", response.message);
            }
        } catch (error) {
            Logger.error("Error aplicando descuento", error);
            Alerts.error("Error", "No se pudo aplicar el descuento");
        }
    },

    async aplicarAGenero(e) {
        e.preventDefault();
        const idDescuento = $("#selectDescuentoGenero").val();
        const idGenero = $("#selectGenero").val();

        if (!idDescuento || !idGenero) {
            Alerts.warning("Selecciona", "Selecciona descuento y género");
            return;
        }

        try {
            const response = await DescuentosAPI.aplicarAGenero({
                idDescuento: parseInt(idDescuento),
                idGenero: parseInt(idGenero)
            });

            if (response.success) {
                Alerts.success("Éxito", "Descuento aplicado a género");
                document.getElementById("formAplicarGenero").reset();
            } else {
                Alerts.error("Error", response.message);
            }
        } catch (error) {
            Logger.error("Error aplicando descuento", error);
            Alerts.error("Error", "No se pudo aplicar el descuento");
        }
    },

    async aplicarATodos(e) {
        e.preventDefault();
        const idDescuento = $("#selectDescuentoTodos").val();

        if (!idDescuento) {
            Alerts.warning("Selecciona", "Selecciona un descuento");
            return;
        }

        const confirmacion = await Alerts.confirmation(
            "⚠️ Confirmación",
            "Esto aplicará el descuento a TODOS los productos. ¿Continuar?"
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await DescuentosAPI.aplicarATodos({
                idDescuento: parseInt(idDescuento)
            });

            if (response.success) {
                Alerts.success("Éxito", "Descuento aplicado a todos los productos");
                document.getElementById("formAplicarTodos").reset();
            } else {
                Alerts.error("Error", response.message);
            }
        } catch (error) {
            Logger.error("Error aplicando descuento", error);
            Alerts.error("Error", "No se pudo aplicar el descuento");
        }
    },

    async removerDescuento(id) {
        const confirmacion = await Alerts.confirmation(
            "¿Remover descuento?",
            "Esta acción removará el descuento de todas las presentaciones"
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await DescuentosAPI.removerDescuento({
                idDescuento: id
            });

            if (response.success) {
                Alerts.success("Éxito", "Descuento removido");
                await this.cargarDatos();
            } else {
                Alerts.error("Error", response.message);
            }
        } catch (error) {
            Logger.error("Error removiendo descuento", error);
            Alerts.error("Error", "No se pudo remover el descuento");
        }
    }
};

window.DescuentosModule = DescuentosModule;
