const ProductosModule = {
    idRegistro: null,
    modalProducto: null,
    modalPresentacion: null,
    idProducto: null,
    init() {
        this.modalProducto = new bootstrap.Modal(document.getElementById('modalRegistroProducto'));
        this.modalPresentacion = new bootstrap.Modal(document.getElementById('presentacionProducto'));

        $("#formRegistroProducto")[0].reset();
        $("#formRegistroPresentacionProducto")[0].reset();

        this.bindEvents();
        this.listadoProductos();
        this.listadoCategorias();
        this.listadoMarcas();
        this.listadoGeneros();
    },

    bindEvents() {
        $("#formRegistroProducto").on("submit", this.registrarProducto);
        $("#formRegistroPresentacionProducto").on("submit", this.registrarPresentacionProducto);
        
        $(document).on("click", ".btn-add-presentacion", (e) => {
            const id = $(e.currentTarget).data("id");

            idProducto = id;
            ProductosModule.modalPresentacion.show();
        });

        $('#presentacionProducto').on('hidden.bs.modal', () => {
            this.limpiarFormulario("#formRegistroPresentacionProducto");
            this.idRegistro = null;
        });

        $('#modalRegistroProducto').on('hidden.bs.modal', () => {
            this.limpiarFormulario("#formRegistroProducto");
            this.idRegistro = null;
        });
    },

    listadoProductos() {
        $.ajax({
            url: "ajax/productosAjax.php",
            method: "GET",
            data: { accion: "listadoProductos" },
            success(response) {
                const productos = JSON.parse(response);
                console.log(productos);
                
                productos.data.forEach(element => {
                    $(".content-inventory").append(`
                             <div class="card-inventory">
                            <div class="card-inventory_img">
                                <img src="assets/img/productos/${element.img_principal_producto}" alt="${element.nombre_producto}" class="product-image">
                            </div>

                            <div class="card-inventory_infoProducto">` +
                                /* <span class="card-inventory_category">${element.nombre_categoria}</span> */
                                `<h3 class="card-inventory_name">${element.nombre_producto}</h3>
                                <p> <span>Valor de compra:</span> </span> <span class="card-inventory_cost">$ ${element.precio_costo}</span> | <span>Valor de venta:</span> <span class="card-inventory_sale">$ ${element.precio_venta}</span></p>
                            </div>

                            <div class="card-inventory_stock">
                                <span class="stock-label">Stock:</span>
                                <span class="stock-value">${element.stock_producto}</span>
                            </div>

                            <div class="card-inventory_op">
                                <button class="btn btn-success btn-add-presentacion" data-id="${element.id_producto}"><i class="fa-solid fa-file-circle-plus"></i></button>
                                <button class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </div>
                             </div>
                        `);
                });
            }
        });     
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
                            e.target.reset();
                            ProductosModule.modalProducto.hide();
                            ProductosModule.listadoProductos();
                            Alerts.toasSuccess(res.message);
                            idRegistro = res.data;
                        } else {
                            Alerts.error("Error al registrar producto", res.message);   
                        }
                    }
                });
            }
        });
    },

    registrarPresentacionProducto(e) {
        e.preventDefault();
        Alerts.confirmation("Registrar detalles de este producto?", "¿Estás seguro de que deseas registrar los detalles de este producto?").then((result) =>{
            if (result.isConfirmed) {
                const formData = new FormData(e.target);
                formData.append("accion", "registrarPresentacion");
                formData.append("idProducto", idProducto);     
                $.ajax({
                    url: "ajax/presentacionProductoAjax.php",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success(response) {
                        const res = JSON.parse(response);
                        if (res.success) {
                            ProductosModule.modalPresentacion.hide();
                            e.target.reset();
                            ProductosModule.listadoProductos();
                            Alerts.toasSuccess(res.message);
                        } else {
                            Alerts.error("Error al registrar detalles del producto", res.message);   
                        }
                    }
                });
            }
        });
    },  

    listadoCategorias() {
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
    },

    listadoGeneros() {
        $.ajax({
            url: "ajax/generoAjax.php",
            method: "GET",
            data: { accion: "listadoGeneros" },
            success(response) {
                const generos = JSON.parse(response);
                const selectGenero = $("#generoProducto");
                generos.data.forEach(genero => {
                    selectGenero.append(new Option(genero.nombre_genero, genero.id_genero));
                });    
            }
        });
    },

    limpiarFormulario(formId) {
        const form = $(formId);

        form[0].reset();
        form.find("input, textarea").val('');
        form.find("select").prop('selectedIndex', 0);
        form.find("input[type='file']").val('');
        form.find(".is-invalid, .is-valid").removeClass("is-invalid is-valid");
    }

    
}   


