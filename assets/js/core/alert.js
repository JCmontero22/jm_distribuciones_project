const Alerts = {
    success(title, message) {
        return Swal.fire({
            title: title,
            text: message,
            icon: "success",
            confirmButtonText: "OK"
        });
    },

    error(title, message) {
        return Swal.fire({
            title: title,
            text: message,
            icon: "error",
            confirmButtonText: "OK"
        });
    },

    warning(title, message) {
        return Swal.fire({
            title: title,
            text: message,
            icon: "warning",
            confirmButtonText: "OK"
        });
    },

    confirmation(title,message) {
        return Swal.fire({
            title: title,
            text: message,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, hacerlo!",
            cancelButtonText: "No, cancelar!"
        });
    },

    toasSuccess(message) {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: message,
            showConfirmButton: false,
            timer: 1500
        });
    }
}