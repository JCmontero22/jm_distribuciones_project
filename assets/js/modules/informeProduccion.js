const InformeProduccionAPI = {
    ventasClient: new SimpleAPI(CONFIG.AJAX.VENTAS),

    async obtenerPrecios(idSede) {
        const response = await this.ventasClient.get({ accion: "preciosLocion", idSede });
        return response.data || [];
    },

    async guardarPrecio(payload) {
        return await this.ventasClient.post({ accion: "guardarPrecioLocion", ...payload });
    },

    client: new SimpleAPI(CONFIG.AJAX.INFORME_PRODUCCION),
    catalogoClient: new SimpleAPI(CONFIG.AJAX.CATALOGO),

    async obtenerSedes() {
        return await this.catalogoClient.get({ accion: "listadoSedes" });
    },

    async obtenerDatos(idSede) {
        return await this.client.get({ accion: "obtenerDatosInforme", idSede });
    }
};

const InformeProduccionView = {
    renderizarEsencias(esencias, formulas, capacidades, precios = {}, puedeEditarPrecios = true) {
        const container = $('#contenedorEsencias');

        if (!esencias.length) {
            container.html('<div class="text-muted fst-italic text-center py-4">Sin stock de esencias registrado</div>');
            return;
        }

        const accordionId = 'informeProduccionAccordion';

        const html = esencias.map((esencia, idx) => {
            const idAccordion = `esencia-acc-${idx}`;
            const headerId = `esencia-heading-${idx}`;
            const gramos = parseFloat(esencia.total_gramos).toLocaleString('es-CO', { maximumFractionDigits: 0 });

            const filasFormulas = formulas.map(formula => {
                const cantidad = capacidades[formula.id_formula]?.[esencia.id_producto] ?? 0;
                const precio = precios[`${esencia.id_producto}-${formula.id_formula}`] ?? '';
                const frasco   = formula.nombre_insumo   || '—';
                const tamaño   = formula.tamanio_insumo  || '';
                const conc     = formula.nombre_concentracion || '—';

                const badgeClass = cantidad === 0 ? 'bg-danger'
                                 : cantidad < 5  ? 'bg-warning text-dark'
                                 :                 'bg-success';

                return `
                    <tr>
                        <td class="text-start ps-3">
                            ${frasco}
                            ${tamaño ? `<span class="badge bg-secondary ms-1">${tamaño}</span>` : ''}
                        </td>
                        <td>${conc}</td>
                        <td>${formula.cantidad_esencia} g</td>
                        <td>
                            <span class="badge ${badgeClass} px-3 py-2 fs-6">${cantidad}</span>
                        </td>
                        <td>
                            <input type="number" min="0" step="100" class="form-control form-control-sm text-center input-precio-locion"
                                   data-esencia="${esencia.id_producto}" data-formula="${formula.id_formula}"
                                   value="${precio}" placeholder="Sin precio" ${puedeEditarPrecios ? '' : 'disabled'}>
                        </td>
                    </tr>
                `;
            }).join('');

            return `
                <div class="accordion-item mb-2 border rounded acordion" data-nombre="${esencia.nombre_producto.toLowerCase()}">
                    <h2 class="accordion-header" id="${headerId}">
                        <button class="accordion-button collapsed py-3" type="button"
                                data-bs-toggle="collapse" data-bs-target="#${idAccordion}"
                                aria-expanded="false" aria-controls="${idAccordion}">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <i class="bi bi-droplet-half fs-5 text-primary"></i>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold">${esencia.nombre_producto}</span>
                                </div>
                                <span class="badge bg-primary me-3">${gramos} g disponibles</span>
                            </div>
                        </button>
                    </h2>
                    <div id="${idAccordion}" class="accordion-collapse collapse" data-bs-parent="#${accordionId}" aria-labelledby="${headerId}">
                        <div class="accordion-body p-0">
                            <table class="table table-sm table-hover mb-0 text-center align-middle custom-table table-dark">
                                <thead class="">
                                    <tr>
                                        <th class="text-start ps-3" style="width:35%">Frasco</th>
                                        <th style="width:20%">Concentración</th>
                                        <th style="width:15%">Gramos</th>
                                        <th style="width:15%">Lociones posibles</th>
                                        <th style="width:15%">Precio venta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${filasFormulas || '<tr><td colspan="5" class="text-muted fst-italic py-3">Sin fórmulas registradas</td></tr>'}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.html(`<div class="accordion accordion-flush prueba" id="${accordionId}">${html}</div>`);
        $('#resumenContador').html(`<i class="bi bi-info-circle"></i> ${esencias.length} esencia(s) con stock`);
    },

    filtrar(termino) {
        const items = $('#contenedorEsencias [data-nombre]');
        let visibles = 0;
        items.each(function () {
            const nombre = $(this).data('nombre');
            const coincide = nombre.includes(termino.toLowerCase());
            $(this).toggle(coincide);
            if (coincide) visibles++;
        });
        $('#resumenContador').html(`<i class="bi bi-info-circle"></i> ${visibles} esencia(s) mostradas`);
    },

    mostrarCargando() {
        $('#contenedorEsencias').html('<div class="text-muted fst-italic text-center py-4">Cargando...</div>');
        $('#resumenContador').html('');
    },

    mostrarError(msg) {
        $('#contenedorEsencias').html(`<div class="alert alert-danger">${msg}</div>`);
    }
};

const InformeProduccionModule = {
    async init() {
        await this.cargarSedes();
        $('#btnRefrescarInforme').on('click', () => this.cargarDatos());
        $('#selectSedeInforme').on('change', () => this.cargarDatos());
        $(document).on('input', '#buscadorEsencias', function () {
            InformeProduccionView.filtrar($(this).val());
        });
        $(document).on('change', '.input-precio-locion', (e) => this.guardarPrecio($(e.currentTarget)));
        this.cargarDatos();
    },

    async cargarSedes() {
        try {
            const res = await InformeProduccionAPI.obtenerSedes();
            const sedes = res.data || [];
            const $select = $('#selectSedeInforme');
            sedes.forEach(s => $select.append(new Option(s.nombre_sede, s.id_sede)));
            if (sedes.length > 0) {
                $select.val(sedes[0].id_sede);
            }
        } catch (err) {
            Logger.error('Error al cargar sedes del informe', err);
        }
    },

    async guardarPrecio($input) {
        const valor = parseFloat($input.val());
        if (!valor || valor <= 0) {
            Alerts.warning("Precio inválido", "Ingresa un precio mayor a 0");
            return;
        }
        try {
            const res = await InformeProduccionAPI.guardarPrecio({
                idSede: $('#selectSedeInforme').val(),
                idProductoEsencia: $input.data('esencia'),
                idFormula: $input.data('formula'),
                precio: valor
            });
            if (!res.success) {
                Alerts.error("Error", res.message || "No se pudo guardar el precio");
            }
        } catch (err) {
            Logger.error('Error al guardar precio de loción', err);
            Alerts.error("Error", "No se pudo guardar el precio");
        }
    },

    async cargarDatos() {
        const idSede = $('#selectSedeInforme').val();
        /* if (!idSede) {
            InformeProduccionView.mostrarError('Selecciona una sede');
            return;
        } */

        InformeProduccionView.mostrarCargando();
        $('#buscadorEsencias').val('');

        try {
            const [res, preciosRes] = await Promise.all([
                InformeProduccionAPI.obtenerDatos(idSede),
                InformeProduccionAPI.obtenerPrecios(idSede)
            ]);
            if (!res.success) {
                InformeProduccionView.mostrarError(res.message || 'Error al cargar datos');
                return;
            }

            const { esencias, formulas, capacidades, puedeEditarPrecios } = res.data;
            const precios = {};
            preciosRes.forEach(p => { precios[`${p.id_producto_esencia}-${p.id_formula}`] = parseFloat(p.precio_venta); });
            InformeProduccionView.renderizarEsencias(esencias, formulas, capacidades, precios, puedeEditarPrecios !== false);
        } catch (err) {
            Logger.error('Error al cargar informe de produccion', err);
            InformeProduccionView.mostrarError('Error de conexión al cargar el informe');
        }
    }
};

window.InformeProduccionModule = InformeProduccionModule;
