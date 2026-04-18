/**
 * CAPA API — Solo responsable de comunicarse con el servidor
 * Los métodos aquí devuelven datos del servidor sin modificar
 */
const RelojesAPI = {
    listarProductos(categoria = "Relojes") {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/productosAjax.php",
                method: "GET",
                data: { accion: "listadoRelojes", categoria: categoria },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                }
            });
        });
    },

    registrarProducto(formData) {
        return new Promise((resolve, reject) => {
            formData.append("accion", "registrarProducto");
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/productosAjax.php",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success(response) {
                    const res = JSON.parse(response);
                    resolve(res);
                },
                error(error) {
                    reject(error);
                }
            });
        });
    },

    registrarPresentaciones(formData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/productosAjax.php",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success(response) {
                    const res = JSON.parse(response);
                    resolve(res);
                },
                error(error) {
                    reject(error);
                }
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
                }
            });
        });
    },

    obtenerMarcas() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/catalogoAjax.php",
                method: "GET",
                data: { accion: "listadoMarcas" },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                }
            });
        });
    },

    obtenerGeneros() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/catalogoAjax.php",
                method: "GET",
                data: { accion: "listadoGeneros" },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                }
            });
        });
    }
};

/**
 * CAPA VIEW — Solo responsable de renderizar HTML y manipular el DOM
 * Recibe datos y los convierte en HTML, sin hacer lógica de negocio
 */
const RelojesView = {
    renderizarTarjetaProducto(producto) {
        return `
            <div class="card-inventory">
                <div class="card-inventory_img">
                    <img src="/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/assets/img/productos/${producto.img_presentacion}" alt="${producto.nombre_producto}" class="product-image">
                </div>
                <div class="card-inventory_infoProducto">
                    <h3 class="card-inventory_name">${producto.nombre_producto}</h3>
                    <p>
                        <span>Valor de compra:</span>
                        <span class="card-inventory_cost">$ ${producto.precio_costo}</span> |
                        <span>Valor de venta:</span>
                        <span class="card-inventory_sale">$ ${producto.precio_venta}</span>
                    </p>
                </div>
                <div class="card-inventory_stock">
                    <span class="stock-label">Stock:</span>
                    <span class="stock-value">${producto.stock_producto}</span>
                </div>
                <div class="card-inventory_op">
                    <button class="btn btn-success btn-add-presentacion" data-id="${producto.id_producto}" type="button">
                        <i class="fa-solid fa-file-circle-plus"></i>
                    </button>
                    <button class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-danger"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>
        `;
    },

    mostrarInventario(productos) {
        const contenedor = $(".content-inventory");
        contenedor.empty();
        productos.forEach(producto => {
            contenedor.append(this.renderizarTarjetaProducto(producto));
        });
    },

    poblarSelect(selectId, items, valueKey, textKey) {
        const select = $(selectId);
        items.forEach(item => {
            select.append(new Option(item[textKey], item[valueKey]));
        });
    },

    renderizarFilaPresentacion(presentacion) {
        return `
            <tr>
                <td>${presentacion.nombrePresentacion}</td>
                <td>${presentacion.codigoPresentacion}</td>
                <td>$${presentacion.precioCompraPresentacion.toLocaleString('es-CO')}</td>
                <td>$${presentacion.precioVentaPresentacion.toLocaleString('es-CO')}</td>
                <td>
                    <span class="badge ${presentacion.tipoProducto === '1' ? 'bg-info' : 'bg-warning'}">
                        ${presentacion.tipoProducto === '1' ? 'REVENTA' : 'PRODUCCIÓN'}
                    </span>
                </td>
                <td>
                    ${presentacion.imagenPresentacion ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-times text-danger"></i>'}
                </td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="RelojesModule.eliminarPresentacion(${presentacion.id})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    },

    mostrarPresentacionesEnTabla(presentaciones) {
        let tabla = $("#tablaPresentaciones");

        if (tabla.length === 0) {
            $("#formRegistroPresentacionProducto").after(`
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Presentaciones agregadas</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Código</th>
                                        <th>Precio Compra</th>
                                        <th>Precio Venta</th>
                                        <th>Tipo</th>
                                        <th>Imagen</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaPresentaciones"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `);
            tabla = $("#tablaPresentaciones");
        }

        if (presentaciones.length === 0) {
            tabla.html('<tr><td colspan="7" class="text-center text-muted">No hay presentaciones agregadas</td></tr>');
            return;
        }

        tabla.html(presentaciones.map(p => this.renderizarFilaPresentacion(p)).join(''));
    },

    limpiarFormulario(formId) {
        const form = $(formId);
        form[0].reset();
        form.find("input, textarea").val('');
        form.find("select").prop('selectedIndex', 0);
        form.find("input[type='file']").val('');
        form.find(".is-invalid, .is-valid").removeClass("is-invalid is-valid");
    },

    limpiarInventario() {
        $(".content-inventory").empty();
    }
};

/**
 * MÓDULO RELOJES — Coordina API y View, maneja estado y lógica de negocio
 */
const RelojesModule = {
    idRegistro: null,
    idProducto: null,
    presentacionesAcumuladas: [],
    modalProducto: null,
    modalPresentacion: null,

    init() {
        this.modalProducto = new bootstrap.Modal(document.getElementById('modalRegistroProducto'));
        this.modalPresentacion = new bootstrap.Modal(document.getElementById('modalRegistroPresentacion'));

        RelojesView.limpiarFormulario("#formRegistroProducto");
        RelojesView.limpiarFormulario("#formRegistroPresentacionProducto");

        this.bindEvents();
        this.cargarDatos();
    },

    bindEvents() {
        $("#formRegistroProducto").on("submit", (e) => this.registrarProducto(e));
        $("#formRegistroPresentacionProducto").on("submit", (e) => this.registrarTodasPresentaciones(e));

        $(document).on("click", "#btnAgregarStock", () => {
            this.agregarPresentacionAlAcumulador();
        });

        $(document).on("click", ".btn-add-presentacion", (e) => {
            const id = $(e.currentTarget).data("id");
            this.idProducto = id;
            this.modalPresentacion.show();
        });

        $('#modalRegistroPresentacion').on('hidden.bs.modal', () => {
            RelojesView.limpiarFormulario("#formRegistroPresentacionProducto");
            this.idRegistro = null;
        });

        $('#modalRegistroProducto').on('hidden.bs.modal', () => {
            RelojesView.limpiarFormulario("#formRegistroProducto");
            this.idRegistro = null;
        });
    },

    async cargarDatos() {
        try {
            await Promise.all([
                this.cargarProductos(),
                this.cargarCategorias(),
                this.cargarMarcas(),
                this.cargarGeneros()
            ]);
        } catch (error) {
            Alerts.error("Error", "No se pudieron cargar los datos");
            console.error(error);
        }
    },

    async cargarProductos() {
        try {
            const productos = await RelojesAPI.listarProductos("Relojes");
            RelojesView.mostrarInventario(productos);
        } catch (error) {
            console.error("Error al cargar productos:", error);
        }
    },

    async cargarCategorias() {
        try {
            const categorias = await RelojesAPI.obtenerCategorias();
            RelojesView.poblarSelect("#categoriaProducto", categorias, 'id_categoria', 'nombre_categoria');
        } catch (error) {
            console.error("Error al cargar categorías:", error);
        }
    },

    async cargarMarcas() {
        try {
            const marcas = await RelojesAPI.obtenerMarcas();
            RelojesView.poblarSelect("#marcaProducto", marcas, 'id_marca', 'nombre_marca');
        } catch (error) {
            console.error("Error al cargar marcas:", error);
        }
    },

    async cargarGeneros() {
        try {
            const generos = await RelojesAPI.obtenerGeneros();
            RelojesView.poblarSelect("#generoProducto", generos, 'id_genero', 'nombre_genero');
        } catch (error) {
            console.error("Error al cargar géneros:", error);
        }
    },

    async registrarProducto(e) {
        e.preventDefault();
        const resultado = await Alerts.confirmation(
            "Registrar este producto?",
            "¿Estás seguro de que deseas registrar este producto?"
        );

        if (!resultado.isConfirmed) return;

        try {
            const formData = new FormData(e.target);
            const respuesta = await RelojesAPI.registrarProducto(formData);

            if (respuesta.success) {
                e.target.reset();
                this.modalProducto.hide();
                await this.cargarProductos();
                Alerts.toasSuccess(respuesta.message);
                this.idRegistro = respuesta.data;
            } else {
                Alerts.error("Error al registrar producto", respuesta.message);
            }
        } catch (error) {
            Alerts.error("Error", "Error al registrar el producto");
            console.error(error);
        }
    },

    agregarPresentacionAlAcumulador() {
        const nombre = $("#nombrePresentacion").val();
        const codigo = $("#codigoPresentacion").val();
        const precioCompra = $("#precioCompraPresentacion").val();
        const precioVenta = $("#precioVentaPresentacion").val();
        const tipo = $("#tipoProducto").val();
        const imagen = $("#imagenPresentacion")[0].files[0];

        if (!nombre || !codigo || !precioCompra || !precioVenta || !tipo) {
            Alerts.error("Campos incompletos", "Por favor completa todos los campos requeridos");
            return;
        }

        const presentacion = {
            id: Date.now(),
            nombrePresentacion: nombre,
            codigoPresentacion: codigo,
            precioCompraPresentacion: parseFloat(precioCompra),
            precioVentaPresentacion: parseFloat(precioVenta),
            tipoProducto: tipo,
            imagenPresentacion: imagen,
            idProducto: this.idProducto
        };

        this.presentacionesAcumuladas.push(presentacion);
        RelojesView.mostrarPresentacionesEnTabla(this.presentacionesAcumuladas);
        RelojesView.limpiarFormulario("#formRegistroPresentacionProducto");
        Alerts.toasSuccess("Presentación agregada correctamente");
    },

    eliminarPresentacion(id) {
        this.presentacionesAcumuladas = this.presentacionesAcumuladas.filter(p => p.id !== id);
        RelojesView.mostrarPresentacionesEnTabla(this.presentacionesAcumuladas);
        Alerts.toasSuccess("Presentación eliminada");
    },

    async registrarTodasPresentaciones(e) {
        e.preventDefault();

        if (this.presentacionesAcumuladas.length === 0) {
            Alerts.error("Sin presentaciones", "Debes agregar al menos una presentación");
            return;
        }

        const resultado = await Alerts.confirmation(
            "Registrar presentaciones?",
            `¿Estás seguro de que deseas registrar ${this.presentacionesAcumuladas.length} presentación(es)?`
        );

        if (!resultado.isConfirmed) return;

        await this.enviarPresentacionesAlServidor();
    },

    async enviarPresentacionesAlServidor() {
        try {
            const presentaciones = this.presentacionesAcumuladas.map(p => ({
                nombrePresentacion: p.nombrePresentacion,
                codigoPresentacion: p.codigoPresentacion,
                precioCompraPresentacion: p.precioCompraPresentacion,
                precioVentaPresentacion: p.precioVentaPresentacion,
                tipoProducto: p.tipoProducto,
                idProducto: p.idProducto
            }));

            const formData = new FormData();
            formData.append("accion", "registrarPresentaciones");
            formData.append("presentaciones", JSON.stringify(presentaciones));

            this.presentacionesAcumuladas.forEach((p, index) => {
                if (p.imagenPresentacion) {
                    formData.append(`imagen_${index}`, p.imagenPresentacion);
                }
            });

            const respuesta = await RelojesAPI.registrarPresentaciones(formData);

            if (respuesta.success) {
                this.modalPresentacion.hide();
                this.limpiarAcumulador();
                await this.cargarProductos();
                Alerts.toasSuccess(respuesta.message);
            } else {
                Alerts.error("Error al registrar presentaciones", respuesta.message);
            }
        } catch (error) {
            Alerts.error("Error", "Error en la conexión con el servidor");
            console.error(error);
        }
    },

    limpiarAcumulador() {
        this.presentacionesAcumuladas = [];
        const tabla = $("#tablaPresentaciones");
        if (tabla.length > 0) {
            tabla.html('<tr><td colspan="7" class="text-center text-muted">No hay presentaciones agregadas</td></tr>');
        }
        RelojesView.limpiarFormulario("#formRegistroPresentacionProducto");
    }
};
