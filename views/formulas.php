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
                <table class="table table-striped table-hover">
                    <thead>
                        <tr class="text-center">
                            <th>ID Fórmula</th>
                            <th>Nombre Fórmula</th>
                            <th>Cantidad Esencia</th>
                            <th>Insumo</th>
                        </tr>
                    </thead>
                    <tbody id="formulasTableBody" class="text-center">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <!-- <h3>Registrar Nueva Fórmula</h3> -->
            <form id="formularioRegistroFormula">
                <div class="row mt-5">
                    <div class="col-md-4">
                        <label for="nombreFormula" class="form-label">* Nombre de la Fórmula</label>
                        <input type="text" class="form-control" id="nombreFormula" name="nombreFormula" placeholder="Ej: Fórmula X">
                    </div>

                    <div class="col-md-2">
                        <label for="cantidadEsencia" class="form-label">* Cantidad de Esencia</label>
                        <input type="text" class="form-control" id="cantidadEsencia" name="cantidadEsencia" placeholder="Ej: 100G">
                    </div>

                    <div class="col-md-6">
                        <label for="insumo" class="form-label">* Seleccione insumo</label>
                        <select name="insumo" id="insumo" class="form-select">
                            <option value="">Seleccione el insumo</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btnRegistro">Registrar Fórmula</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

