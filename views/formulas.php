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
                        <tr>
                            <th>ID Fórmula</th>
                            <th>Nombre Fórmula</th>
                            <th>Código Fórmula</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="formulasTableBody">
                        
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
                    <div class="col-md-6">
                        <label for="nombreFormula" class="form-label">Nombre de la Fórmula</label>
                        <input type="text" class="form-control" id="nombreFormula" name="nombreFormula" placeholder="Ej: Fórmula X">
                    </div>

                    <div class="col-md-6">
                        <label for="codigoFormula" class="form-label">Seleccione la presentación</label>
                        <select name="presentacion" id="presentacion" class="form-select">
                            <option value="">Seleccione la presentación</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-12">
                        <label for="descripcionFormula" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionFormula" name="descripcionFormula" rows="3" placeholder="Descripción de la fórmula..."></textarea>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-7">
                        <label for="insumo" class="form-label">Seleccione insumo</label>
                        <select name="insumo" id="insumo" class="form-select">
                            <option value="">Seleccione el insumo</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="cantidadInsumo" class="form-label">Cantidad del insumo</label>
                        <input type="number" class="form-control" id="cantidadInsumo" name="cantidadInsumo" placeholder="Ej: 100 ml">
                    </div>

                    <div class="col-md-1 d-flex justify-content-center align-items-center">
                        <i class="fa-solid fa-circle-plus" style="font-size: 3rem; cursor: pointer; margin-top: 2rem; color: var(--color-dorado);"></i>
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

