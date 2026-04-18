/**
 * CAPA API — Solo responsable de comunicarse con el servidor
 * Los métodos aquí devuelven datos del servidor sin modificar
 */
const ComprasAPI = {
    obtenerProductos(categoria = "Relojes") {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/productosAjax.php",
                method: "GET",
                data: { accion: "listarProductos", categoria: categoria },
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

    obtenerProveedores() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/proveedorAjax.php",
                method: "GET",
                data: { accion: "listadoProveedores" },
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
const ComprasView = {
    poblarSelectProductos(productos) {
        let listado = '<option value="">Seleccione producto</option>';
        productos.forEach(producto => {
            listado += `<option value="${producto.id_producto}">${producto.nombre_producto}</option>`;
        });
        $("#selectProducto").html(listado);
    },

    poblarSelectProveedores(proveedores) {
        let listado = '<option value="">Seleccione proveedor</option>';
        proveedores.forEach(proveedor => {
            listado += `<option value="${proveedor.id_proveedor}">${proveedor.nombre_proveedor}</option>`;
        });
        $("#selectProveedor").html(listado);
    }
};

/**
 * MÓDULO COMPRAS — Coordina API y View, maneja estado y lógica de negocio
 */
const comprasModule = {
    init() {
        this.bindEvents();
    },

    bindEvents() {
        $("#btnModalCompra").on("click", () => {
            this.cargarDatos();
        });
    },

    async cargarDatos() {
        try {
            await Promise.all([
                this.cargarProductos(),
                this.cargarProveedores()
            ]);
        } catch (error) {
            Alerts.error("Error", "No se pudieron cargar los datos");
            console.error(error);
        }
    },

    async cargarProductos() {
        try {
            const productos = await ComprasAPI.obtenerProductos("Relojes");
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
    }
};
