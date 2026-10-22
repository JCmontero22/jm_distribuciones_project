<section class="content-panel" id="moduleGastos">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1>Gastos</h1>
            <p class="text-muted">Gastos operativos por sede (arriendo, servicios, transporte, etc.). Cada gasto descuenta de la caja de la sede.</p>
        </div>
        <div class="col-md-4 text-end">
            <span class="text-muted small d-block">Total del período</span>
            <span class="badge bg-warning text-dark fs-5" id="totalGastosPeriodo">$ 0</span>
        </div>
    </div>

    <div class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
            <label for="filtroSedeGastos" class="form-label">Sede</label>
            <select class="form-select" id="filtroSedeGastos"></select>
        </div>
        <div class="col-md-3">
            <label for="filtroDesdeGastos" class="form-label">Desde</label>
            <input type="date" class="form-control" id="filtroDesdeGastos">
        </div>
        <div class="col-md-3">
            <label for="filtroHastaGastos" class="form-label">Hasta</label>
            <input type="date" class="form-control" id="filtroHastaGastos">
        </div>
        <div class="col-md-3">
            <button class="btn btnRegistro w-100" id="btnFiltrarGastos">Filtrar</button>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table id="tablaGastos" class="table table-striped table-hover table-bordered custom-table table-dark text-center align-middle">
            <thead class="text-center">
                <tr>
                    <th>Fecha</th>
                    <th>Sede</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Método de pago</th>
                    <th>Monto</th>
                    <th>Registró</th>
                </tr>
            </thead>
            <tbody id="tablaGastosBody"></tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h4 class="mb-3">Registrar gasto</h4>
            <form id="formRegistroGasto">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="sedeGasto" class="form-label">* Sede</label>
                        <select class="form-select" id="sedeGasto" name="idSede" required></select>
                    </div>
                    <div class="col-md-2">
                        <label for="fechaGasto" class="form-label">* Fecha</label>
                        <input type="date" class="form-control" id="fechaGasto" name="fechaGasto" required>
                    </div>
                    <div class="col-md-3">
                        <label for="categoriaGasto" class="form-label">* Categoría</label>
                        <input type="text" class="form-control" id="categoriaGasto" name="categoriaGasto" maxlength="100" placeholder="Ej: Arriendo, Servicios, Transporte" required>
                    </div>
                    <div class="col-md-2">
                        <label for="montoGasto" class="form-label">* Monto</label>
                        <input type="number" class="form-control" id="montoGasto" name="montoGasto" min="1" step="1" placeholder="0" required>
                    </div>
                    <div class="col-md-2">
                        <label for="metodoPagoGasto" class="form-label">Método de pago</label>
                        <select class="form-select" id="metodoPagoGasto" name="idMetodoPago"></select>
                    </div>
                    <div class="col-md-12">
                        <label for="descripcionGasto" class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="descripcionGasto" name="descripcionGasto" maxlength="255" placeholder="Detalle opcional">
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btnRegistro">Registrar gasto</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
