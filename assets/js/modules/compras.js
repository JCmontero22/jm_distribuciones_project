const comprasModule = {
    init(){
        this.bindEvents();
    },

    bindEvents(){
        $("#btnModalCompra").on("click", () => {
            this.listadoProdcutos();
            this.listadoProveedores();
        });
    },

    listadoProdcutos(){
        $.ajax({
            url: "ajax/productosAjax.php",
            method: "GET",
            data: { accion: "listadoProductos" },
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
            url: "ajax/proveedorAjax.php",
            method: "GET",
            data: { accion: "listadoProveedores" },
            success(response) {
                console.log(response);
                
                
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