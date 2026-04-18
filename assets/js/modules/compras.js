const comprasModule = {
    init(){
        this.bindEvents();
    },

    bindEvents(){
        $("#btnModalCompra").on("click", () => {
            this.listadoProductos();
            this.listadoProveedores();
        });
    },

    listadoProductos(){
        $.ajax({
            url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/productosAjax.php",
            method: "GET",
            data: { accion: "listarProductos", categoria: "Relojes" },
            success(response) {
                const productos = JSON.parse(response);
                
                let listado = '<option value="">Seleccione producto</option>';
                productos.data.forEach(element => {
                    listado += `<option value="${element.id_producto}">${element.nombre_producto}</option>`;
                });
                $("#selectProducto").html(listado);
            }     
        });
    },

    listadoProveedores(){
        $.ajax({
            url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/proveedorAjax.php",
            method: "GET",
            data: { accion: "listadoProveedoresSelect" },
            success(response) {
                const proveedores = JSON.parse(response);
                let listado = '<option value="">Seleccione proveedor</option>';
                proveedores.data.forEach(element => {
                    console.log(element.id_proveedor);
                    
                    listado += `<option value="${element.id_proveedor}">${element.nombre_proveedor}</option>`;
                });
                $("#selectProveedor").html(listado);
            }     
        });
    }
}