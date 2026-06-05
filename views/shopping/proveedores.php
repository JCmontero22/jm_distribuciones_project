<section class="content-panel" id="moduloProveedores">
    <div class="row">
        <div class="col-md-10">
            <h1>Proveedores</h1>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-new-product" id="btnModalProveedor" data-bs-toggle="modal" data-bs-target="#modalRegistroProveedor"><i class="fa-solid fa-plus"></i> Nuevo Proveedor</button>
        </div>
    </div>
</section>

<section class="content-list-proveedores contenedor-page container">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover custom-table table-dark" id="tablaProveedores">
            <thead class="text-center">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>                    
                </tr>
            </thead>
            <tbody id="proveedoresTableBody">
                <!-- Aquí se llenarán los proveedores dinámicamente -->
            </tbody>
        </table>
    </div>
</section>


<!-- ************* MODAL DE REGISTRO DE PROVEEDORES ********** -->
<div class="modal fade" id="modalRegistroProveedor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRegistroProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalRegistroProveedorLabel">Registrar Nuevo Proveedor</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="registroDeProveedor">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nombre" id="inputNombre" placeholder="Nombre del proveedor" >
                        </div>

                        <div class="col-md-4">
                            <input type="text" class="form-control" name="contacto" id="inputContacto" placeholder="Contacto" >
                        </div>

                        <div class="col-md-4">
                            <input type="text" class="form-control" name="telefono" id="inputTelefono" placeholder="Teléfono" >
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center mt-5">
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-primary w-100 btnRegistro">Registrar Proveedor</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>