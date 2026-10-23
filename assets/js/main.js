/**
 * main.js - Inicialización global de la aplicación
 * Carga módulos, inicializa eventos, configura UI
 */

// ============================================================
// Variables globales y configuración
// ============================================================
const btn = document.getElementById('btnHamburger');
const sidebar = document.getElementById('sidebarCol');
const overlay = document.getElementById('sidebarOverlay');

const isMobile = () => window.innerWidth < 768;

// ============================================================
// Inicialización principal
// ============================================================
function init() {
    try {
        Logger.info('App initialized', { timestamp: new Date().toISOString() });

        // Inicializar módulos
        initializeModules();

        // Configurar UI
        setupUI();

        Logger.info('Initialization complete');

    } catch (error) {
        Logger.error('Initialization error', error);
        Alerts.error('Error', 'Error al inicializar la aplicación');
    }
}

/**
 * Inicializa todos los módulos disponibles
 */
function initializeModules() {
    // Módulos de inventario genéricos
    const inventoryModules = {
        relojesModule: "RelojesModule",
        esenciasModule: "EsenciasModule",
        insumosModule: "InsumosModule",
        locionesAAAModule: "LocionesAAAModule",
        descuentosModule: "DescuentosModule",
        ConfiguracionModule: "ConfiguracionModule"
    };

    Object.entries(inventoryModules).forEach(([elementId, moduleName]) => {
        const $element = $(`#${elementId}`);
        if ($element.length) {
            try {
                const configStr = $element.data("inventory-config");
                if (configStr) {
                    const config = typeof configStr === 'string' ? JSON.parse(configStr) : configStr;
                    window[moduleName] = crearModuloInventario(config);
                    window[moduleName].init();
                    Logger.debug(`Initialized module: ${moduleName}`);
                }
            } catch (error) {
                Logger.error(`Error initializing ${moduleName}`, error);
            }
        }
    });

    // Módulos específicos
    const modules = [
        { id: 'moduleCompras', name: 'comprasModule' },
        { id: 'moduleVentas', name: 'ventasModule' },
        { id: 'moduleGastos', name: 'gastosModule' },
        { id: 'moduleHistorial', name: 'historialModule' },
        { id: 'moduleBalance', name: 'balanceModule' },
        { id: 'moduloProveedores', name: 'proveedoresModule' },
        { id: 'formulasModule', name: 'formulasModule' },
        { id: 'descuentosModule', name: 'DescuentosModule' },
        { id: 'informeProduccionModule', name: 'InformeProduccionModule' },
        { id: 'marcasModule', name: 'marcasModule' },
        { id: 'bannerPromocionesModule', name: 'bannerPromocionesModule' },
        { id: 'ConfiguracionModule', name: 'ConfiguracionModule' },
        { id: 'dashboardHomeModule', name: 'homeDashboardModule' }
    ];

    modules.forEach(({ id, name }) => {
        if ($(`#${id}`).length && window[name]) {
            try {
                window[name].init();
                Logger.debug(`Initialized module: ${name}`);
            } catch (error) {
                Logger.error(`Error initializing ${name}`, error);
            }
        }
    });

    if (($('.btn-ver-presentaciones').length || $('#seccionPresentacionesExistentes').length) && window.inventarioPresentacionesModule) {
        try {
            window.inventarioPresentacionesModule.init();
            Logger.debug('Initialized module: inventarioPresentacionesModule');
        } catch (error) {
            Logger.error('Error initializing inventarioPresentacionesModule', error);
        }
    }
}

/**
 * Configura la interfaz de usuario
 */
function setupUI() {
    setActiveMenu();
    setupMenuHamburguer();
    setupGlobalErrorHandling();

    // Limpiar cualquier backdrop huérfano cuando se cierra o se abre un modal Bootstrap
    document.addEventListener('hidden.bs.modal', () => {
        if (typeof window.limpiarFondosModales === 'function') {
            window.limpiarFondosModales();
        }
    });
    document.addEventListener('shown.bs.modal', () => {
        if (typeof window.limpiarFondosModales === 'function') {
            window.limpiarFondosModales();
        }
    });
}

/**
 * Marca el menú actual como activo
 */
function setActiveMenu() {
    const path = (typeof currentPage !== 'undefined') ? currentPage : 'home';

    document.querySelectorAll('.ul-list__item[onclick]').forEach(item => {
        item.classList.remove('active');
        const match = item.getAttribute('onclick').match(/redirect\(['"](.+)['"]\)/);

        if (match && match[1] === path) {
            item.classList.add('active');
            const collapse = item.closest('.collapse');
            if (collapse) {
                collapse.classList.add('show');
                const trigger = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });

    // Cerrar cualquier otro submenú abierto cuando se abre uno nuevo
    const collapses = document.querySelectorAll('#sidebarMenuAccordion > .collapse');
    collapses.forEach(col => {
        col.addEventListener('show.bs.collapse', function () {
            collapses.forEach(other => {
                if (other !== col && other.classList.contains('show')) {
                    bootstrap.Collapse.getOrCreateInstance(other, { toggle: false }).hide();
                }
            });
        });
    });
}

/**
 * Redirige a una vista
 */
function redirect(vista) {
    window.location.href = CONFIG.BASE_URL + vista;
}

/**
 * Configura el menú hamburguesa
 */
function setupMenuHamburguer() {
    if (!btn || !sidebar || !overlay) {
        Logger.warning('Hamburger menu elements not found');
        return;
    }

    btn.addEventListener('click', function () {
        if (isMobile()) {
            sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile();
        } else {
            toggleDesktop();
        }
    });

    overlay.addEventListener('click', closeMobile);

    // Limpiar estados al redimensionar
    window.addEventListener('resize', function () {
        if (!isMobile()) {
            closeMobile();
        }
    });
}

/**
 * Abre menú en móvil
 */
function openMobile() {
    sidebar.classList.add('mobile-open');
    overlay.classList.add('active');
    btn.classList.add('open');
}

/**
 * Cierra menú en móvil
 */
function closeMobile() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
    btn.classList.remove('open');
}

/**
 * Toggle menú en desktop
 */
function toggleDesktop() {
    sidebar.classList.toggle('collapsed');
}

/**
 * Manejo global de errores no capturados
 */
function setupGlobalErrorHandling() {
    window.addEventListener('error', (event) => {
        Logger.error('Uncaught error', {
            message: event.message,
            file: event.filename,
            line: event.lineno,
            column: event.colno
        });
    });

    window.addEventListener('unhandledrejection', (event) => {
        Logger.error('Unhandled promise rejection', {
            reason: event.reason
        });
    });
}

// ============================================================
// Iniciar aplicación cuando DOM esté listo
// ============================================================
document.addEventListener('DOMContentLoaded', init);
