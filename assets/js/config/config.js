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
    BASE_URL: '/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project/',

    // ============================================================
    // ENDPOINTS AJAX
    // ============================================================
    AJAX: {
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

