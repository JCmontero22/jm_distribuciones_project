/**
 * MÓDULO FORMULAS — Gestión de fórmulas de producción
 * Permite registrar recetas que indican qué insumos y cantidades se usan para producir lociones
 */

const formulasModule = (function() {
    const BASE_URL = "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project";

    /**
     * CAPA API — Comunicación con el servidor
     */
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
                    error(error) {
                        reject(error);
                    }
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
                        const datos = JSON.parse(response);
                        resolve(datos.data || []);
                    },
                    error(error) {
                        reject(error);
                    }
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
                        const res = JSON.parse(response);
                        console.log(res);
                        
                        resolve(res);
                    },
                    error(error) {
                        reject(error);
                    }
                });
            });
        }
    };

    /**
     * CAPA VIEW — Renderización del HTML
     */
    const View = {
        poblarSelect(selectId, items, valueKey, textKey) {
            const select = $(selectId);
            select.find('option:not(:first)').remove();
            items.forEach(item => {
                let text;
                if (textKey) {
                    text = item[textKey];
                } else {
                    // Construir: Producto - Presentación
                    text = `${item.nombre_producto} / ${item.nombre_presentacion}`;
                }
                select.append(new Option(text, item[valueKey]));
            });
        },

        renderizarFilaInsumo(insumo, index) {
            return `
                <tr>
                    <td>${insumo.nombre}</td>
                    <td>${insumo.cantidad_requerida} ml</td>
                    <td>
                        <button class="btn btn-sm btn-danger btn-eliminar-insumo" data-index="${index}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        },

        renderizarFilaFormula(formula) {
            return `
                <tr>
                    <td>${formula.id_formula}</td>
                    <td>${formula.nombre_formula}</td>
                    <td>${formula.cantidad_esencia}G</td>
                    <td>${formula.nombre_presentacion}</td>
                </tr>
            `;
        },

        mostrarFormulas(formulas) {
            const tbody = $("#formulasTableBody");
            if (formulas.length === 0) {
                tbody.html('<tr><td colspan="4" class="text-center text-muted">No hay fórmulas registradas</td></tr>');
                return;
            }
            tbody.html(formulas.map(formula => this.renderizarFilaFormula(formula)).join(''));
            $("#cantidad-formulas").text(formulas.length);
        },

        limpiarFormulario() {
            $("#formularioRegistroFormula")[0].reset();
            $("#nombreFormula").val('');
            $("#cantidadEsencia").val('');
            $("#insumo").prop('selectedIndex', 0);
            $("#cantidadInsumo").val('');
        }
    };

    /**
     * MÓDULO PRINCIPAL — Lógica y coordinación
     */
    const Module = {
        insumosAcumulados: [],

        init() {
            this.bindEvents();
            this.cargarDatos();
        },

        bindEvents() {
            $("#formularioRegistroFormula").on("submit", (e) => this.registrarFormula(e));
        },

        async cargarDatos() {
            try {
                const [presentaciones, insumos, formulas] = await Promise.all([
                    this.listarInsumos(),
                    this.listarFormulas()
                ]);
            } catch (error) {
                Alerts.error("Error", "No se pudieron cargar los datos");
                console.error("Error al cargar datos:", error);
            }
        },

        async listarInsumos() {
            try {
                const insumos = await API.listarInsumos();
                View.poblarSelect("#insumo", insumos, "id_presentacion", null);
            } catch (error) {
                Alerts.error("Error", "No se pudieron cargar los insumos");
                console.error("Error al listar insumos:", error);
                return [];
            }
        },

        async listarFormulas() {
            try {
                const formulas = await API.listarFormulas();
                View.mostrarFormulas(formulas);
            } catch (error) {
                Alerts.error("Error", "No se pudieron cargar las fórmulas");
                console.error("Error al listar fórmulas:", error);
                return [];
            }
        },

        
        async registrarFormula(e) {
            e.preventDefault();

            const nombreFormula = $("#nombreFormula").val().trim();
            const cantidadEsencia = $("#cantidadEsencia").val();
            const insumo = $("#insumo").val();

            if (!nombreFormula || !cantidadEsencia || !insumo) {
                Alerts.error("Campos incompletos", "Nombre, cantidad de esencia e insumo son obligatorios");
                return;
            }

           
            const resultado = await Alerts.confirmation(
                "Registrar fórmula?",
                `¿Registrar la fórmula "${nombreFormula}" con ${cantidadEsencia} de esencia y ${$("#insumo option:selected").text()} insumo?`
            );

            if (!resultado.isConfirmed) return;

            try {
                const data = {
                    accion: "registrarFormula",
                    nombreFormula: nombreFormula,
                    cantidadEsencia: cantidadEsencia,
                    insumo: insumo,
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
