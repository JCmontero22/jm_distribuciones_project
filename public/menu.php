<?php
    if (!defined('REQUIRE_AUTH')) {
        define('REQUIRE_AUTH', true);
    }

    require_once 'core/Auth.php';

    // Inicializar autenticación
    if (REQUIRE_AUTH) {
        Auth::init();
    }

    // Misma regla que AuthMiddleware: un ítem se muestra solo si el usuario puede abrir su ruta.
    // Sin autenticación activa se muestran todos los ítems.
    $puede = function ($ruta) {
        return !REQUIRE_AUTH || Auth::hasPermission($ruta);
    };

    // Un grupo (sección) se muestra si al menos uno de sus ítems es visible
    $algunoPuede = function (array $rutas) use ($puede) {
        foreach ($rutas as $ruta) {
            if ($puede($ruta)) {
                return true;
            }
        }
        return false;
    };

    $grupoInventario   = ['relojes', 'esencias', 'insumos', 'locionesAAA'];
    $grupoCompras      = ['compras', 'proveedores'];
    $grupoProduccion   = ['informeProduccion', 'formulas', 'marcas'];
    $grupoPromociones  = ['bannerPromociones', 'descuentos'];
    $grupoVentas       = ['ventas', 'gastos', 'historialVentas', 'balanceMensual'];
    $grupoVendedor = ['ventas', 'informeProduccion', 'gastos'];

?>

<div class="content-menu">
    <section class="content-marca">
        <div class="content-marca__logo">
            <img src="./assets/img/logo4.png" alt="Logo" class="logo">
        </div>
    </section>

    <section class="menu-content">
        <ul class="ul-list">


            <?php if ($puede('home')): ?>
            <li class="ul-list__item" onclick="redirect('home')"><a href="#">  <i class="fa-solid fa-gauge-high"></i> Resumen</a></li>
            <?php endif; ?>

            <?php if ($algunoPuede($grupoVentas)): ?>
            <li class="ul-list__item" ><a type="button" data-bs-toggle="collapse" data-bs-target="#sales" aria-expanded="false" aria-controls="sales"><i class="fa-solid fa-cart-shopping"></i> Ventas</a></li>
            <div id="sales" class="collapse">
                <ul class="ul-list">
                    <?php if ($puede('ventas')): ?>
                    <li class="ul-list__item" onclick="redirect('ventas')"><a href="#"><i class="fa-solid fa-cash-register"></i> Ventas</a></li>
                    <?php endif; ?>
                    <?php if ($puede('gastos')): ?>
                    <li class="ul-list__item" onclick="redirect('gastos')"><a href="#"><i class="fa-solid fa-file-invoice-dollar"></i> Gastos</a></li>
                    <?php endif; ?>
                    <?php if ($puede('historialVentas')): ?>
                    <li class="ul-list__item" onclick="redirect('historialVentas')"><a href="#"><i class="fa-solid fa-clock-rotate-left"></i> Historial de ventas</a></li>
                    <?php endif; ?>
                    <?php if ($puede('balanceMensual')): ?>
                    <li class="ul-list__item" onclick="redirect('balanceMensual')"><a href="#"><i class="fa-solid fa-scale-balanced"></i> Balance mensual</a></li>
                    <?php endif; ?>
                </ul>
            </div>
                
            <?php endif; ?>

            <?php if ($algunoPuede($grupoInventario)): ?>
            <li class="ul-list__item" ><a type="button" data-bs-toggle="collapse" data-bs-target="#inventory" aria-expanded="false" aria-controls="inventory"><i class="fa-solid fa-cart-flatbed"></i> Inventario</a></li>
            <div id="inventory" class="collapse">
                <ul class="ul-list">
                    <?php if ($puede('relojes')): ?>
                    <li class="ul-list__item" onclick="redirect('relojes')"><a href="#"><i class="fa-solid fa-stopwatch-20"></i> Relojes</a></li>
                    <?php endif; ?>
                    <?php if ($puede('esencias')): ?>
                    <li class="ul-list__item" onclick="redirect('esencias')"><a href="#"><i class="fa-solid fa-bottle-water"></i> Esencias</a></li>
                    <?php endif; ?>
                    <?php if ($puede('insumos')): ?>
                    <li class="ul-list__item" onclick="redirect('insumos')"><a href="#"><i class="fa-solid fa-toolbox"></i> Insumos</a></li>
                    <?php endif; ?>
                    <?php if ($puede('locionesAAA')): ?>
                    <li class="ul-list__item" onclick="redirect('locionesAAA')"><a href="#"><i class="fa-solid fa-spray-can-sparkles"></i> Lociones AAA</a></li>
                    <?php endif; ?>

                </ul>
            </div>
            <?php endif; ?>

            <?php if ($algunoPuede($grupoCompras)): ?>
            <li class="ul-list__item" ><a type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class="fa-solid fa-cart-plus"></i> Compras</a></li>
            <div id="collapseExample" class="collapse">
                <ul class="ul-list">
                    <?php if ($puede('compras')): ?>
                    <li class="ul-list__item" onclick="redirect('compras')"><a href="#"><i class="fa-solid fa-cash-register"></i> Compras</a></li>
                    <?php endif; ?>
                    <?php if ($puede('proveedores')): ?>
                    <li class="ul-list__item" onclick="redirect('proveedores')"><a href="#"><i class="fa-solid fa-users"></i> Proveedores</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if ($algunoPuede($grupoProduccion)): ?>
            <li class="ul-list__item" ><a type="button" data-bs-toggle="collapse" data-bs-target="#produccion" aria-expanded="false" aria-controls="produccion"><i class="fa-solid fa-pump-soap"></i> Produccion</a></li>
            <div id="produccion" class="collapse">
                <ul class="ul-list">
                    <?php if ($puede('informeProduccion')): ?>
                    <li class="ul-list__item" onclick="redirect('informeProduccion')"><a href="#"><i class="fa-solid fa-cash-register"></i> Informe de producción</a></li>
                    <?php endif; ?>
                    <?php if ($puede('formulas')): ?>
                    <li class="ul-list__item" onclick="redirect('formulas')"><a href="#"><i class="fa-solid fa-flask-vial"></i> Formulas</a></li>
                    <?php endif; ?>
                    <?php if ($puede('marcas')): ?>
                    <li class="ul-list__item" onclick="redirect('marcas')"><a href="#"><i class="fa-solid fa-cubes"></i> Marcas</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if ($algunoPuede($grupoPromociones)): ?>
            <li class="ul-list__item" ><a type="button" data-bs-toggle="collapse" data-bs-target="#promocion" aria-expanded="false" aria-controls="promocion"><i class="fa-solid fa-hand-holding-dollar"></i> Promociones</a></li>
            <div id="promocion" class="collapse">
                <ul class="ul-list">
                    <?php if ($puede('bannerPromociones')): ?>
                    <li class="ul-list__item" onclick="redirect('bannerPromociones')"><a href="#"><i class="fa-regular fa-file-image"></i> Banner Promociones</a></li>
                    <?php endif; ?>
                    <?php if ($puede('descuentos')): ?>
                    <li class="ul-list__item" onclick="redirect('descuentos')"><a href="#"><i class="fa-solid fa-cart-arrow-down"></i> Descuentos</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if ($puede('configuracion')): ?>
            <li class="ul-list__item" onclick="redirect('configuracion')"><a href="#"><i class="fa-solid fa-cog"></i> Configuraciones</a></li>
            <?php endif; ?>

            

        </ul>
    </section>

    <section class="copyRith">
        <p style="font-size: 1.3rem;">© 2026 Distribuciones JM-Devmente. Todos los derechos reservados.</p>
    </section>
</div>

