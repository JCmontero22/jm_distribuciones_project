function init() {
    if ($("#formRegistroProducto").length) {
        ProductosModule.init();
    }

    if ($("#moduleCompras").length) {
        comprasModule.init();
    }
}


function redirect(vista) {
    window.location.href = vista;
}

/***************  ***************/

init();