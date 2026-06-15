const DescuentosAPI = {
    client: new SimpleAPI(CONFIG.AJAX.DESCUENTOS),

    async registrarDescuento(data) {
        return await this.client.post({ ...data, accion: 'registrarDescuento' });
    },

    async obtenerDescuentos() {
        const response = await this.client.get({ accion: 'obtenerDescuentos' });
        return response.data || [];
    },

    async aplicarAProductos(idDescuento, idsProductos) {
        return await this.client.post({
            accion: 'aplicarAProductosEspecificos',
            idDescuento,
            idsProductos: JSON.stringify(idsProductos)
        });
    },

    async aplicarAMarca(idDescuento, idMarca) {
        return await this.client.post({
            accion: 'aplicarAMarca',
            idDescuento,
            idMarca
        });
    },

    async aplicarAProductoPorGenero(idDescuento, idProducto, idGenero) {
        return await this.client.post({
            accion: 'aplicarAProductoPorGenero',
            idDescuento,
            idProducto,
            idGenero
        });
    },

    async aplicarAGenero(idDescuento, idGenero) {
        return await this.client.post({
            accion: 'aplicarAGenero',
            idDescuento,
            idGenero
        });
    },

    async aplicarATodos(idDescuento) {
        return await this.client.post({
            accion: 'aplicarATodos',
            idDescuento
        });
    }
};

const CatalogoAPI = {
    client: new SimpleAPI(CONFIG.AJAX.CATALOGO),

    async obtenerProductos() {
        const response = await this.client.get({ accion: 'listadoProductos' });
        return { data: response.data || [] };
    },

    async obtenerMarcas() {
        const response = await this.client.get({ accion: 'listadoMarcas' });
        return { data: response.data || [] };
    },

    async obtenerGeneros() {
        const response = await this.client.get({ accion: 'listadoGeneros' });
        return { data: response.data || [] };
    }
};

const DescuentosView = {
    tablaDescuentos: null,
    modalRegistro: null,

    init() {
        this.tablaDescuentos = document.getElementById('tbodyDescuentos');
        this.modalRegistro = new bootstrap.Modal(document.getElementById('modalRegistrarDescuento'));
    },

    renderTablaDescuentos(descuentos) {
        this.tablaDescuentos.innerHTML = '';

        descuentos.forEach(desc => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${desc.id_descuento}</td>
                <td>${desc.nombre_descuento}</td>
                <td>${desc.porcentaje_descuento}%</td>
                <td>${this.formatDate(desc.fecha_inicio)}</td>
                <td>${this.formatDate(desc.fecha_fin)}</td>
                <td>
                    <button class="btn btn-sm btn-danger" data-id="${desc.id_descuento}" onclick="DescuentosModule.removerDescuento(${desc.id_descuento})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            this.tablaDescuentos.appendChild(row);
        });
    },

    llenarSelectDescuentos(descuentos) {
        const selectores = document.querySelectorAll('.selectDescuento');

        selectores.forEach(select => {
            const value = select.value;
            select.innerHTML = '<option value="">-- Selecciona un descuento --</option>';

            descuentos.forEach(desc => {
                const option = document.createElement('option');
                option.value = desc.id_descuento;
                option.textContent = `${desc.nombre_descuento} (${desc.porcentaje_descuento}%)`;
                select.appendChild(option);
            });

            if (value) select.value = value;
        });
    },

    formatDate(date) {
        return new Date(date).toLocaleDateString('es-ES');
    }
};

const DescuentosModule = {
    descuentos: [],
    productos: [],
    marcas: [],
    generos: [],

    async init() {
        DescuentosView.init();
        await this.cargarDatos();
        this.bindEvents();
    },

    async cargarDatos() {
        try {
            // Cargar descuentos
            const respDescuentos = await DescuentosAPI.obtenerDescuentos();
            if (respDescuentos.success) {
                this.descuentos = respDescuentos.data;
                DescuentosView.renderTablaDescuentos(this.descuentos);
                DescuentosView.llenarSelectDescuentos(this.descuentos);
            }

            // Cargar productos
            const respProductos = await CatalogoAPI.obtenerProductos();
            if (respProductos && respProductos.data) {
                this.productos = respProductos.data;
                this.llenarProductos();
            }

            // Cargar marcas
            const respMarcas = await CatalogoAPI.obtenerMarcas();
            if (respMarcas && respMarcas.data) {
                this.marcas = respMarcas.data;
                this.llenarMarcas();
            }

            // Cargar géneros
            const respGeneros = await CatalogoAPI.obtenerGeneros();
            if (respGeneros && respGeneros.data) {
                this.generos = respGeneros.data;
                this.llenarGeneros();
            }
        } catch (error) {
            console.error('Error cargando datos:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
        }
    },

    llenarProductos() {
        const selectProductos = document.getElementById('selectProductoGenero');
        const productosCheckbox = document.getElementById('productosCheckbox');

        // Llenar select de producto para "Género de Producto"
        selectProductos.innerHTML = '<option value="">-- Selecciona un producto --</option>';
        this.productos.forEach(prod => {
            const option = document.createElement('option');
            option.value = prod.id_producto;
            option.textContent = prod.nombre_producto;
            selectProductos.appendChild(option);
        });

        // Llenar checkboxes de productos para "Productos Específicos"
        productosCheckbox.innerHTML = '';
        this.productos.forEach(prod => {
            const div = document.createElement('div');
            div.className = 'form-check';
            div.innerHTML = `
                <input class="form-check-input" type="checkbox" value="${prod.id_producto}" id="check_${prod.id_producto}">
                <label class="form-check-label" for="check_${prod.id_producto}">
                    ${prod.nombre_producto}
                </label>
            `;
            productosCheckbox.appendChild(div);
        });
    },

    llenarMarcas() {
        const selectMarca = document.getElementById('selectMarca');
        selectMarca.innerHTML = '<option value="">-- Selecciona una marca --</option>';
        this.marcas.forEach(marca => {
            const option = document.createElement('option');
            option.value = marca.id_marca;
            option.textContent = marca.nombre_marca;
            selectMarca.appendChild(option);
        });
    },

    llenarGeneros() {
        const selectGenero = document.getElementById('selectGenero');
        const selectGeneroProducto = document.getElementById('selectGeneroProducto');

        // Llenar ambos selects de género
        [selectGenero, selectGeneroProducto].forEach(select => {
            select.innerHTML = '<option value="">-- Selecciona un género --</option>';
            this.generos.forEach(genero => {
                const option = document.createElement('option');
                option.value = genero.id_genero;
                option.textContent = genero.nombre_genero;
                select.appendChild(option);
            });
        });
    },

    bindEvents() {
        // Registro de descuento
        document.getElementById('formRegistroDescuento').addEventListener('submit', (e) => {
            e.preventDefault();
            this.registrarDescuento();
        });

        // Formularios de aplicación
        document.getElementById('formAplicarProductos').addEventListener('submit', (e) => {
            e.preventDefault();
            this.aplicarAProductos();
        });

        document.getElementById('formAplicarMarca').addEventListener('submit', (e) => {
            e.preventDefault();
            this.aplicarAMarca();
        });

        document.getElementById('formAplicarProductoGenero').addEventListener('submit', (e) => {
            e.preventDefault();
            this.aplicarAProductoPorGenero();
        });

        document.getElementById('formAplicarGenero').addEventListener('submit', (e) => {
            e.preventDefault();
            this.aplicarAGenero();
        });

        document.getElementById('formAplicarTodos').addEventListener('submit', (e) => {
            e.preventDefault();
            this.aplicarATodos();
        });

        // Cascada: cuando selecciona producto, cargar géneros de ese producto
        document.getElementById('selectProductoGenero').addEventListener('change', (e) => {
            // Aquí podrías filtrar géneros solo del producto seleccionado si es necesario
        });
    },

    async registrarDescuento() {
        const form = document.getElementById('formRegistroDescuento');
        const data = new FormData(form);

        try {
            const response = await DescuentosAPI.registrarDescuento(Object.fromEntries(data));

            if (response.success) {
                Swal.fire('Éxito', 'Descuento registrado correctamente', 'success');
                form.reset();
                DescuentosView.modalRegistro.hide();
                await this.cargarDatos();
            } else {
                Swal.fire('Error', response.message || 'Error al registrar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al registrar el descuento', 'error');
        }
    },

    async aplicarAProductos() {
        const idDescuento = document.getElementById('selectDescuentoProductos').value;
        const checkboxes = document.querySelectorAll('#productosCheckbox input:checked');
        const idsProductos = Array.from(checkboxes).map(cb => parseInt(cb.value));

        if (!idDescuento) {
            Swal.fire('Advertencia', 'Selecciona un descuento', 'warning');
            return;
        }

        if (idsProductos.length === 0) {
            Swal.fire('Advertencia', 'Selecciona al menos un producto', 'warning');
            return;
        }

        try {
            const response = await DescuentosAPI.aplicarAProductos(parseInt(idDescuento), idsProductos);

            if (response.success) {
                Swal.fire('Éxito', `Descuento aplicado a ${idsProductos.length} productos`, 'success');
                document.getElementById('formAplicarProductos').reset();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al aplicar descuento', 'error');
        }
    },

    async aplicarAMarca() {
        const idDescuento = document.getElementById('selectDescuentoMarca').value;
        const idMarca = document.getElementById('selectMarca').value;

        if (!idDescuento || !idMarca) {
            Swal.fire('Advertencia', 'Selecciona descuento y marca', 'warning');
            return;
        }

        try {
            const response = await DescuentosAPI.aplicarAMarca(parseInt(idDescuento), parseInt(idMarca));

            if (response.success) {
                Swal.fire('Éxito', 'Descuento aplicado a la marca', 'success');
                document.getElementById('formAplicarMarca').reset();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al aplicar descuento', 'error');
        }
    },

    async aplicarAProductoPorGenero() {
        const idDescuento = document.getElementById('selectDescuentoProductoGenero').value;
        const idProducto = document.getElementById('selectProductoGenero').value;
        const idGenero = document.getElementById('selectGeneroProducto').value;

        if (!idDescuento || !idProducto || !idGenero) {
            Swal.fire('Advertencia', 'Completa todos los campos', 'warning');
            return;
        }

        try {
            const response = await DescuentosAPI.aplicarAProductoPorGenero(
                parseInt(idDescuento),
                parseInt(idProducto),
                parseInt(idGenero)
            );

            if (response.success) {
                Swal.fire('Éxito', 'Descuento aplicado al género del producto', 'success');
                document.getElementById('formAplicarProductoGenero').reset();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al aplicar descuento', 'error');
        }
    },

    async aplicarAGenero() {
        const idDescuento = document.getElementById('selectDescuentoGenero').value;
        const idGenero = document.getElementById('selectGenero').value;

        if (!idDescuento || !idGenero) {
            Swal.fire('Advertencia', 'Selecciona descuento y género', 'warning');
            return;
        }

        try {
            const response = await DescuentosAPI.aplicarAGenero(parseInt(idDescuento), parseInt(idGenero));

            if (response.success) {
                Swal.fire('Éxito', 'Descuento aplicado a género', 'success');
                document.getElementById('formAplicarGenero').reset();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al aplicar descuento', 'error');
        }
    },

    async aplicarATodos() {
        const idDescuento = document.getElementById('selectDescuentoTodos').value;

        if (!idDescuento) {
            Swal.fire('Advertencia', 'Selecciona un descuento', 'warning');
            return;
        }

        Swal.fire({
            title: '⚠️ Confirmación',
            text: 'Esto aplicará el descuento a TODOS los productos. ¿Continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, aplicar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await DescuentosAPI.aplicarATodos(parseInt(idDescuento));

                    if (response.success) {
                        Swal.fire('Éxito', 'Descuento aplicado a todos los productos', 'success');
                        document.getElementById('formAplicarTodos').reset();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Error al aplicar descuento', 'error');
                }
            }
        });
    },

    async removerDescuento(idDescuento) {
        Swal.fire({
            title: '¿Remover descuento?',
            text: 'Esta acción removará el descuento de todas las presentaciones',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, remover',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    // Aquí irá la llamada a la API para remover
                    Swal.fire('Éxito', 'Descuento removido', 'success');
                    await this.cargarDatos();
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Error al remover descuento', 'error');
                }
            }
        });
    }
};

window.DescuentosModule = DescuentosModule;

