const FormatUtils = {
    separaMiles(numero) {
        if (numero === null || numero === undefined || numero === "") {
            return "0";
        }
        return numero.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },

    formatearMiles(e) {
        const $input = $(e.target);
        let valor = $input.val().replace(/\D/g, "");

        if (valor) {
            $input.data("valor-limpio", valor);
            $input.val(this.separaMiles(valor));
        }
    },

    obtenerValorLimpio($input) {
        return $input.data("valor-limpio") || $input.val().replace(/\D/g, "");
    }
};