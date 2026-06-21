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

    async obtenerDescuentoID(id) {
        const response = await this.client.get({ accion: "obtenerDescuentoPorID", idDescuento: id });
        return response.data || null;
    },

    async actualizarDescuentos(formData) {
        formData.set("accion", "actualizarDescuento");
        return await this.client.post(formData);
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
    },

    async eliminarDescuento(id) {
        return await this.client.post({ accion: "eliminarDescuento", idDescuento: id });
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
                        return `<button class="btn btn-sm btn-primary btn_editar" data-id="${row.id_descuento}">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-sm btn-warning btn_quitar_descuento" data-id="${row.id_descuento}" title="Quitar descuento de productos">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn_eliminar" data-id="${row.id_descuento}" title="Eliminar descuento">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                                `
                                ;
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
    },

    cargarDescuentoEnModal(descuento) {
        $("#id_descuento").val(descuento.id_descuento);
        $("#nombreDescuento").val(descuento.nombre_descuento);
        $("#porcentajeDescuento").val(descuento.porcentaje_descuento);
        $("#fechaInicio").val(descuento.fecha_inicio.split(" ")[0]);
        $("#fechaFin").val(descuento.fecha_fin.split(" ")[0]);
        $("#btnRegistrarDetalles").text("Actualizar Descuento");
        if (DescuentosModule.modalDescuento) DescuentosModule.modalDescuento.show();
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
        $("#registroDeDescuento").on("submit", (e) => {
            const id = $("#id_descuento").val();
            if (id) {
                this.actualizarDescuento(id, e);
            } else {
                this.registrarDescuento(e)
            }
        });

        // Reset modal
        $("#modalRegistrarDescuento").on("hidden.bs.modal", () => {
            AppUI.resetForm("#registroDeDescuento");
            $("#id_descuento").val("");
            $("#btnRegistrarDetalles").text("Registrar Descuento");
        });

        // Quitar descuento de las presentaciones que lo tienen aplicado
        $("#tablaDescuentos").on("click", ".btn_quitar_descuento", (e) => {
            const id = $(e.currentTarget).data("id");
            this.quitarDescuentoDeProductos(id);
        });

        $("#tablaDescuentos").on("click", ".btn_eliminar", (e) => {
            const id = $(e.currentTarget).data("id");
            this.eliminarDescuento(id);
        });

        $("#tablaDescuentos").on("click", ".btn_editar", (e) => {
            const id = $(e.currentTarget).data("id");
            this.obtenerDescuentoID(id)
        });

        // Aplicar descuentos
        $("#formAplicarProductos").on("submit", (e) => this.aplicarAProductos(e));
        $("#formAplicarMarca").on("submit", (e) => this.aplicarAMarca(e));
        $("#formAplicarProductoGenero").on("submit", (e) => this.aplicarAProductoPorGenero(e));
        $("#formAplicarGenero").on("submit", (e) => this.aplicarAGenero(e));
        $("#formAplicarTodos").on("submit", (e) => this.aplicarATodos(e));

        // Resetear buscador cuando se abre el tab
        $(document).on("shown.bs.tab", '[data-bs-target="#formProductos"]', () => {
            $("#buscarProductosDescuento").val("").trigger("input");
        });
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
        $("#totalProductosDescuento").text(this.productos.length);
        $("#countProductosBuscados").text(this.productos.length);

        if (!this.productos.length) {
            $productosCheckbox.html('<p class="descuentos-empty mb-0">No hay productos activos para aplicar descuentos.</p>');
            return;
        }

        this.productos.forEach(prod => {
            const codigo = (prod.codigo_producto || '').toLowerCase();
            const nombre = (prod.nombre_producto || '').toLowerCase();
            const div = `
                <div class="form-check producto-descuento-item" data-producto-id="${prod.id_producto}" data-producto-nombre="${nombre}" data-producto-codigo="${codigo}">
                    <input class="form-check-input" type="checkbox" value="${prod.id_producto}" id="check_${prod.id_producto}">
                    <label class="form-check-label" for="check_${prod.id_producto}">
                        ${prod.nombre_producto}
                    </label>
                </div>
            `;
            $productosCheckbox.append(div);
        });

        this.initBuscador();
    },

    initBuscador() {
        const self = this;
        const $input = $("#buscarProductosDescuento");

        if ($input.length === 0) {
            console.error("❌ No se encontró #buscarProductosDescuento");
            return;
        }

        $input.off("input").on("input", function() {
            const query = $(this).val().toLowerCase().trim();
            self.filtrarProductos(query);
        });

        console.log("✅ Buscador inicializado para", this.productos.length, "productos");
    },

    filtrarProductos(query) {
        const $items = $(".producto-descuento-item");
        let visible = 0;

        if (!query) {
            $items.show();
            visible = this.productos.length;
        } else {
            $items.each(function() {
                const $item = $(this);
                const nombre = ($item.attr("data-producto-nombre") || "").toString();
                const codigo = ($item.attr("data-producto-codigo") || "").toString();
                const coincide = nombre.includes(query) || codigo.includes(query);

                if (coincide) {
                    $item.show();
                    visible++;
                } else {
                    $item.hide();
                }
            });
        }

        $("#countProductosBuscados").text(visible);
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

    async actualizarDescuento(id, e) {
         e.preventDefault();
        const formData = new FormData(e.target);
        formData.set("id_descuento", id);
        formData.set("accion", "actualizarDescuento");
        const nombreDescuento = formData.get("nombreDescuento");

        // Validación
        if (!nombreDescuento || !formData.get("porcentajeDescuento") ||
            !formData.get("fechaInicio") || !formData.get("fechaFin")) {
            Alerts.warning("Campos incompletos", "Por favor complete todos los campos.");
            return;
        }

        // Confirmación
        const confirmacion = await Alerts.confirmation(
            "Actualizar descuento?",
            `¿Estás seguro de actualizar el descuento "${nombreDescuento}"?`
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await DescuentosAPI.actualizarDescuentos(formData);

            if (response.success) {
                Alerts.success("Descuento Actualizado", response.message || "El descuento se actualizó exitosamente");
                if (this.modalDescuento) this.modalDescuento.hide();
                await this.cargarDatos();
            } else {
                Alerts.error("Error", response.message || "No se pudo actualizar el descuento");
            }
        } catch (error) {
            Logger.error("Error actualizando descuento", error);
            Alerts.error("Error", "No se pudo actualizar el descuento");
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

    async quitarDescuentoDeProductos(id) {
        const confirmacion = await Alerts.confirmation(
            "¿Quitar descuento?",
            "Esta acción quitará este descuento de todos los productos que lo tengan aplicado."
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await DescuentosAPI.removerDescuento({
                idDescuento: id
            });

            if (response.success) {
                Alerts.success("Éxito", response.message || "Descuento quitado de los productos");
                await this.cargarDatos();
            } else {
                Alerts.error("Error", response.message);
            }
        } catch (error) {
            Logger.error("Error removiendo descuento", error);
            Alerts.error("Error", "No se pudo remover el descuento");
        }
    },

    async eliminarDescuento(id) {
        const confirmacion = await Alerts.confirmation(
            "¿Eliminar descuento?",
            "Esta acción eliminará el descuento y también lo quitará de los productos que lo tengan aplicado."
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await DescuentosAPI.eliminarDescuento(id);

            if (response.success) {
                Alerts.success("Éxito", response.message || "Descuento eliminado");
                await this.cargarDatos();
            } else {
                Alerts.error("Error", response.message || "No se pudo eliminar el descuento");
            }
        } catch (error) {
            Logger.error("Error eliminando descuento", error);
            Alerts.error("Error", "No se pudo eliminar el descuento");
        }
    },

    async obtenerDescuentoID(id) {
        try {
            const descuento = await DescuentosAPI.obtenerDescuentoID(id);
            if (!descuento) {
                Alerts.error("Error", "Descuento no encontrado");
                return;
            }
            DescuentosView.cargarDescuentoEnModal(descuento);
            
        } catch (error) {
            Logger.error("Error obteniendo descuento por ID", error);
            Alerts.error("Error", "No se pudo obtener el descuento");
        }
    }
};

window.DescuentosModule = DescuentosModule;
