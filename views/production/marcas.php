<section class="content-panel" id="marcasModule">
    <div class="row">
        <div class="col-md-8">
            <h1>Inventario Marcas</h1>
            <p>(Cantidad de marcas: <span id="cantidad-marcas">0</span>)</p>
        </div>
    </div>
</section>



<section class="container" id="marcasContent">

    <div class="row mt-5">
        <div class="col-md-12">
            <h4 class="mb-3">Registrar Nueva Marca</h4>
            <form id="formularioRegistroMarca" class="mt-5">
                <input type="hidden" name="id_marca" id="id_marca">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nombreMarca" class="form-label">* Nombre de la Marca</label>
                        <input type="text" class="form-control" id="nombreMarca" name="nombreMarca" placeholder="Ej: Carolina Herrera" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="imagenMarca" class="form-label">* Imagen Marca</label>
                        <input type="file" class="form-control" id="imagenMarca" name="imagenMarca" accept="image/*">
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btnRegistro" id="btnRegistrarMarca">Registrar Marca</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="content-inventory-marcas table-responsive">
                <table id="tablaMarcas" class="table table-striped table-hover table-dark table-bordered table-sm custom-table">
                    <thead>
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Nombre Marca</th>
                            <th>Imagen Marca</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="marcasTableBody" class="text-center"></tbody>
                </table>
            </div>
        </div>
    </div>

    
</section>
