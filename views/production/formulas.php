<section class="content-panel" id="formulasModule">
    <div class="row">
        <div class="col-md-8">
            <h1>Inventario Fórmulas</h1>
            <p>(Cantidad de fórmulas: <span id="cantidad-formulas">0</span>)</p>
        </div>
    </div>
</section>

<section class="container" id="formulasContent">
    <div class="row">
        <div class="col-md-12">
            <div class="content-inventory-formulas table-responsive">
                <table id="tablaFormulas" class="table table-striped table-hover table-dark table-bordered table-sm custom-table">
                    <thead>
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Nombre Fórmula</th>
                            <th>Gramos</th>
                            <th>Frasco</th>
                            <th>Concentración</th>                            
                        </tr>
                    </thead>
                    <tbody id="formulasTableBody" class="text-center"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h4 class="mb-3">Registrar Nueva Fórmula</h4>
            <form id="formularioRegistroFormula" class="mt-5">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nombreFormula" class="form-label">* Nombre de la Fórmula</label>
                        <input type="text" class="form-control" id="nombreFormula" name="nombreFormula" placeholder="Ej: 30ml Normal">
                    </div>

                    <div class="col-md-2">
                        <label for="cantidadEsencia" class="form-label">* Gramos de Esencia</label>
                        <input type="number" class="form-control" id="cantidadEsencia" name="cantidadEsencia" placeholder="Ej: 30" min="1">
                    </div>

                    <div class="col-md-3">
                        <label for="insumo" class="form-label">* Frasco (Insumo)</label>
                        <select name="insumo" id="insumo" class="form-select">
                            <option value="">Seleccione el frasco</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="concentracion" class="form-label">* Concentración</label>
                        <select name="concentracion" id="concentracion" class="form-select">
                            <option value="">Seleccione la concentración</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btnRegistro">Registrar Fórmula</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
