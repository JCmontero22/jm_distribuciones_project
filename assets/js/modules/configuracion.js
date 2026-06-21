const ConfiguracionAPI = {
    catalogoClient: new SimpleAPI(CONFIG.AJAX.CATALOGO),

    async categoriasProductos() {
        const response = await this.catalogoClient.get({ accion: 'listadoCategorias' });
        return response && response.data ? response.data : [];
    }
};

const ConfiguracionView = {
    renderCategoriasProductos(categorias) {
        const tbody = document.querySelector('#tablaCategoriaProductos tbody');
        tbody.innerHTML = '';

        if (categorias.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">No hay categorías registradas.</td></tr>';
            return;
        }

        categorias.forEach(categoria => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${categoria.id_categoria}</td>
                <td>${categoria.nombre_categoria}</td>
                <td>
                    <button class="btn btn-sm btn-warning btnEditar" data-id="${categoria.id_categoria}"><i class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-sm btn-danger btnEliminar" data-id="${categoria.id_categoria}"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
};

const ConfiguracionModule = {
    init() {
        this.cargarDatosIniciales();
    },

    cargarDatosIniciales() {
        this.listadoCategoriasProductos();
    },

    async listadoCategoriasProductos() {
        try {
            const categorias = await ConfiguracionAPI.categoriasProductos();
            ConfiguracionView.renderCategoriasProductos(categorias);
        } catch (error) {
            console.error('Error al cargar categorías de productos:', error);
        }
     }  
};

// Exponer el módulo en `window` para que `main.js` lo encuentre
window.ConfiguracionModule = ConfiguracionModule;