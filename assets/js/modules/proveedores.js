/**
 * CAPA API — Solo responsable de comunicarse con el servidor
 * Los métodos aquí devuelven datos del servidor sin modificar
 */
const ProveedoresAPI = {
    listarProveedores() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/proveedorAjax.php",
                method: "GET",
                data: { accion: "listadoProveedoresSelect" },
                success(response) {
                    const datos = JSON.parse(response);
                    console.log(datos);
                    
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                }
            });
        });
    },

    registrarProveedor(formData) {
        return new Promise((resolve, reject) => {
            formData.append("accion", "registrarProveedor");
            $.ajax({
                url: "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/ajax/proveedorAjax.php",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
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
 * CAPA VIEW — Solo responsable de renderizar HTML y manipular el DOM
 * Recibe datos y los convierte en HTML, sin hacer lógica de negocio
 */
const ProveedoresView = {
    renderizarTablaProveedores(proveedores) {
        // Destruir tabla anterior si existe
        if ($.fn.DataTable.isDataTable("#tablaProveedores")) {
            $("#tablaProveedores").DataTable().destroy();
        }

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
            }
        });
    },

    limpiarFormulario(formId) {
        $(formId)[0].reset();
    }
};

/**
 * MÓDULO PROVEEDORES — Coordina API y View, maneja estado y lógica de negocio
 */
const proveedoresModule = {
    modalProveedor: null,

    init() {
        this.modalProveedor = new bootstrap.Modal(document.getElementById('modalRegistroProveedor'));

        this.bindEvents();
        this.cargarProveedores();
    },

    bindEvents() {
        $("#registroDeProveedor").on("submit", (e) => this.registrarProveedor(e));

        $('#modalRegistroProveedor').on('hidden.bs.modal', () => {
            ProveedoresView.limpiarFormulario("#registroDeProveedor");
        });
    },

    async cargarProveedores() {
        try {
            const proveedores = await ProveedoresAPI.listarProveedores();
            ProveedoresView.renderizarTablaProveedores(proveedores);
        } catch (error) {
            console.error("Error al cargar proveedores:", error);
            Alerts.error("Error", "No se pudieron cargar los proveedores");
        }
    },

    async registrarProveedor(e) {
        e.preventDefault();

        // Validación
        const formData = new FormData(e.target);
        if (!formData.get("nombre") || !formData.get("contacto") || !formData.get("telefono")) {
            Alerts.warning("Campos incompletos", "Por favor completa todos los campos");
            return;
        }

        // Confirmación
        const resultado = await Alerts.confirmation(
            "Registrar este proveedor?",
            "¿Estás seguro de que deseas registrar este proveedor?"
        );

        if (!resultado.isConfirmed) return;

        try {
            const respuesta = await ProveedoresAPI.registrarProveedor(new FormData(e.target));

            if (respuesta.success) {
                ProveedoresView.limpiarFormulario("#registroDeProveedor");
                this.modalProveedor.hide();
                await this.cargarProveedores();
                Alerts.toasSuccess(respuesta.message);
            } else {
                Alerts.error("Error al registrar", respuesta.message);
            }
        } catch (error) {
            Alerts.error("Error", "Error al registrar el proveedor");
            console.error(error);
        }
    }
};
