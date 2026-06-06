/**
 * Módulo para gestionar presentaciones de productos
 * Permite agregar, editar y eliminar presentaciones en el inventario
 */

class PresentacionesModule {
    constructor(modalSelector = '#modalRegistroPresentacion') {
        this.presentaciones = [];
        this.indexEditando = null;
        this.modal = document.querySelector(modalSelector);

        if (!this.modal) {
            console.error('Modal no encontrado:', modalSelector);
            return;
        }

        this.formulario = this.modal.querySelector('#formRegistroPresentacionProducto');
        this.btnAgregar = this.modal.querySelector('#btnAgregarStock');
        this.tabla = this.modal.querySelector('#tablaPresentaciones');

        if (!this.formulario || !this.btnAgregar || !this.tabla) {
            console.error('Elementos del formulario no encontrados', {
                formulario: !!this.formulario,
                btnAgregar: !!this.btnAgregar,
                tabla: !!this.tabla
            });
            return;
        }

        this.inicializar();
    }

    inicializar() {
        if (!this.btnAgregar || !this.formulario) {
            console.error('No se puede inicializar el módulo');
            return;
        }

        // Remover event listeners previos para evitar duplicados
        const nuevoBoton = this.btnAgregar.cloneNode(true);
        this.btnAgregar.parentNode.replaceChild(nuevoBoton, this.btnAgregar);
        this.btnAgregar = nuevoBoton;

        // Agregar listener con alta prioridad para capturar el evento antes que otros
        this.btnAgregar.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.manejarAgregar(e);
        }, true); // useCapture = true para capturar en la fase de captura

        this.formulario.addEventListener('reset', () => this.cancelarEdicion());

        console.log('PresentacionesModule inicializado correctamente');
    }

    manejarAgregar(e) {
        try {
            const datos = this.obtenerDatosFormulario();

            if (!this.validar(datos)) {
                return;
            }

            if (this.indexEditando !== null) {
                this.presentaciones[this.indexEditando] = datos;
                this.cancelarEdicion();
            } else {
                this.presentaciones.push(datos);
                this.limpiarFormulario();
            }

            this.renderizarTabla();
            console.log('Presentación guardada', { total: this.presentaciones.length });
        } catch (error) {
            console.error('Error al guardar presentación:', error);
            alert('Error: ' + error.message);
        }
    }

    obtenerDatosFormulario() {
        return {
            nombre: this.formulario.querySelector('#nombrePresentacion').value.trim(),
            codigo: this.formulario.querySelector('#codigoPresentacion').value.trim(),
            precioCompra: this.formulario.querySelector('#precioCompraPresentacion').value.trim(),
            precioVenta: this.formulario.querySelector('#precioVentaPresentacion').value.trim(),
            tipo: this.formulario.querySelector('#tipoProducto').value,
            unidad: this.formulario.querySelector('#unidadMedidaPresentacion').value,
            genero: this.formulario.querySelector('#generoPresentacion').value,
        };
    }

    validar(datos) {
        if (!datos.nombre || !datos.nombre.trim()) {
            alert('El campo "Nombre" es obligatorio');
            return false;
        }
        if (!datos.codigo || !datos.codigo.trim()) {
            alert('El campo "Código" es obligatorio');
            return false;
        }
        return true;
    }

    renderizarTabla() {
        if (this.presentaciones.length === 0) {
            this.tabla.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay presentaciones agregadas</td>
                </tr>
            `;
            return;
        }

        this.tabla.innerHTML = this.presentaciones.map((p, index) => `
            <tr data-index="${index}">
                <td>${p.nombre}</td>
                <td>${p.codigo}</td>
                <td>${p.precioCompra || '-'}</td>
                <td>${p.precioVenta || '-'}</td>
                <td>${p.tipo || '-'}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning me-2" onclick="presentacionesModulo.editar(${index})" title="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="presentacionesModulo.eliminar(${index})" title="Eliminar">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    editar(index) {
        const p = this.presentaciones[index];

        this.formulario.querySelector('#nombrePresentacion').value = p.nombre;
        this.formulario.querySelector('#codigoPresentacion').value = p.codigo;
        this.formulario.querySelector('#precioCompraPresentacion').value = p.precioCompra;
        this.formulario.querySelector('#precioVentaPresentacion').value = p.precioVenta;
        this.formulario.querySelector('#tipoProducto').value = p.tipo;
        this.formulario.querySelector('#unidadMedidaPresentacion').value = p.unidad;
        this.formulario.querySelector('#generoPresentacion').value = p.genero;

        this.indexEditando = index;
        this.btnAgregar.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Guardar Cambios';
        this.btnAgregar.classList.add('btn-info');
        this.btnAgregar.classList.remove('btn-primary');

        // Scroll al formulario
        this.formulario.scrollIntoView({ behavior: 'smooth' });
    }

    eliminar(index) {
        if (confirm('¿Estás seguro de que deseas eliminar esta presentación?')) {
            this.presentaciones.splice(index, 1);
            this.renderizarTabla();

            if (this.indexEditando === index) {
                this.cancelarEdicion();
            }
        }
    }

    cancelarEdicion() {
        this.indexEditando = null;
        this.limpiarFormulario();
        this.btnAgregar.innerHTML = '<i class="fa-solid fa-plus"></i> Agregar';
        this.btnAgregar.classList.remove('btn-info');
        this.btnAgregar.classList.add('btn-primary');
    }

    limpiarFormulario() {
        this.formulario.reset();
    }

    obtenerPresentaciones() {
        return this.presentaciones;
    }

    resetear() {
        this.presentaciones = [];
        this.indexEditando = null;
        this.renderizarTabla();
        this.limpiarFormulario();
    }
}

// Variable global para acceso desde HTML
let presentacionesModulo;
