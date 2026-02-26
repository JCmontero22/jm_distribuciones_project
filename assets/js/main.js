function init() {
    if ($("#formRegistroProducto").length) {
        ProductosModule.init();
    }
}


function redirect(vista) {
    window.location.href = vista;
}

/***************  ***************/

init();