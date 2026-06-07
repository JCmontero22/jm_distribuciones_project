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

const AppUI = {
    dataTableLanguage() {
        return {
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles",
            info: "Mostrando _START_ a _END_ de _TOTAL_",
            infoEmpty: "Mostrando 0 a 0 de 0",
            infoFiltered: "(filtrado de _MAX_ registros)",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                last: "Ultimo",
                next: "Siguiente",
                previous: "Anterior"
            }
        };
    },

    initDataTable(selector, options = {}) {
        if (!selector || !$(selector).length) return null;

        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().destroy();
        }

        return $(selector).DataTable({
            language: this.dataTableLanguage(),
            ...options
        });
    },

    createModal(elementId) {
        const element = document.getElementById(elementId);
        return element ? bootstrap.Modal.getOrCreateInstance(element) : null;
    },

    resetForm(formSelector) {
        const form = $(formSelector)[0];
        if (form) form.reset();
    },

    fillSelect(selectId, items, valueKey, textKey, keepFirstOption = true) {
        const $select = $(selectId);
        if (!$select.length) return;

        const first = keepFirstOption ? $select.find("option:first").clone() : null;
        $select.empty();
        if (first && first.length) {
            $select.append(first);
        }

        items.forEach((item) => {
            $select.append(new Option(item[textKey], item[valueKey]));
        });
    }
};

window.FormatUtils = FormatUtils;
window.TextParserUtils = TextParserUtils;
window.AppUI = AppUI;