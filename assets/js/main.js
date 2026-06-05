var btn      = document.getElementById('btnHamburger');
    var sidebar  = document.getElementById('sidebarCol');
    var overlay  = document.getElementById('sidebarOverlay');
    var isMobile = function () { return window.innerWidth < 768; };

function init() {
    // Detectar y crear módulos de inventario genéricos
    const inventoryModules = {
        relojesModule: "RelojesModule",
        esenciasModule: "EsenciasModule",
        insumosModule: "InsumosModule",
        locionesPreparadasModule: "LocionesModule",
        locionesAAAModule: "locionesAAAModule",
    };

    Object.entries(inventoryModules).forEach(([elementId, moduleName]) => {
        const $element = $(`#${elementId}`);
        if ($element.length) {
            const configStr = $element.data("inventory-config");
            if (configStr) {
                const config = typeof configStr === 'string' ? JSON.parse(configStr) : configStr;
                window[moduleName] = crearModuloInventario(config);
                window[moduleName].init();
            }
        }
    });

    if ($("#moduleCompras").length) {
        comprasModule.init();
    }

    if ($("#moduloProveedores").length) {
        proveedoresModule.init();
    }

    if ($("#formulasModule").length) {
        formulasModule.init();
    }

    if ($("#informeProduccionModule").length) {
        InformeProduccionModule.init();
    }

    if ($("#marcasModule").length) {
        marcasModule.init();
    }

    menuHamburguer();
    setActiveMenu();
}

/* Activar el menu  */
function setActiveMenu() {
    var path = (typeof currentPage !== 'undefined') ? currentPage : 'home';
    document.querySelectorAll('.ul-list__item[onclick]').forEach(function (item) {
        item.classList.remove('active');
        var match = item.getAttribute('onclick').match(/redirect\(['"](.+)['"]\)/);
        if (match && match[1] === path) {
            item.classList.add('active');
            var collapse = item.closest('#collapseExample');
            if (collapse) {
                collapse.classList.add('show');
            }
        }
    });
}

/* Redirigir a una vista */
function redirect(vista) {
    window.location.href = vista;
}

/* Menu hamburguesa */
function menuHamburguer() {
     btn.addEventListener('click', function () {
        if (isMobile()) {
            sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile();
        } else {
            toggleDesktop();
        }
    });

    overlay.addEventListener('click', closeMobile);

    // Al redimensionar, limpiar estados cruzados
    window.addEventListener('resize', function () {
        if (!isMobile()) {
            closeMobile();
        }
    });
}

/* Abrir menú en móvil */
 function openMobile() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('active');
        btn.classList.add('open');
    }

    function closeMobile() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        btn.classList.remove('open');
    }

    function toggleDesktop() {
        sidebar.classList.toggle('collapsed');
    }

init();