/**
 * ============================================================
 * MÓDULO LOGIN
 *
 * Arquitectura en 3 capas:
 * - LoginAPI: Solo comunica con servidor
 * - LoginView: Renderiza HTML y manipula DOM
 * - LoginModule: Coordina lógica entre API y View
 * ============================================================
 */

// ============================================================
// CAPA API — Comunicación con servidor
// ============================================================
const LoginAPI = {
    loginClient: new SimpleAPI(CONFIG.AJAX.LOGIN),

    async validarCredenciales(formData) {
        return await this.loginClient.post(formData);
    },

    async logout() {
        const formData = new FormData();
        formData.append('accion', 'logout');
        return await this.loginClient.post(formData);
    }
};

// ============================================================
// CAPA VIEW — Renderización y manipulación del DOM
// ============================================================
const LoginView = {
    /**
     * Mostrar alerta de error
     */
    mostrarError(mensaje) {
        const container = document.getElementById('alertContainer');
        if (!container) return;

        const alertEl = document.createElement('div');
        alertEl.className = 'alert alert-danger';
        alertEl.innerHTML = `
            <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
            ${mensaje}
        `;

        container.innerHTML = '';
        container.appendChild(alertEl);
    },

    /**
     * Mostrar alerta de éxito
     */
    mostrarExito(mensaje) {
        const container = document.getElementById('alertContainer');
        if (!container) return;

        const alertEl = document.createElement('div');
        alertEl.className = 'alert alert-success';
        alertEl.innerHTML = `
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
            ${mensaje}
        `;

        container.innerHTML = '';
        container.appendChild(alertEl);

        setTimeout(() => alertEl.remove(), 3000);
    },

    /**
     * Habilitar/Deshabilitar botón de login
     */
    setBotonLoading(isLoading) {
        const btn = document.getElementById('loginBtn');
        if (!btn) return;

        if (isLoading) {
            btn.disabled = true;
            btn.classList.add('loading');
            btn.innerHTML = '<div class="loading-spinner"></div><span class="btn-text">Validando...</span>';
        } else {
            btn.disabled = false;
            btn.classList.remove('loading');
            btn.innerHTML = '<span class="btn-text">Iniciar Sesión</span>';
        }
    },

    /**
     * Limpiar formulario
     */
    limpiarFormulario() {
        const form = document.getElementById('loginForm');
        if (form) form.reset();
    }
};

// ============================================================
// CAPA MODULE — Lógica y coordinación
// ============================================================
const LoginModule = {

    /**
     * Inicializar eventos del módulo
     */
    init() {
        const form = document.getElementById('loginForm');
        this.setupTogglePassword();
        form.addEventListener('submit', (e) => this.handleLogin(e));
    },

    /**
     * Setup del toggle de contraseña
     */
    setupTogglePassword() {
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (!toggleBtn || !passwordInput) return;

        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleBtn.innerHTML = `<i class="fas fa-eye${isPassword ? '-slash' : ''}"></i>`;
        });
    },

    /**
     * Manejar submit del formulario de login
     */
    async handleLogin(e) {
        e.preventDefault();

        const formData = new FormData(e.target);

        // Validaciones
        if (!formData.get('usuario') || !formData.get('password')) {
            LoginView.mostrarError('Usuario y contraseña son obligatorios');
            return;
        }

        // Mostrar loading
        LoginView.setBotonLoading(true);

        try {
            formData.append('recordarme', formData.get('recordarme') ? '1' : '0');
            formData.append('accion', 'validar');
            const respuesta = await LoginAPI.validarCredenciales(formData);
            if (respuesta.success) {
                LoginView.mostrarExito('¡Bienvenido! Redirigiendo...');
                setTimeout(() => {
                    window.location.href = respuesta.data.redirect;
                }, 1500);
            } else {
                LoginView.mostrarError(respuesta.message || 'Error al iniciar sesión');
                LoginView.setBotonLoading(false);
            }
        } catch (error) {
            console.error('[LoginModule] Error:', error);
            LoginView.mostrarError('Error al conectar con el servidor: ' + error.message);
            LoginView.setBotonLoading(false);
        }
    }
};

// ============================================================
// INICIALIZACIÓN
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    LoginModule.init();
});
