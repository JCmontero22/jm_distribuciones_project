const formulasModule = (function() {
    const BASE_URL = "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project";

    const API = {
        listarFormulas() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/formmulaAjax.php`,
                    method: "GET",
                    data: { accion: "listarFormulas" },
                    success(response) {
                        const datos = JSON.parse(response);
                        resolve(datos.data || []);
                    },
                    error: reject
                });
            });
        },

        listarInsumos() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/formmulaAjax.php`,
                    method: "GET",
                    data: { accion: "listarInsumos" },
                    success(response) {
                        resolve(JSON.parse(response));
                    },
                    error: reject
                });
            });
        },

        listarConcentraciones() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/formmulaAjax.php`,
                    method: "GET",
                    data: { accion: "listarConcentraciones" },
                    success(response) {
                        resolve(JSON.parse(response));
                    },
                    error: reject
                });
            });
        },

        registrarFormula(data) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/formmulaAjax.php`,
                    method: "POST",
                    data: data,
                    success(response) {
                        resolve(JSON.parse(response));
                    },
                    error: reject
                });
            });
        }
    };

    /*************** VIEWS ***************/

    const View = {
        poblarSelect(selectId, items, valueKey, textFn) {
            const select = $(selectId);
            select.find('option:not(:first)').remove();
            items.forEach(item => {
                select.append(new Option(textFn(item)));
            });
        },

        renderizarFilaFormula(formula) {
            const frasco        = formula.nombre_insumo  || '—';
            const tamaño        = formula.tamanio_insumo || '';
            const concentracion = formula.nombre_concentracion || '—';
            return `
                <tr>
                    <td>${formula.id_formula}</td>
                    <td>${formula.nombre_formula}</td>
                    <td>${formula.cantidad_esencia} g</td>
                    <td>${frasco}</td>
                    <td>${concentracion}</td>
                    
                </tr>
            `;
        },

        mostrarFormulas(formulas) {
            const tbody = $("#formulasTableBody");
            if (!formulas.length) {
                tbody.html('<tr><td colspan="5" class="text-center text-muted">No hay fórmulas registradas</td></tr>');
                return;
            }
            tbody.html(formulas.map(f => this.renderizarFilaFormula(f)).join(''));
            $("#cantidad-formulas").text(formulas.length);
        },

        limpiarFormulario() {
            $("#formularioRegistroFormula")[0].reset();
            $("#insumo").prop('selectedIndex', 0);
            $("#concentracion").prop('selectedIndex', 0);
        }
    };

    const Module = {
        init() {
            this.bindEvents();
            this.cargarDatos();
        },

        bindEvents() {
            $("#formularioRegistroFormula").on("submit", (e) => this.registrarFormula(e));
        },

        async cargarDatos() {
            try {
                const insumos = await API.listarInsumos();
                View.poblarSelect("#insumo", insumos, "id_insumo_formula",
                    item => `${item.nombre_insumo}`
                );
            } catch (e) { console.error("Error al cargar insumos:", e); }

            try {
                const concentraciones = await API.listarConcentraciones();
                console.log("Concentraciones recibidas:", concentraciones);
                View.poblarSelect("#concentracion", concentraciones, "id_tipo_concentracion",
                    item => item.nombre_concentracion
                );
            } catch (e) { console.error("Error al cargar concentraciones:", e); }

            try {
                const formulas = await API.listarFormulas();
                View.mostrarFormulas(formulas);
            } catch (e) { console.error("Error al cargar fórmulas:", e); }
        },

        async registrarFormula(e) {
            e.preventDefault();

            const nombreFormula   = $("#nombreFormula").val().trim();
            const cantidadEsencia = $("#cantidadEsencia").val();
            const insumo          = $("#insumo").val();
            const concentracion   = $("#concentracion").val();

            if (!nombreFormula || !cantidadEsencia || !insumo || !concentracion) {
                Alerts.error("Campos incompletos", "Todos los campos son obligatorios");
                return;
            }

            const insumoText       = $("#insumo option:selected").text();
            const concentracionText= $("#concentracion option:selected").text();

            const resultado = await Alerts.confirmation(
                "¿Registrar fórmula?",
                `"${nombreFormula}" — ${cantidadEsencia}g · ${insumoText} · ${concentracionText}`
            );

            if (!resultado.isConfirmed) return;

            try {
                const data = {
                    accion:           "registrarFormula",
                    nombreFormula:    nombreFormula,
                    cantidadEsencia:  cantidadEsencia,
                    insumo:           insumo,
                    concentracion:    concentracion,
                };

                const respuesta = await API.registrarFormula(data);

                if (respuesta.success) {
                    Alerts.toasSuccess(respuesta.message);
                    View.limpiarFormulario();
                    await this.cargarDatos();
                } else {
                    Alerts.error("Error", respuesta.message);
                }
            } catch (error) {
                Alerts.error("Error", "Error al registrar la fórmula");
                console.error("Error:", error);
            }
        }
    };

    return Module;
})();
