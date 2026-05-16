/**
 * FACTORY FUNCTION - Genera módulos de inventario parametrizables
 *
 * Uso:
 * const EsenciasModule = crearModuloInventario({
 *   categoria: "Esencias",
 *   contenedorSelector: ".content-inventory-esencias",
 *   cantidadId: "#cantidad-esencias",
 *   accionListar: "listarProductos",
 *   tieneSedes: false
 * });
 */
function crearModuloInventario(config) {
    const BASE_URL = "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project";

    /**
     * CAPA API — Solo responsable de comunicarse con el servidor
     */
    const API = {
        listarProductos(categoria = null) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/productosAjax.php`,
                    method: "GET",
                    data: {
                        accion: config.accionListar,
                        categoria: categoria || config.categoria
                    },
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
                    url: `${BASE_URL}/ajax/productosAjax.php`,
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
                fetch(`${BASE_URL}/ajax/productosAjax.php`, {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(text => {
                    try {
                        const startIdx = text.indexOf('{');
                        const endIdx = text.lastIndexOf('}');

                        if (startIdx !== -1 && endIdx !== -1 && startIdx < endIdx) {
                            const jsonStr = text.substring(startIdx, endIdx + 1);
                            const data = JSON.parse(jsonStr);
                            resolve(data);
                        } else {
                            reject(new Error("Respuesta JSON inválida"));
                        }
                    } catch (e) {
                        reject(e);
                    }
                })
                .catch(error => reject(error));
            });
        },

        obtenerCategorias() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/catalogoAjax.php`,
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
                    url: `${BASE_URL}/ajax/catalogoAjax.php`,
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
                    url: `${BASE_URL}/ajax/catalogoAjax.php`,
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
        },

        obtenerTiposProductos() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/catalogoAjax.php`,
                    method: "GET",
                    data: { accion: "listadoTiposProductos" },
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

        obtenerSedes() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/catalogoAjax.php`,
                    method: "GET",
                    data: { accion: "listadoSedes" },
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
     */
    const View = {
        renderizarTarjetaProducto(producto) {
            return `
                <div class="card-inventory">
                    <div class="card-inventory_img">
                        <img src="${BASE_URL}/assets/img/productos/${producto.img_presentacion}" alt="${producto.nombre_producto}" class="product-image">
                    </div>
                    <div class="card-inventory_infoProducto">
                        <h3 class="card-inventory_name">${producto.nombre_producto}</h3>
                        <p>
                            <span>Valor de compra:</span>
                            <span class="card-inventory_cost">$ ${Module.separaMiles(producto.precio_compra_presentacion)}</span>
                        </p>
                        <p>
                            <span>Valor de venta:</span>
                            <span class="card-inventory_sale">$ ${Module.separaMiles(producto.precio_venta_presentacion)}</span>
                        </p>

                        <p class="card-inventory_description">
                            ${producto.descripcion_producto}
                        </p>
                    </div>
                    <div class="card-inventory_stock">
                        <p class="stock-label">Stock: ${producto.stock_actual || 0}</p>
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
            const contenedor = $(config.contenedorSelector);
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
                        <button class="btn btn-sm btn-danger btn-eliminar-presentacion" data-id="${presentacion.id}">
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
            $(config.contenedorSelector).empty();
        }
    };

    /**
     * MÓDULO PRINCIPAL — Coordina API y View, maneja estado y lógica de negocio
     */
    const Module = {
        ...config,
        idRegistro: null,
        idProducto: null,
        presentacionesAcumuladas: [],
        modalProducto: null,
        modalPresentacion: null,
        todosLosProductos: [],
        productosMostrados: 0,
        productosPorCarga: 20,
        cargando: false,

        init() {
            this.modalProducto = new bootstrap.Modal(document.getElementById('modalRegistroProducto'));
            this.modalPresentacion = new bootstrap.Modal(document.getElementById('modalRegistroPresentacion'));

            View.limpiarFormulario("#formRegistroProducto");
            View.limpiarFormulario("#formRegistroPresentacionProducto");

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
                View.limpiarFormulario("#formRegistroPresentacionProducto");
                this.idRegistro = null;
            });

            $('#modalRegistroProducto').on('hidden.bs.modal', () => {
                View.limpiarFormulario("#formRegistroProducto");
            });

            $("[data-format-miles]").on("input", (e) => this.formatearMiles(e));

            $("#buscar").on("input", (e) => {
                const query = e.target.value.trim();
                this.buscadorProductos(query);
            });

            $(document).on("click", ".btn-eliminar-presentacion", (e) => {
                const id = $(e.currentTarget).data("id");
                this.eliminarPresentacion(id);
            });

            $(window).on('scroll', () => {
                if (this.cargando) return;
                const bottom = $(window).scrollTop() + $(window).height() >= $(document).height() - 100;
                if (bottom && this.productosMostrados < this.todosLosProductos.length && !$("#buscar").val()) {
                    this.cargarMasProductos();
                }
            });
        },

        formatearMiles(e) {
            const $input = $(e.target);
            let valor = $input.val().replace(/\D/g, "");

            if (valor) {
                $input.data("valor-limpio", valor);
                $input.val(this.separaMiles(valor));
            }
        },

        obtenerValorLimpio($input) {
            return $input.data("valor-limpio") || $input.val().replace(/\D/g, "");
        },

        async cargarDatos() {
            try {
                const promises = [
                    this.cargarProductos(),
                    this.cargarCategorias(),
                    this.cargarMarcas(),
                    this.cargarGeneros(),
                    this.cargarTiposProductos(),
                ];
                if (config.tieneSedes) promises.push(this.cargarSedes());

                await Promise.all(promises);
            } catch (error) {
                Alerts.error("Error", "No se pudieron cargar los datos");
                console.error(error);
            }
        },

        async cargarProductos() {
            try {
                const productos = await API.listarProductos();
                this.todosLosProductos = productos;
                this.productosMostrados = 0;
                const cantidad = productos.length;
                $(config.cantidadId).text(cantidad);
                $(config.contenedorSelector).empty();
                this.cargarMasProductos();
            } catch (error) {
                console.error("Error al cargar productos:", error);
            }
        },

        cargarMasProductos() {
            this.cargando = true;
            const inicio = this.productosMostrados;
            const fin = Math.min(inicio + this.productosPorCarga, this.todosLosProductos.length);
            const productosParaMostrar = this.todosLosProductos.slice(inicio, fin);
            productosParaMostrar.forEach(producto => {
                $(config.contenedorSelector).append(View.renderizarTarjetaProducto(producto));
            });
            this.productosMostrados = fin;
            this.cargando = false;
        },

        async cargarCategorias() {
            try {
                const categorias = await API.obtenerCategorias();
                View.poblarSelect("#categoriaProducto", categorias, 'id_categoria', 'nombre_categoria');
            } catch (error) {
                console.error("Error al cargar categorías:", error);
            }
        },

        async cargarMarcas() {
            try {
                const marcas = await API.obtenerMarcas();
                View.poblarSelect("#marcaProducto", marcas, 'id_marca', 'nombre_marca');
            } catch (error) {
                console.error("Error al cargar marcas:", error);
            }
        },

        async cargarGeneros() {
            try {
                const generos = await API.obtenerGeneros();
                View.poblarSelect("#generoProducto", generos, 'id_genero', 'nombre_genero');
            } catch (error) {
                console.error("Error al cargar géneros:", error);
            }
        },

        async cargarTiposProductos() {
            try {
                const tipos = await API.obtenerTiposProductos();
                View.poblarSelect("#tipoProducto", tipos, 'id_tipo_producto', 'descripcion_tipo_producto');
            } catch (error) {
                console.error("Error al cargar tipos de productos:", error);
            }
        },

        async cargarSedes() {
            try {
                const sedes = await API.obtenerSedes();
                View.poblarSelect("#sedeProducto", sedes, 'id_sede', 'nombre_sede');
            } catch (error) {
                console.error("Error al cargar sedes:", error);
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
                const respuesta = await API.registrarProducto(formData);

                if (respuesta.success) {
                    const idProducto = respuesta.data.id_producto;
                    e.target.reset();
                    this.modalProducto.hide();
                    this.modalPresentacion.show();
                    this.idRegistro = idProducto;
                    Alerts.toasSuccess(respuesta.message);

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
            const $precioCompra = $("#precioCompraPresentacion");
            const $precioVenta = $("#precioVentaPresentacion");
            const tipo = $("#tipoProducto").val();
            const imagen = $("#imagenPresentacion")[0].files[0];
            const stockActual = $("#stockActualPresentacion").val();
            const unidadMedida = $("#unidadMedidaPresentacion").val();
            const esPreparado = $("#preparada").val();

            const precioCompraLimpio = this.obtenerValorLimpio($precioCompra);
            const precioVentaLimpio = this.obtenerValorLimpio($precioVenta);

            if (!nombre || !codigo || !precioCompraLimpio || !precioVentaLimpio || !tipo) {
                Alerts.error("Campos incompletos", "Por favor completa todos los campos requeridos");
                return;
            }

            const presentacion = {
                id: Date.now(),
                nombrePresentacion: nombre,
                codigoPresentacion: codigo,
                precioCompraPresentacion: parseInt(precioCompraLimpio),
                precioVentaPresentacion: parseInt(precioVentaLimpio),
                tipoProducto: tipo,
                imagenPresentacion: imagen,
                idProducto: this.idRegistro,
                stockActual: parseInt(stockActual) || 0,
                unidadMedidaProductosPresentacion: unidadMedida,
                esPreparadoPresentacionProducto: esPreparado
            };

            this.presentacionesAcumuladas.push(presentacion);
            View.mostrarPresentacionesEnTabla(this.presentacionesAcumuladas);
            View.limpiarFormulario("#formRegistroPresentacionProducto");
            Alerts.toasSuccess("Presentación agregada correctamente");
        },

        eliminarPresentacion(id) {
            this.presentacionesAcumuladas = this.presentacionesAcumuladas.filter(p => p.id !== id);
            View.mostrarPresentacionesEnTabla(this.presentacionesAcumuladas);
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
                if (!this.idRegistro || this.idRegistro === null || this.idRegistro === undefined) {
                    Alerts.error("Error", "No hay ID de producto. Registra el producto primero.");
                    console.error("idRegistro inválido:", this.idRegistro);
                    return;
                }

                const presentaciones = this.presentacionesAcumuladas.map(p => {
                    if (!p.idProducto) {
                        console.warn("⚠️ Presentación sin idProducto:", p);
                    }

                    return {
                        nombrePresentacion: p.nombrePresentacion,
                        codigoPresentacion: p.codigoPresentacion,
                        precioCompraPresentacion: p.precioCompraPresentacion,
                        precioVentaPresentacion: p.precioVentaPresentacion,
                        tipoProducto: p.tipoProducto,
                        stockActual: p.stockActual,
                        unidadMedidaProductosPresentacion: p.unidadMedidaProductosPresentacion,
                        esPreparadoPresentacionProducto: p.esPreparadoPresentacionProducto,
                        idProducto: p.idProducto
                    };
                });

                const formData = new FormData();
                formData.append("accion", "registrarPresentaciones");
                formData.append("presentaciones", JSON.stringify(presentaciones));

                let imagenesAgregadas = 0;
                this.presentacionesAcumuladas.forEach((p, index) => {
                    if (p.imagenPresentacion) {
                        formData.append(`imagen_${index}`, p.imagenPresentacion);
                        imagenesAgregadas++;
                    }
                });

                const respuesta = await API.registrarPresentaciones(formData);

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

        async buscadorProductos(query) {
            try {
                if (!this.todosLosProductos.length) {
                    this.todosLosProductos = await API.listarProductos();
                }
                if (!query) {
                    $(config.contenedorSelector).empty();
                    this.productosMostrados = 0;
                    this.cargarMasProductos();
                    return;
                }
                const resultados = this.todosLosProductos.filter(p =>
                    p.nombre_producto.toLowerCase().includes(query.toLowerCase()) ||
                    p.codigo_producto.toLowerCase().includes(query.toLowerCase())
                );
                View.mostrarInventario(resultados);
            } catch (error) {
                console.error("Error en el buscador:", error);
            }
        },

        limpiarAcumulador() {
            this.presentacionesAcumuladas = [];
            const tabla = $("#tablaPresentaciones");
            if (tabla.length > 0) {
                tabla.html('<tr><td colspan="7" class="text-center text-muted">No hay presentaciones agregadas</td></tr>');
            }
            View.limpiarFormulario("#formRegistroPresentacionProducto");
        },

        separaMiles(numero) {
            return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    };

    return Module;
}
