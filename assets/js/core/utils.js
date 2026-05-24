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

const TextParserUtils = {
    // Extrae cantidad de gramos de un texto como "212 MEN - 125G" -> 125
    extraerCantidadGramos(texto) {
        if (!texto || typeof texto !== 'string') {
            return null;
        }

        // Busca números seguidos de "g" o "G" (ej: 125G, 250g)
        let match = texto.match(/(\d+)\s*[gG](?:ramos)?/);
        if (match) {
            return parseInt(match[1], 10);
        }

        // Fallback: si el texto es solo números, asumir que son gramos (ej: "250" -> 250)
        match = texto.match(/^(\d+)$/);
        if (match) {
            return parseInt(match[1], 10);
        }

        return null;
    },

    // Verifica si el texto contiene una referencia a gramos
    tieneReferenciaDegramos(texto) {
        return /(\d+)\s*[gG](?:ramos)?|^(\d+)$/i.test(texto);
    }
};