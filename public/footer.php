
<!-- ============================================================
     LIBRERÍAS EXTERNAS
     ============================================================ -->
<script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>

<!-- ============================================================
     1. CONFIGURACIÓN (DEBE SER PRIMERO)
     ============================================================ -->
<?php
    $bp = defined('BASE_PATH') ? BASE_PATH : '/';
    $archivosVersion = array_merge(
        [__DIR__ . '/../assets/css/style.css', __DIR__ . '/../assets/js/main.js'],
        glob(__DIR__ . '/../assets/js/core/*.js') ?: [],
        glob(__DIR__ . '/../assets/js/modules/*.js') ?: []
    );
    $jsVer = max(array_map(fn($f) => @filemtime($f) ?: 0, $archivosVersion)) ?: time();
?>
<script src="<?= $bp ?>assets/js/config/config.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/core/csrf.js?v=<?= $jsVer ?>"></script>

<!-- ============================================================
     2. CORE - SERVICIOS BÁSICOS
     ============================================================ -->
<script src="<?= $bp ?>assets/js/core/Logger.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/core/SimpleAPI.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/core/alert.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/core/utils.js?v=<?= $jsVer ?>"></script>

<!-- ============================================================
     3. MÓDULOS DE LA APLICACIÓN
     ============================================================ -->

<!-- ============================================================
     4. INICIALIZACIÓN PRINCIPAL
     ============================================================ -->
<script src="<?= $bp ?>assets/js/modules/inventarioGenerico.js?v=<?= @filemtime(__DIR__ . '/../assets/js/modules/inventarioGenerico.js') ?: $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/compras.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/ventas.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/gastos.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/historial.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/balance.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/proveedores.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/inventarioFormulas.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/informeProduccion.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/marcas.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/bannerPromociones.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/descuentos.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/configuracion.js?v=<?= $jsVer ?>"></script>
<script src="<?= $bp ?>assets/js/modules/homeDashboard.js?v=<?= @filemtime(__DIR__ . '/../assets/js/modules/homeDashboard.js') ?: $jsVer ?>"></script>

<script src="<?= $bp ?>assets/js/main.js?v=<?= $jsVer ?>"></script>

<!-- ============================================================
     LOGOUT HANDLER
     ============================================================ -->
<script>
    // Manejar click en botón de logout
    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.getElementById('logoutBtn');

        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Confirmar logout
                Swal.fire({
                    title: '¿Cerrar sesión?',
                    text: 'Se cerrará tu sesión actual',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, cerrar sesión',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Realizar logout
                        const formData = new FormData();
                        formData.append('accion', 'logout');

                        fetch('<?= $bp ?>ajax/login.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Redirigir al login
                                window.location.href = '<?= $bp ?>login';
                            } else {
                                Swal.fire('Error', 'Error al cerrar sesión', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error al logout:', error);
                            Swal.fire('Error', 'Error de conexión', 'error');
                        });
                    }
                });
            });
        }
    });
</script>

</body>
</html>

