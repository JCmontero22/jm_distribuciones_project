<section class="content-panel" id="informeProduccionModule">
    <div class="row mb-3">
        <div class="col-md-10">
            <h1>Informe de Producción</h1>
            <p class="text-muted">Lociones preparables según stock de esencias y fórmulas registradas:</p>
            <p class="text-muted small" id="resumenContador"></p>
            
        </div>
        <div class="col-md-2 text-end d-flex align-items-center justify-content-end gap-2">
            <button class="btn btnRegistro  btn-sm" id="btnRefrescarInforme">
                <i class="bi bi-arrow-clockwise "></i> Actualizar
            </button>
        </div>
    </div>

    <!-- Buscador -->
    <div class="row mb-5 d-flex align-items-center justify-content-center">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="buscadorEsencias" placeholder="Buscar esencia...">
            </div>
        </div>
        
    </div>

    <!-- Acordeones de esencias  -->
    <div id="contenedorEsencias" class="mb-5 container">
        <div class="text-muted fst-italic text-center py-4" style="background-color: red;">Cargando...</div>
    </div>
</section>
