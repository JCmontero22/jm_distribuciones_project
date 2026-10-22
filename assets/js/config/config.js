/**
 * CONFIG - Configuración Frontend
 * PUNTO ÚNICO DE CAMBIO: BASE_URL (línea 10)
 */

const CONFIG = {
    // ============================================================
    // RUTA BASE - CAMBIAR PARA SERVIDOR
    // ============================================================
    // Localhost: '/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/'
    // Servidor:  '/jm_distribuciones/' o '/'
    // Base del proyecto calculada desde la URL de este mismo archivo (funciona en XAMPP y en producción)
    BASE_URL: (function () {
        const src = document.currentScript ? document.currentScript.src : '';
        const i = src.indexOf('/assets/js/config/');
        return i >= 0 ? src.substring(0, i + 1) : '/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/';
    })(),

    // ============================================================
    // ENDPOINTS AJAX
    // ============================================================
    AJAX: {
        LOGIN: 'ajax/login.php',
        PRODUCTOS: 'ajax/productosAjax.php',
        COMPRAS: 'ajax/comprasAjax.php',
        PROVEEDORES: 'ajax/proveedorAjax.php',
        CATALOGO: 'ajax/catalogoAjax.php',
        USUARIOS: 'ajax/usuariosAjax.php',
        FORMULAS: 'ajax/formulasAjax.php',
        PROMOCIONES: 'ajax/promocionesAjax.php',
        DESCUENTOS: 'ajax/descuentosAjax.php',
        INFORME_PRODUCCION: 'ajax/informeProduccionAjax.php',
        CONFIGURACION: 'ajax/configuracionAjax.php',
        VENTAS: 'ajax/ventasAjax.php',
        GASTOS: 'ajax/gastosAjax.php',
        HISTORIAL: 'ajax/historialAjax.php',
        BALANCE: 'ajax/balanceAjax.php',
    },

    // ============================================================
    // RUTAS DE ASSETS
    // ============================================================
    ASSETS: {
        IMG_PRODUCTOS: 'assets/img/productos/',
        IMG_BANNERS: 'assets/img/banners/',
    }
};

window.CONFIG = CONFIG;

