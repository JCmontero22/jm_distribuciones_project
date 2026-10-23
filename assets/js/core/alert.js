function limpiarFondosModales() {
    setTimeout(() => {
        const hayModalAbierto = document.querySelectorAll(".modal.show").length > 0;
        if (!hayModalAbierto) {
            document.querySelectorAll(".modal-backdrop").forEach((el) => el.remove());
            document.body.classList.remove("modal-open");
            document.body.style.removeProperty("overflow");
            document.body.style.removeProperty("padding-right");
        } else {
            // Si hay solo 1 modal abierto pero más de 1 backdrop, dejar únicamente 1
            const backdrops = document.querySelectorAll(".modal-backdrop");
            if (backdrops.length > 1) {
                backdrops.forEach((el, idx) => {
                    if (idx > 0) el.remove();
                });
            }
        }

        const haySwalActivo = document.querySelector(".swal2-popup");
        if (!haySwalActivo) {
            document.querySelectorAll(".swal2-container").forEach((el) => el.remove());
            document.body.classList.remove("swal2-shown", "swal2-height-auto", "swal2-no-backdrop");
        }
    }, 60);
}

window.limpiarFondosModales = limpiarFondosModales;

const Alerts = {
    success(title, message) {
        return Swal.fire({
            title: title,
            text: message,
            icon: "success",
            confirmButtonText: "OK",
            didClose: limpiarFondosModales
        });
    },

    error(title, message) {
        return Swal.fire({
            title: title,
            text: message,
            icon: "error",
            confirmButtonText: "OK",
            didClose: limpiarFondosModales
        });
    },

    warning(title, message) {
        return Swal.fire({
            title: title,
            text: message,
            icon: "warning",
            confirmButtonText: "OK",
            didClose: limpiarFondosModales
        });
    },

    confirmation(title, message) {
        return Swal.fire({
            title: title,
            text: message,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, hacerlo!",
            cancelButtonText: "No, cancelar!",
            didClose: limpiarFondosModales
        });
    },

    toasSuccess(message) {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: message,
            showConfirmButton: false,
            timer: 1500,
            didClose: limpiarFondosModales
        });
    }
};