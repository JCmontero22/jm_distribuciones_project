const proveedoresModule = {
    modalProveedor: null,
    init() {
        this.modalProveedor = new bootstrap.Modal(document.getElementById('modalRegistroProveedor'));

        this.bindEvents();
        this.cargarProveedores();
        this.limpiarFormulario();
        
    },

    bindEvents() {
        $("#registroDeProveedor").on("submit", (e) => this.registrarProveedor(e));
    },

    registrarProveedor(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        if (!formData.get("nombre") || !formData.get("contacto") || !formData.get("telefono")) {
            Alerts.warning("Todos los campos son obligatorios", "Por favor, completa todos los campos antes de registrar el proveedor.");
            return;
        }

        Alerts.confirmation("Registrar este proveedor?", "¿Estás seguro de que deseas registrar este proveedor?").then((result) =>{
            if (result.isConfirmed) {
                formData.append("accion", "registrarProveedor");
                $.ajax({
                    url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/proveedorAjax.php",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success(response) {
                        const res = JSON.parse(response);

                        if (res.success) {
                            Alerts.toasSuccess(res.message);
                            proveedoresModule.cargarProveedores();
                            proveedoresModule.limpiarFormulario();
                        } else {
                            Alerts.error("Error al registrar proveedor", res.message);
                        }
                    },
                    error() {
                        alert("Error en la solicitud. Inténtalo de nuevo.");
                    }
                });
            }
        });
    },

    cargarProveedores() {
        $.ajax({
            url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/proveedorAjax.php",
            method: "GET",
            data: { accion: "listadoProveedores" },
            success(response) {
                const res = JSON.parse(response);
                if (res.success) {
                    proveedoresModule.renderizarTablaProveedores(res.data);
                } else {
                    alert("Error al cargar los proveedores. Inténtalo de nuevo.");
                }
                
            },
            error() {
                alert("Error al cargar los proveedores. Inténtalo de nuevo.");
            }
        });
    },

    renderizarTablaProveedores(proveedores) {
        $("#tablaProveedores").DataTable({
            data: proveedores,
            columns: [
                { data: "id_proveedor" },
                { data: "nombre_proveedor" },
                { data: "contacto_proveedor" },
                { data: "telefono_proveedor" }
             ],
             order: [[0, "desc"]],
            language: {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "aria": {
                    "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            destroy: true,
        });
    },

    limpiarFormulario() {
        $("#registroDeProveedor")[0].reset();
        proveedoresModule.modalProveedor.hide();

    }
}