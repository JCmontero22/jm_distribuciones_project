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
    const BASE_URL = (CONFIG.BASE_URL || "").replace(/\/$/, "");

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

        obtenerFormulas() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/formulasAjax.php`,
                    method: "GET",
                    data: { accion: "listarFormulas" },
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

        obtenerPresentacionesPorProducto(idProducto) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/productosAjax.php`,
                    method: "GET",
                    data: { accion: "listarPresentacionesPorProducto", idProducto },
                    success(response) {
                        const datos = JSON.parse(response);
                        resolve(datos.data || []);
                    },
                    error(error) { reject(error); }
                });
            });
        },

        obtenerProducto(idProducto) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/productosAjax.php`,
                    method: "GET",
                    data: { accion: "obtenerProducto", idProducto },
                    success(response) {
                        const datos = JSON.parse(response);
                        resolve(datos.data || {});
                    },
                    error(error) { reject(error); }
                });
            });
        },

        editarProducto(formData) {
            return new Promise((resolve, reject) => {
                formData.append("accion", "editarProducto");
                $.ajax({
                    url: `${BASE_URL}/ajax/productosAjax.php`,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success(response) { resolve(JSON.parse(response)); },
                    error(error) { reject(error); }
                });
            });
        },

        eliminarProducto(idProducto) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/productosAjax.php`,
                    method: "POST",
                    data: { accion: "eliminarProducto", idProducto },
                    success(response) { resolve(JSON.parse(response)); },
                    error(error) { reject(error); }
                });
            });
        },

        eliminarPresentacion(idPresentacion) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/productosAjax.php`,
                    method: "POST",
                    data: { accion: "eliminarPresentacion", idPresentacion },
                    success(response) { resolve(JSON.parse(response)); },
                    error(error) { reject(error); }
                });
            });
        },

    };

    /**
     * CAPA VIEW — Solo responsable de renderizar HTML y manipular el DOM
     */
    const View = {
        renderizarTarjetaProducto(producto) {
            if (config.esEsencia) {
                const gramos = parseFloat(producto.cantidad_gramos || 0);
                const costoPorGramo = parseFloat(producto.costo_por_gramo || 0);
                return `
                    <div class="card-inventory">
                        <div class="card-inventory_img">
                            <img src="${BASE_URL}/assets/img/productos/${producto.logo_producto}" alt="${producto.nombre_producto}" class="product-image">
                        </div>
                        <div class="card-inventory_infoProducto">
                            <h3 class="card-inventory_name">${producto.nombre_producto}</h3>
                            <p>
                                <span>Costo por gramo:</span>
                                <span class="card-inventory_cost">${costoPorGramo > 0 ? '$ ' + FormatUtils.separaMiles(costoPorGramo.toFixed(0)) : '-'}</span>
                            </p>
                            <p class="card-inventory_description">
                                ${producto.descripcion_producto || ''}
                            </p>
                        </div>
                        <div class="card-inventory_stock">
                            <p class="stock-label">Disponible: ${FormatUtils.separaMiles(gramos.toFixed(0))} g</p>
                        </div>
                        <div class="card-inventory_op">
                            <button class="btn btn-info btn-ver-presentaciones" data-id="${producto.id_producto}" data-nombre="${producto.nombre_producto}" title="Ver presentaciones">
                                <i class="fa-solid fa-list"></i>
                            </button>
                            <button class="btn btn-success btn-add-presentacion" data-id="${producto.id_producto}" type="button" data-bs-toggle="modal" data-bs-target="${config.idModalPresentacion}">
                                <i class="fa-solid fa-file-circle-plus"></i>
                            </button>
                            <button class="btn btn-warning "
                                data-id="${producto.id_producto}"
                                title="Editar producto">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-danger btn-eliminar-producto"
                                data-id="${producto.id_producto}"
                                data-nombre="${producto.nombre_producto}"
                                title="Eliminar producto">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }

            const precioCompra = producto.precio_compra_presentacion ? `$ ${FormatUtils.separaMiles(producto.precio_compra_presentacion)}` : '-';
            const precioVenta = producto.precio_venta_presentacion ? `$ ${FormatUtils.separaMiles(producto.precio_venta_presentacion)}` : '-';

            return `
                <div class="card-inventory">
                    <div class="card-inventory_img">
                        <img src="${BASE_URL}/assets/img/productos/${producto.img_presentacion}" alt="${producto.nombre_producto}" class="product-image">
                    </div>
                    <div class="card-inventory_infoProducto">
                        <h3 class="card-inventory_name">${producto.nombre_producto} - ${producto.nombre_presentacion}</h3>
                        <p>
                            <span>Valor de compra:</span>
                            <span class="card-inventory_cost">${precioCompra}</span>
                        </p>
                        <p>
                            <span>Valor de venta:</span>
                            <span class="card-inventory_sale">${precioVenta}</span>
                        </p>
                        <p class="card-inventory_description">
                            ${producto.descripcion_producto}
                        </p>
                    </div>
                    <div class="card-inventory_stock">
                        <p class="stock-label">Stock: ${producto.stock_total || 0}</p>
                    </div>
                    <div class="card-inventory_op">
                        <button class="btn btn-info btn-ver-presentaciones" data-id="${producto.id_producto}" data-nombre="${producto.nombre_producto}" title="Ver presentaciones">
                            <i class="fa-solid fa-list"></i>
                        </button>
                        <button class="btn btn-success btn-add-presentacion" data-id="${producto.id_producto}" type="button" data-bs-toggle="modal" data-bs-target="${config.idModalPresentacion}">
                            <i class="fa-solid fa-file-circle-plus"></i>
                        </button>
                        <button class="btn btn-warning btn-editar-producto"
                            data-id="${producto.id_producto}"
                            title="Editar producto">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-danger btn-eliminar-presentacion-card"
                            data-id="${producto.id_presentacion}"
                            data-nombre="${producto.nombre_presentacion}"
                            title="Eliminar presentación">
                            <i class="fa-solid fa-trash"></i>
                        </button>
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
            const placeholder = select.find('option:first').clone();

            select.empty();

            if (placeholder.length && placeholder.is('[disabled]')) {
                select.append(placeholder);
            }

            items.forEach(item => {
                select.append(new Option(item[textKey], item[valueKey]));
            });
        },

        obtenerColumnasPresentaciones() {
            const tabla = $("#tablaPresentaciones").closest("table");
            if (!tabla.length) return [];

            return tabla.find("thead th").map((_, th) => {
                return $(th).text().trim().toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/\s+/g, "");
            }).get();
        },

        obtenerColspanPresentaciones() {
            const columnas = this.obtenerColumnasPresentaciones();
            if (columnas.length > 0) return columnas.length;
            return config.tieneFormula ? 7 : 6;
        },

        renderizarFilaPresentacion(presentacion, columnas = []) {
            const tipoBadge = `<span class="badge ${presentacion.tipoProducto === '1' ? 'bg-info' : 'bg-warning'}">${presentacion.tipoProducto === '1' ? 'REVENTA' : 'PRODUCCIÓN'}</span>`;
            const accionesHtml = `
                <button class="btn btn-sm btn-warning btn-editar-presentacion me-2" data-id="${presentacion.id}" title="Editar">
                    <i class="fa-solid fa-pen-to-square"></i> Editar
                </button>
                <button class="btn btn-sm btn-danger btn-eliminar-presentacion" data-id="${presentacion.id}" title="Eliminar">
                    <i class="fa-solid fa-trash"></i> Eliminar
                </button>
            `;

            const celdasPorColumna = {
                nombre: presentacion.nombrePresentacion || '-',
                codigo: presentacion.codigoPresentacion || '-',
                tamano: presentacion.cantidadGramosPresentacion ? `${presentacion.cantidadGramosPresentacion}g` : '-',
                preciocompra: `$${presentacion.precioCompraPresentacion ? presentacion.precioCompraPresentacion.toLocaleString('es-CO') : '-'}`,
                precioventa: `$${presentacion.precioVentaPresentacion ? presentacion.precioVentaPresentacion.toLocaleString('es-CO') : '-'}`,
                tipo: tipoBadge,
                formula: presentacion.nombreFormula || '-',
                acciones: accionesHtml
            };

            const columnasRender = columnas.length
                ? columnas
                : ["nombre", "codigo", "preciocompra", "precioventa", "tipo", "acciones"];

            const tds = columnasRender.map(col => {
                const valor = celdasPorColumna[col] ?? '-';
                return `<td>${valor}</td>`;
            }).join('');

            return `<tr>${tds}</tr>`;
        },

        mostrarPresentacionesEnTabla(presentaciones) {
            console.log('📊 mostrarPresentacionesEnTabla ejecutándose', presentaciones.length);
            let tabla = $("#tablaPresentaciones");
            const self = this;

            if (tabla.length === 0) {
                console.log('📋 Creando tabla de presentaciones');
                let encabezados = `<th>Nombre</th><th>Código</th><th>Precio Compra</th><th>Precio Venta</th><th>Tipo</th><th>Acciones</th>`;

                $("#formRegistroPresentacionProducto").after(`
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Presentaciones agregadas</h5>
                            <table class="table table-striped table-sm">
                                <thead class="table-dark">
                                    <tr>${encabezados}</tr>
                                </thead>
                                <tbody id="tablaPresentaciones"></tbody>
                            </table>
                        </div>
                    </div>
                `);
                tabla = $("#tablaPresentaciones");
            }

            if (presentaciones.length === 0) {
                console.log('✅ Sin presentaciones');
                tabla.html(`<tr><td colspan="6" class="text-center text-muted">No hay presentaciones agregadas</td></tr>`);
                return;
            }

            console.log('🎨 Renderizando', presentaciones.length, 'presentaciones');
            const html = presentaciones.map((p, idx) => `
                <tr>
                    <td>${p.nombrePresentacion}</td>
                    <td>${p.codigoPresentacion}</td>
                    <td>${p.precioCompraPresentacion || '-'}</td>
                    <td>${p.precioVentaPresentacion || '-'}</td>
                    <td>${p.tipoProducto || '-'}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning me-2" onclick="editarPres(${idx})">Editar</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarPres(${idx})">Eliminar</button>
                    </td>
                </tr>
            `).join('');

            tabla.html(html);
            console.log('✅ Tabla actualizada');
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
        presentacionEditandoId: null,
        presentacionExistenteEditando: null,
        modalProducto: null,
        modalPresentacion: null,
        todosLosProductos: [],
        productosMostrados: 0,
        productosPorCarga: 20,
        cargando: false,

        init() {
            this.modalProducto = new bootstrap.Modal(document.getElementById('modalRegistroProducto'));
            this.modalPresentacion = new bootstrap.Modal(document.getElementById('modalRegistroPresentacion'));

            // Establecer como módulo activo para funciones globales
            moduloActivo = this;
            console.log('✅ Módulo inicializado:', config.categoria);

            View.limpiarFormulario("#formRegistroProducto");
            View.limpiarFormulario("#formRegistroPresentacionProducto");

            this.bindEvents();
            this.cargarDatos();
        },

        bindEvents() {
            $("#formRegistroProducto").on("submit", (e) => {
                const idEditar = $("#idProductoEditar").val();
                if (idEditar) {
                    this.submitEditarProducto(e);
                } else {
                    this.registrarProducto(e);
                }
            });
            $("#formRegistroPresentacionProducto").on("submit", (e) => this.registrarTodasPresentaciones(e));

            const self = this;
            $(document).on("click", "#btnAgregarStock", function(e) {
                e.preventDefault();
                console.log('🔘 Click en Agregar - ejecutando');
                self.agregarPresentacionAlAcumulador();
            });

            $(document).on("click", ".btn-add-presentacion", (e) => {
                const id = $(e.currentTarget).data("id");
                this.idProducto = id;
                this.cargarPresentacionesExistentesEnModal(id);
                this.modalPresentacion.show();
            });

            $(document).on("click", ".btn-ver-presentaciones", (e) => {
                const id = $(e.currentTarget).data("id");
                const nombre = $(e.currentTarget).data("nombre");
                this.verPresentaciones(id, nombre);
            });

            $(document).on("click", ".btn-editar-producto", (e) => {
                const id = $(e.currentTarget).data("id");
                this.abrirModalEdicion(id);
            });

            $(document).on("click", ".btn-eliminar-producto", (e) => {
                const id = $(e.currentTarget).data("id");
                const nombre = $(e.currentTarget).data("nombre");
                this.confirmarEliminarProducto(id, nombre);
            });

            $(document).on("click", ".btn-eliminar-presentacion-card", (e) => {
                const id = $(e.currentTarget).data("id");
                const nombre = $(e.currentTarget).data("nombre");
                this.confirmarEliminarPresentacion(id, nombre);
            });

            $('#modalRegistroPresentacion').on('show.bs.modal', () => {
                this.cargarGeneros();
            });

            $('#modalRegistroPresentacion').on('hidden.bs.modal', () => {
                View.limpiarFormulario("#formRegistroPresentacionProducto");
                this.cancelarEdicionPresentacion();
                this.limpiarAcumulador();
                this.idRegistro = null;
                $("#seccionPresentacionesExistentes").hide();
                $("#tablaExistentesBody").empty();
            });

            $('#modalRegistroProducto').on('hidden.bs.modal', () => {
                View.limpiarFormulario("#formRegistroProducto");
                $("#idProductoEditar").val("");
                $("#staticBackdropLabel").text("Registrar Producto");
                $("#formRegistroProducto .btnRegistro").html('<i class="fa-regular fa-floppy-disk"></i> Registrar Producto');
            });

            $("[data-format-miles]").on("input", (e) => FormatUtils.formatearMiles(e));

            $("#buscar").on("input", (e) => {
                const query = e.target.value.trim();
                this.buscadorProductos(query);
            });

            $(document).on("click", ".btn-eliminar-presentacion", (e) => {
                const id = $(e.currentTarget).data("id");
                this.eliminarPresentacionAcumulada(id);
            });

            $(document).on("click", ".btn-editar-presentacion", (e) => {
                e.preventDefault();
                e.stopPropagation();
                const id = $(e.currentTarget).data("id");
                console.log('🖊️ Editando presentación:', id);
                this.editarPresentacionAcumulada(id);
            });

            $(window).on('scroll', () => {
                if (this.cargando) return;
                const bottom = $(window).scrollTop() + $(window).height() >= $(document).height() - 100;
                if (bottom && this.productosMostrados < this.todosLosProductos.length && !$("#buscar").val()) {
                    this.cargarMasProductos();
                }
            });

            $("#nombreProducto").on("input", (e) => {
                const nombre = e.target.value.trim();
                if (nombre) {
                    const codigoGenerado = this.generarCodigoProducto(nombre);
                    $("#codigoProducto").val(codigoGenerado);
                } else {
                    $("#codigoProducto").val("");
                }
            });

            $("#nombrePresentacion").on("input", (e) => {
                const nombre = e.target.value.trim();
                if (nombre) {
                    const codigoGenerado = this.generarCodigoPresentacion(nombre);
                    $("#codigoPresentacion").val(codigoGenerado);
                } else {
                    $("#codigoPresentacion").val("");
                }
            });
        },


        async cargarDatos() {
            try {
                const promises = [
                    this.cargarProductos(),
                    this.cargarCategorias(),
                    this.cargarMarcas(),
                    this.cargarGeneros(),
                    this.cargarTiposProductos(),
                    this.cargarFormulas()
                ];
                
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
                View.poblarSelect("#generoPresentacion", generos, 'id_genero', 'nombre_genero');
            } catch (error) {
                console.error("❌ Error al cargar géneros:", error);
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

        async cargarFormulas() {
            try {
                const formulas = await API.obtenerFormulas();
                View.poblarSelect("#formula", formulas, 'id_formula', 'nombre_formula');
            } catch (error) {
                console.error("Error al cargar fórmulas:", error);
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
            console.log('➕ AGREGANDO PRESENTACIÓN');

            const nombre = $("#nombrePresentacion").val();
            console.log('📝 Nombre:', nombre);
            const codigo = $("#codigoPresentacion").val();
            const $precioCompra = $("#precioCompraPresentacion") ?? 0;
            const $precioVenta = $("#precioVentaPresentacion") ?? 0;
            const tipo = $("#tipoProducto").val();
            const imagen = $("#imagenPresentacion")[0].files[0];
            const unidadMedida = $("#unidadMedidaPresentacion").val();
            const idGenero = $("#generoPresentacion").val();
            const favoritoPresentacion = $("#favoritoPresentacion").is(":checked") ? 1 : 0;
            const descripcion = $("#descripcionPresentacion").val();

            this.idRegistro = this.idRegistro || this.idProducto;

            const precioCompraLimpio = $precioCompra.val() ? FormatUtils.obtenerValorLimpio($precioCompra) : null;
            const precioVentaLimpio = $precioVenta.val() ? FormatUtils.obtenerValorLimpio($precioVenta) : null;

            // Extrae cantidad de gramos del nombre de la presentación (ej: "212 MEN - 125G" -> 125)
            const cantidadGramosPresentacion = TextParserUtils.extraerCantidadGramos(nombre);

            if (!nombre || !codigo || !tipo) {
                Alerts.error("Campos incompletos", "Por favor completa los campos: nombre, código y tipo");
                return;
            }

            if (config.tieneFormula && !idFormula) {
                Alerts.error("Campos incompletos", "Por favor selecciona una fórmula");
                return;
            }

            const presentacion = {
                id: this.presentacionEditandoId || Date.now(),
                isExistente: this.presentacionExistenteEditando ? true : false,
                idPresentacionExistente: this.presentacionExistenteEditando || null,
                nombrePresentacion: nombre,
                codigoPresentacion: codigo,
                precioCompraPresentacion: precioCompraLimpio ? parseInt(precioCompraLimpio) : null,
                precioVentaPresentacion: precioVentaLimpio ? parseInt(precioVentaLimpio) : null,
                tipoProducto: tipo,
                imagenPresentacion: imagen,
                idProducto: this.idRegistro,
                unidadMedidaProductosPresentacion: unidadMedida,
                cantidadGramosPresentacion: cantidadGramosPresentacion,
                idGenero: idGenero || null,
                favoritoPresentacion: favoritoPresentacion,
                descripcionPresentacion: descripcion
            };

            if (this.presentacionEditandoId) {
                // Modo edición: actualizar presentación (nueva o existente)
                const indice = this.presentacionesAcumuladas.findIndex(p => p.id === this.presentacionEditandoId);
                if (indice !== -1) {
                    this.presentacionesAcumuladas[indice] = presentacion;
                    Alerts.toasSuccess("Presentación actualizada correctamente");
                } else {
                    this.presentacionesAcumuladas.push(presentacion);
                    Alerts.toasSuccess("Presentación agregada correctamente");
                }
                this.cancelarEdicionPresentacion();
            } else {
                // Modo agregar: insertar nueva presentación
                this.presentacionesAcumuladas.push(presentacion);
                Alerts.toasSuccess("Presentación agregada correctamente");
            }

            View.mostrarPresentacionesEnTabla(this.presentacionesAcumuladas);
            View.limpiarFormulario("#formRegistroPresentacionProducto");
        },

        eliminarPresentacionAcumulada(id) {
            this.presentacionesAcumuladas = this.presentacionesAcumuladas.filter(p => p.id !== id);
            View.mostrarPresentacionesEnTabla(this.presentacionesAcumuladas);
            if (this.presentacionEditandoId === id) {
                this.cancelarEdicionPresentacion();
            }
            Alerts.toasSuccess("Presentación eliminada");
        },

        editarPresentacionAcumulada(id) {
            const presentacion = this.presentacionesAcumuladas.find(p => p.id === id);
            if (!presentacion) return;

            // Cargar datos en el formulario
            $("#nombrePresentacion").val(presentacion.nombrePresentacion);
            $("#codigoPresentacion").val(presentacion.codigoPresentacion);
            $("#precioCompraPresentacion").val(presentacion.precioCompraPresentacion || '');
            $("#precioVentaPresentacion").val(presentacion.precioVentaPresentacion || '');
            $("#tipoProducto").val(presentacion.tipoProducto);
            $("#unidadMedidaPresentacion").val(presentacion.unidadMedidaProductosPresentacion || '');
            $("#generoPresentacion").val(presentacion.idGenero || '');
            $("#descripcionPresentacion").val(presentacion.descripcionPresentacion || '');
            if (presentacion.favoritoPresentacion) {
                $("#favoritoPresentacion").prop('checked', true);
            }

            // Cambiar botón a modo edición
            this.presentacionEditandoId = id;
            const $btnAgregar = $("#btnAgregarStock");
            $btnAgregar.html('<i class="fa-solid fa-pen-to-square"></i> Guardar Cambios');
            $btnAgregar.addClass('btn-info').removeClass('btn-primary');

            // Scroll al formulario
            document.getElementById('formRegistroPresentacionProducto').scrollIntoView({ behavior: 'smooth' });
        },

        cancelarEdicionPresentacion() {
            this.presentacionEditandoId = null;
            this.presentacionExistenteEditando = null;
            const $btnAgregar = $("#btnAgregarStock");
            $btnAgregar.html('<i class="fa-solid fa-plus"></i> Agregar');
            $btnAgregar.removeClass('btn-info').addClass('btn-primary');
            View.limpiarFormulario("#formRegistroPresentacionProducto");
        },

        editarPresentacionExistente(presentacion) {
            // Guardar ID de presentación existente
            this.presentacionExistenteEditando = presentacion.id_presentacion;
            this.presentacionEditandoId = presentacion.id_presentacion;
            this.idRegistro = presentacion.id_producto || this.idRegistro || this.idProducto;

            // Cargar datos en el formulario
            $("#nombrePresentacion").val(presentacion.nombre_presentacion || '');
            $("#codigoPresentacion").val(presentacion.codigo_presentacion || '');
            $("#precioCompraPresentacion").val(presentacion.precio_compra_presentacion || '');
            $("#precioVentaPresentacion").val(presentacion.precio_venta_presentacion || '');
            $("#tipoProducto").val(presentacion.id_tipo_producto || '');
            $("#unidadMedidaPresentacion").val(presentacion.unidad_medida_presentacion || '');
            $("#generoPresentacion").val(presentacion.id_genero || '');
            $("#descripcionPresentacion").val(presentacion.descripcion_presentacion || '');
            if (presentacion.favorito_presentacion) {
                $("#favoritoPresentacion").prop('checked', true);
            }

            // Cambiar botón a modo edición
            const $btnAgregar = $("#btnAgregarStock");
            $btnAgregar.html('<i class="fa-solid fa-pen-to-square"></i> Guardar Cambios');
            $btnAgregar.addClass('btn-info').removeClass('btn-primary');

            $("#staticBackdropLabel").text("Editar Presentación");

            if (this.modalPresentacion) {
                this.modalPresentacion.show();
            }

            // Scroll al formulario
            document.getElementById('formRegistroPresentacionProducto').scrollIntoView({ behavior: 'smooth' });
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
                        idPresentacionExistente: p.idPresentacionExistente || null,
                        isExistente: p.isExistente || false,
                        nombrePresentacion: p.nombrePresentacion,
                        codigoPresentacion: p.codigoPresentacion,
                        precioCompraPresentacion: p.precioCompraPresentacion,
                        precioVentaPresentacion: p.precioVentaPresentacion,
                        tipoProducto: p.tipoProducto,
                        stockActual: p.stockActual,
                        unidadMedidaProductosPresentacion: p.unidadMedidaProductosPresentacion,
                        cantidadGramosPresentacion: p.cantidadGramosPresentacion,
                        idGenero: p.idGenero || null,
                        idProducto: p.idProducto,
                        favoritoPresentacion: p.favoritoPresentacion,
                        descripcionPresentacion: p.descripcionPresentacion || null
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

                console.log(formData);


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

                console.log(this.todosLosProductos);
                

                const resultados = this.todosLosProductos.filter(p =>
                    p.nombre_producto.toLowerCase().includes(query.toLowerCase()) ||
                    p.codigo_producto.toLowerCase().includes(query.toLowerCase()) || 
                    p.nombre_presentacion.toLowerCase().includes(query.toLowerCase())
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
                const colspan = View.obtenerColspanPresentaciones();
                tabla.html(`<tr><td colspan="${colspan}" class="text-center text-muted">No hay presentaciones agregadas</td></tr>`);
            }
            View.limpiarFormulario("#formRegistroPresentacionProducto");
        },

        async verPresentaciones(idProducto, nombreProducto) {
            try {
                const presentaciones = await API.obtenerPresentacionesPorProducto(idProducto);

                let filas;
                if (config.esEsencia) {
                    filas = presentaciones.length === 0
                        ? `<tr><td colspan="6" class="text-center text-muted py-3">Sin presentaciones registradas</td></tr>`
                        : presentaciones.map(p => `
                            <tr>
                                <td>${p.nombre_presentacion}</td>
                                <td>${p.codigo_presentacion}</td>
                                <td class="text-center">${p.cantidad_gramos_presentacion ? p.cantidad_gramos_presentacion + ' g' : '-'}</td>
                                <td class="text-center fw-bold ${parseFloat(p.stock_gramos) > 0 ? 'text-success' : 'text-danger'}">
                                    ${FormatUtils.separaMiles(parseFloat(p.stock_gramos).toFixed(0))} g
                                </td>
                                <td class="text-center">${parseFloat(p.costo_por_gramo) > 0 ? '$ ' + FormatUtils.separaMiles(parseFloat(p.costo_por_gramo).toFixed(0)) : '-'}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-danger btn-del-pres-modal" data-id="${p.id_presentacion}" data-nombre="${p.nombre_presentacion}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                } else {
                    filas = presentaciones.length === 0
                        ? `<tr><td colspan="5" class="text-center text-muted py-3">Sin presentaciones registradas</td></tr>`
                        : presentaciones.map(p => `
                            <tr>
                                <td>${p.nombre_presentacion}</td>
                                <td>${p.codigo_presentacion}</td>
                                <td class="text-center">${p.precio_compra_presentacion ? '$ ' + FormatUtils.separaMiles(p.precio_compra_presentacion) : '-'}</td>
                                <td class="text-center fw-bold ${parseInt(p.stock_total) > 0 ? 'text-success' : 'text-danger'}">
                                    ${p.stock_total || 0}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning btn-edit-pres-modal me-2" data-id="${p.id_presentacion}" data-nombre="${p.nombre_presentacion}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-del-pres-modal" data-id="${p.id_presentacion}" data-nombre="${p.nombre_presentacion}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                }

                const encabezados = config.esEsencia
                    ? `<th>Presentación</th><th>Código</th><th class="text-center">Tamaño</th><th class="text-center">Stock</th><th class="text-center">Costo/g</th><th></th>`
                    : `<th>Presentación</th><th>Código</th><th class="text-center">P. Compra</th><th class="text-center">Stock</th><th></th>`;

                const html = `
                    <div class="table-responsive">
                        <table class="table table-sm table-dark mb-0" id="tablaPresentacionesModal">
                            <thead><tr>${encabezados}</tr></thead>
                            <tbody>${filas}</tbody>
                        </table>
                    </div>
                `;

                const swalRef = Swal.fire({
                    title: `<span style="font-size:1rem;color:#eab308"><i class="fa-solid fa-list"></i></span> ${nombreProducto}`,
                    html,
                    width: 700,
                    background: '#1c1c1e',
                    color: '#f4f4f5',
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#3f3f46',
                    showClass: { popup: 'animate__animated animate__fadeIn' },
                    didOpen: () => {
                        document.querySelectorAll('.btn-edit-pres-modal').forEach(btn => {
                            btn.addEventListener('click', async (ev) => {
                                const idPresentacion = ev.currentTarget.dataset.id;
                                const presentacion = presentaciones.find(p => p.id_presentacion == idPresentacion);
                                if (presentacion) {
                                    Swal.close();
                                    this.editarPresentacionExistente(presentacion);
                                }
                            });
                        });

                        document.querySelectorAll('.btn-del-pres-modal').forEach(btn => {
                            btn.addEventListener('click', async (ev) => {
                                const id = ev.currentTarget.dataset.id;
                                const nombre = ev.currentTarget.dataset.nombre;
                                Swal.close();
                                await this.confirmarEliminarPresentacion(id, nombre, () => this.verPresentaciones(idProducto, nombreProducto));
                            });
                        });
                    }
                });

                await swalRef;
            } catch (error) {
                console.error("Error al cargar presentaciones:", error);
                Alerts.error("Error", "No se pudieron cargar las presentaciones");
            }
        },

        async abrirModalEdicion(idProducto) {
            try {
                const producto = await API.obtenerProducto(idProducto);
                if (!producto || !producto.id_producto) {
                    Alerts.error("Error", "No se pudo cargar el producto");
                    return;
                }

                $("#idProductoEditar").val(producto.id_producto);
                $("#nombreProducto").val(producto.nombre_producto);
                $("#codigoProducto").val(producto.codigo_producto);
                $("#categoriaProducto").val(producto.id_categoria);
                $("#marcaProducto").val(producto.id_marca);
                $("#descripcionProducto").val(producto.descripcion_producto);

                $("#staticBackdropLabel").text("Editar Producto");
                $("#formRegistroProducto .btnRegistro").html('<i class="fa-regular fa-floppy-disk"></i> Actualizar Producto');

                this.modalProducto.show();
            } catch (error) {
                console.error("Error al cargar producto:", error);
                Alerts.error("Error", "No se pudo cargar el producto");
            }
        },

        async submitEditarProducto(e) {
            e.preventDefault();
            const resultado = await Alerts.confirmation("¿Actualizar producto?", "Se guardarán los cambios realizados");
            if (!resultado.isConfirmed) return;

            try {
                const formData = new FormData(e.target);
                const respuesta = await API.editarProducto(formData);

                if (respuesta.success) {
                    this.modalProducto.hide();
                    await this.cargarProductos();
                    Alerts.toasSuccess(respuesta.message);
                } else {
                    Alerts.error("Error al actualizar", respuesta.message);
                }
            } catch (error) {
                Alerts.error("Error", "Error al actualizar el producto");
                console.error(error);
            }
        },

        async confirmarEliminarProducto(idProducto, nombre) {
            const resultado = await Alerts.confirmation(
                "¿Eliminar producto?",
                `Se eliminará "${nombre}" y todas sus presentaciones. Esta acción no se puede deshacer.`
            );
            if (!resultado.isConfirmed) return;

            try {
                const respuesta = await API.eliminarProducto(idProducto);
                if (respuesta.success) {
                    await this.cargarProductos();
                    Alerts.toasSuccess(respuesta.message);
                } else {
                    Alerts.error("Error al eliminar", respuesta.message);
                }
            } catch (error) {
                Alerts.error("Error", "Error al eliminar el producto");
                console.error(error);
            }
        },

        async confirmarEliminarPresentacion(idPresentacion, nombre, callback = null) {
            const resultado = await Alerts.confirmation(
                "¿Eliminar presentación?",
                `Se eliminará la presentación "${nombre}".`
            );
            if (!resultado.isConfirmed) return;

            try {
                const respuesta = await API.eliminarPresentacion(idPresentacion);
                if (respuesta.success) {
                    if (callback) {
                        callback();
                    } else {
                        await this.cargarProductos();
                    }
                    Alerts.toasSuccess(respuesta.message);
                } else {
                    Alerts.error("Error al eliminar", respuesta.message);
                }
            } catch (error) {
                Alerts.error("Error", "Error al eliminar la presentación");
                console.error(error);
            }
        },

        async cargarPresentacionesExistentesEnModal(idProducto) {
            try {
                this.idProducto = idProducto;
                this.idRegistro = this.idRegistro || idProducto;

                const presentaciones = await API.obtenerPresentacionesPorProducto(idProducto);
                const seccion = $("#seccionPresentacionesExistentes");
                const tbody = $("#tablaExistentesBody");

                if (!seccion.length) return;

                if (presentaciones.length === 0) {
                    seccion.hide();
                    return;
                }

                const filas = presentaciones.map(p => {
                    const stock = config.esEsencia
                        ? `${FormatUtils.separaMiles(parseFloat(p.stock_gramos).toFixed(0))} g`
                        : `${p.stock_total || 0}`;
                    const precio = p.precio_compra_presentacion
                        ? `$ ${FormatUtils.separaMiles(p.precio_compra_presentacion)}`
                        : '-';
                    return `
                        <tr>
                            <td>${p.nombre_presentacion}</td>
                            <td>${p.codigo_presentacion}</td>
                            <td class="text-center">${precio}</td>
                            <td class="text-center">${stock}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-warning btn-edit-existente me-2" data-id="${p.id_presentacion}" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-del-existente" data-id="${p.id_presentacion}" data-nombre="${p.nombre_presentacion}" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                tbody.html(filas);
                seccion.show();

                // Listener para editar presentación existente
                $(document).off("click", ".btn-edit-existente").on("click", ".btn-edit-existente", async (e) => {
                    const idPresentacion = $(e.currentTarget).data("id");
                    const presentacion = presentaciones.find(p => p.id_presentacion == idPresentacion);
                    if (presentacion) {
                        Swal.close();
                        this.editarPresentacionExistente(presentacion);
                    }
                });

                $(document).off("click", ".btn-del-existente").on("click", ".btn-del-existente", async (e) => {
                    const id = $(e.currentTarget).data("id");
                    const nombre = $(e.currentTarget).data("nombre");
                    await this.confirmarEliminarPresentacion(id, nombre, () => {
                        this.cargarPresentacionesExistentesEnModal(idProducto);
                    });
                });
            } catch (error) {
                console.error("Error al cargar presentaciones existentes:", error);
            }
        },

        generarCodigoProducto(nombre) {
            const timestamp = Date.now().toString().slice(-5); // últimos 5 dígitos
            const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            const nombreLimpio = nombre.replace(/\s+/g, '').toUpperCase().slice(0, 5); // primeros 5 caracteres
            return `${nombreLimpio}-${timestamp}-${randomNum}`;
        },

        generarCodigoPresentacion(nombre) {
            const timestamp = Date.now().toString().slice(-5); // últimos 5 dígitos
            const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            const nombreLimpio = nombre.replace(/\s+/g, '').toUpperCase().slice(0, 5); // primeros 5 caracteres
            return `${nombreLimpio}-${timestamp}-${randomNum}`;
        }
    };

    return Module;
}

// Funciones globales para editar/eliminar presentaciones
let moduloActivo = null;

function editarPres(idx) {
    console.log('🔧 Editando presentación índice:', idx);
    if (moduloActivo && moduloActivo.editarPresentacionAcumulada) {
        const presentacion = moduloActivo.presentacionesAcumuladas[idx];
        if (presentacion) {
            moduloActivo.editarPresentacionAcumulada(presentacion.id);
        }
    }
}

function eliminarPres(idx) {
    console.log('🗑️ Eliminando presentación índice:', idx);
    if (moduloActivo && moduloActivo.eliminarPresentacionAcumulada) {
        const presentacion = moduloActivo.presentacionesAcumuladas[idx];
        if (presentacion) {
            moduloActivo.eliminarPresentacionAcumulada(presentacion.id);
        }
    }
}
