<div class="content-menu">
    <section class="content-marca">
        <div class="content-marca__logo">
            <img src="./assets/img/logo4.png" alt="Logo" class="logo">
        </div>
    </section>

    <section class="menu-content">
        <ul class="ul-list">
            <li class="ul-list__item" onclick="redirect('home')"><a href="#">  <i class="fa-solid fa-gauge-high"></i> Resumen</a></li>
            <li class="ul-list__item" ><a type="button" data-bs-toggle="collapse" data-bs-target="#inventory" aria-expanded="false" aria-controls="inventory"><i class="fa-solid fa-cart-flatbed"></i> Inventario</a></li>
            <div id="inventory" class="collapse">
                <ul class="ul-list">
                    <li class="ul-list__item" onclick="redirect('relojes')"><a href="#"><i class="fa-solid fa-stopwatch-20"></i> Relojes</a></li>
                    <li class="ul-list__item" onclick="redirect('locionesPreparadas')"><a href="#"><i class="fa-solid fa-vial"></i> Lociones Preparadas</a></li>
                    <li class="ul-list__item" onclick="redirect('esencias')"><a href="#"><i class="fa-solid fa-bottle-water"></i> Esencias</a></li>
                    <li class="ul-list__item" onclick="redirect('insumos')"><a href="#"><i class="fa-solid fa-toolbox"></i> Insumos</a></li>
                    <li class="ul-list__item" onclick="redirect('locionesAAA')"><a href="#"><i class="fa-solid fa-spray-can-sparkles"></i> Lociones AAA</a></li>
                    
                </ul>
            </div>
            <li class="ul-list__item" ><a type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class="fa-solid fa-cart-plus"></i> Compras</a></li>
            <div id="collapseExample" class="collapse">
                <ul class="ul-list">
                    <li class="ul-list__item" onclick="redirect('compras')"><a href="#"><i class="fa-solid fa-cash-register"></i> Compras</a></li>
                    <li class="ul-list__item" onclick="redirect('proveedores')"><a href="#"><i class="fa-solid fa-users"></i> Proveedores</a></li>
                </ul>
            </div>
            <li class="ul-list__item" onclick="redirect('historial')"><a href="#"><i class="fa-solid fa-clock-rotate-left"></i> Historial</a></li>
            
        </ul>
    </section>

    <section class="copyRith">
        <p style="font-size: 1.3rem;">© 2026 Distribuciones JM-Devmente. Todos los derechos reservados.</p>
    </section>
</div>

