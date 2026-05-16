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

        listarPresentaciones(categoria) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${BASE_URL}/ajax/formmulaAjax.php`,
                    method: "GET",
                    data: { accion: "listarPresentaciones", categoria: categoria },
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
                select.append(new Option(item[textKey], item[valueKey]));
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

        mostrarInsumosAcumulados(insumos) {
            const tbody = $("#tablaInsumosAcumulados tbody");
            if (insumos.length === 0) {
                tbody.html('<tr><td colspan="3" class="text-center text-muted">No hay insumos agregados</td></tr>');
                return;
            }
            tbody.html(insumos.map((insumo, index) => this.renderizarFilaInsumo(insumo, index)).join(''));
        },

        renderizarFilaFormula(formula) {
            return `
                <tr>
                    <td>${formula.nombre_formula}</td>
                    <td>${formula.presentacion_final}</td>
                    <td>${formula.insumos_utilizados}</td>
                </tr>
            `;
        },

        mostrarFormulas(formulas) {
            const tbody = $("#formulasTableBody");
            if (formulas.length === 0) {
                tbody.html('<tr><td colspan="5" class="text-center text-muted">No hay fórmulas registradas</td></tr>');
                return;
            }
            tbody.html(formulas.map(formula => this.renderizarFilaFormula(formula)).join(''));
            $("#cantidad-formulas").text(formulas.length);
        },

        limpiarFormulario() {
            $("#formularioRegistroFormula")[0].reset();
            $("#nombreFormula").val('');
            $("#presentacion").prop('selectedIndex', 0);
            $("#insumo").prop('selectedIndex', 0);
            $("#cantidadInsumo").val('');
            Module.insumosAcumulados = [];
            this.mostrarInsumosAcumulados([]);
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

            $(document).on("click", ".fa-circle-plus", () => {
                this.agregarInsumoAlAcumulador();
            });

            $(document).on("click", ".btn-eliminar-insumo", (e) => {
                const index = $(e.currentTarget).data("index");
                this.eliminarInsumo(index);
            });
        },

        async cargarDatos() {
            try {
                const [presentaciones, insumos, formulas] = await Promise.all([
                    API.listarPresentaciones("Lociones"),
                    API.listarInsumos(),
                    API.listarFormulas()
                ]);

                View.poblarSelect("#presentacion", presentaciones, "id_presentacion", "nombre_presentacion");
                View.poblarSelect("#insumo", insumos, "id_presentacion", "nombre_presentacion");
                View.mostrarFormulas(formulas);
            } catch (error) {
                Alerts.error("Error", "No se pudieron cargar los datos");
                console.error("Error al cargar datos:", error);
            }
        },

        agregarInsumoAlAcumulador() {
            const idInsumo = $("#insumo").val();
            const nombreInsumo = $("#insumo option:selected").text();
            const cantidad = $("#cantidadInsumo").val();

            if (!idInsumo || !cantidad) {
                Alerts.error("Campos incompletos", "Selecciona insumo y cantidad");
                return;
            }

            if (isNaN(cantidad) || parseFloat(cantidad) <= 0) {
                Alerts.error("Cantidad inválida", "La cantidad debe ser mayor a 0");
                return;
            }

            this.insumosAcumulados.push({
                id_presentacion_insumo: parseInt(idInsumo),
                nombre: nombreInsumo,
                cantidad_requerida: parseFloat(cantidad)
            });

            View.mostrarInsumosAcumulados(this.insumosAcumulados);
            $("#insumo").prop('selectedIndex', 0);
            $("#cantidadInsumo").val('');
            Alerts.toasSuccess("Insumo agregado");
        },

        eliminarInsumo(index) {
            this.insumosAcumulados.splice(index, 1);
            View.mostrarInsumosAcumulados(this.insumosAcumulados);
            Alerts.toasSuccess("Insumo eliminado");
        },

        async registrarFormula(e) {
            e.preventDefault();

            const nombreFormula = $("#nombreFormula").val().trim();
            const idPresentacionFinal = $("#presentacion").val();

            if (!nombreFormula || !idPresentacionFinal) {
                Alerts.error("Campos incompletos", "Nombre y presentación son obligatorios");
                return;
            }

            if (this.insumosAcumulados.length === 0) {
                Alerts.error("Sin insumos", "Debes agregar al menos un insumo");
                return;
            }

            const resultado = await Alerts.confirmation(
                "Registrar fórmula?",
                `¿Registrar la fórmula "${nombreFormula}" con ${this.insumosAcumulados.length} insumo(s)?`
            );

            if (!resultado.isConfirmed) return;

            try {
                const data = {
                    accion: "registrarFormula",
                    nombreFormula: nombreFormula,
                    idPresentacionFinal: idPresentacionFinal,
                    insumos: JSON.stringify(this.insumosAcumulados)
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
