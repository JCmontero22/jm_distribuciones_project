const ProductosModule = {
    init() {

        $("#registroProducto").show();
        $("#registroDetalleProducto").hide();
        this.bindEvents();
        this.listadoProductos();
        this.listadoMarcas();
    },

    bindEvents() {
        $("#formRegistroProducto").on("submit", this.registrarProducto);
    },

    registrarProducto(e) {
        e.preventDefault();
        Alerts.confirmation("Registrar este producto?", "¿Estás seguro de que deseas registrar este producto?").then((result) =>{
            if (result.isConfirmed) {
                const formData = new FormData(e.target);
                console.log(formData);
                
                formData.append("accion", "registrarProducto");
                $.ajax({
                    url: "ajax/productosAjax.php",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success(response) {
                        const res = JSON.parse(response);
                        if (res.success) {
                            Alerts.toasSuccess(res.message);
                            e.target.reset();
                            $("#modalRegistroProducto").hide();
                        } else {
                            Alerts.error("Error al registrar producto", res.message);   
                        }
                    }
                });
            }
        });
    },

    listadoProductos() {
        $.ajax({
            url: "ajax/categoriaAjax.php",
            method: "GET",
            data: { accion: "listadoCategorias" },
            success(response) {
                const categorias = JSON.parse(response);
                const selectCategoria = $("#categoriaProducto");
                categorias.data.forEach(categoria => {
                    selectCategoria.append(new Option(categoria.nombre_categoria, categoria.id_categoria));
                });    
            }
        });
    },

    listadoMarcas() {
        $.ajax({
            url: "ajax/marcaAjax.php",
            method: "GET",
            data: { accion: "listadoMarcas" },
            success(response) {
                const marcas = JSON.parse(response);
                const selectMarca = $("#marcaProducto");
                marcas.data.forEach(marca => {
                    selectMarca.append(new Option(marca.nombre_marca, marca.id_marca));
                });    
            }
        });
    }
}   


