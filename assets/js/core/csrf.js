/**
 * CSRF - Envía el token CSRF en todas las peticiones que modifican datos
 * (POST, PUT, DELETE...) hechas con jQuery o fetch.
 * Debe cargarse después de jQuery y antes de los módulos.
 * El servidor valida el header X-CSRF-Token en Auth::requireApi().
 */
(function () {
    const METODOS_SEGUROS = ['GET', 'HEAD', 'OPTIONS'];
    const esMetodoSeguro = (metodo) => METODOS_SEGUROS.includes((metodo || 'GET').toUpperCase());

    if (window.jQuery) {
        jQuery.ajaxSetup({
            beforeSend: function (xhr, opciones) {
                if (!esMetodoSeguro(opciones.type)) {
                    xhr.setRequestHeader('X-CSRF-Token', window.csrfToken || '');
                }
            }
        });
    }

    const fetchOriginal = window.fetch;
    window.fetch = function (url, opciones = {}) {
        if (!esMetodoSeguro(opciones.method)) {
            const headers = new Headers(opciones.headers || {});
            headers.set('X-CSRF-Token', window.csrfToken || '');
            opciones = Object.assign({}, opciones, { headers: headers });
        }
        return fetchOriginal.call(this, url, opciones);
    };
})();
