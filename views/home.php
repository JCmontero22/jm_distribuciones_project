<?php
    $usuarioActual = Auth::user() ?? [];
    $nombreUsuario = htmlspecialchars($usuarioActual['nombre_usuario'] ?? 'Usuario');
    $perfilUsuario = htmlspecialchars(Auth::userProfile() ?? 'Colaborador');
    $idSedeUsuario = Auth::sedeUsuario();
    $esAdmin       = empty($idSedeUsuario);

    $mesesEs = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $fechaHoyTexto = date('d') . ' de ' . $mesesEs[(int)date('n') - 1] . ', ' . date('Y');

    $puedeRuta = function (string $ruta): bool {
        return !defined('REQUIRE_AUTH') || !REQUIRE_AUTH || Auth::hasPermission($ruta);
    };
?>

<section class="content-panel" id="dashboardHomeModule">
    <!-- CABECERA DE BIENVENIDA -->
    <div class="dashboard-hero mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                    <span class="dashboard-badge-role">
                        <i class="fa-solid fa-user-shield me-2"></i><?= $perfilUsuario ?>
                    </span>
                    <span class="dashboard-badge-meta">
                        <i class="fa-solid fa-store me-2"></i><?= $esAdmin ? 'Vista General · Todas las Sedes' : 'Sede Asignada #' . (int)$idSedeUsuario ?>
                    </span>
                    <span class="dashboard-badge-meta">
                        <i class="fa-regular fa-calendar me-2"></i><?= $fechaHoyTexto ?>
                    </span>
                </div>
                <h1 class="mb-2">¡Hola, <?= $nombreUsuario ?>!</h1>
                <p class="subtitulo-seccion mb-0">
                    Panel general de operaciones de <strong>Distribuciones JM</strong>. Gestiona ventas, consulta disponibilidad de lociones y controla el movimiento diario de tu sede.
                </p>
            </div>

            <div class="col-lg-4 d-flex flex-wrap justify-content-lg-end gap-3">
                <?php if ($puedeRuta('informeProduccion')): ?>
                    <button type="button" class="btn btn-ventas-secundario" onclick="redirect('informeProduccion')">
                        <i class="fa-solid fa-flask-vial me-2"></i>Ver Producción
                    </button>
                <?php endif; ?>
                <?php if ($puedeRuta('ventas')): ?>
                    <button type="button" class="btn btnRegistro px-4" onclick="redirect('ventas')">
                        <i class="fa-solid fa-cash-register me-2"></i>Nueva Venta
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- INDICADORES OPERATIVOS DEL DÍA (COMÚN PARA VENDEDOR Y ADMIN) -->
    <div class="row g-4 mb-5 align-items-stretch">
        <div class="col-sm-6 col-xl-3">
            <div class="card-information">
                <h3>
                    <span class="icon icon-ventas"><i class="fa-solid fa-bag-shopping"></i></span>
                    VENTAS DE HOY
                </h3>
                <h3 id="homeVentasHoy">$ 0</h3>
                <span class="ventas-kpi-sub" id="homeCantidadVentasHoy">0 ventas registradas hoy</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card-information">
                <h3>
                    <span class="icon icon-ganancia"><i class="fa-solid fa-coins"></i></span>
                    RECAUDO EN CAJA
                </h3>
                <h3 id="homeRecaudoHoy" class="text-success">$ 0</h3>
                <span class="ventas-kpi-sub">Pagos y abonos recibidos hoy</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card-information">
                <h3>
                    <span class="icon icon-potencial"><i class="fa-solid fa-clock"></i></span>
                    CUENTAS POR COBRAR
                </h3>
                <h3 id="homePorCobrar" class="text-warning">$ 0</h3>
                <span class="ventas-kpi-sub" id="homeCantidadPendientes">0 ventas a crédito pendientes</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card-information">
                <h3>
                    <span class="icon icon-inversion"><i class="fa-solid fa-spray-can-sparkles"></i></span>
                    ESENCIAS EN STOCK
                </h3>
                <h3 id="homeEsenciasActivas" class="text-info">0</h3>
                <span class="ventas-kpi-sub">Referencias listas para preparar</span>
            </div>
        </div>
    </div>

    <!-- ACCESOS RÁPIDOS DE OPERACIÓN DIARIA -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0"><i class="fa-solid fa-bolt text-warning me-2"></i>Accesos Rápidos de Operación</h3>
        <span class="subtitulo-seccion small">Módulos principales de trabajo diario</span>
    </div>

    <div class="row g-4 mb-5 align-items-stretch">
        <?php if ($puedeRuta('ventas')): ?>
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-quick-card" onclick="redirect('ventas')">
                <div class="dashboard-quick-card__top">
                    <span class="icon icon-ventas"><i class="fa-solid fa-cash-register"></i></span>
                    <i class="fa-solid fa-arrow-right dashboard-quick-card__arrow"></i>
                </div>
                <h4>Punto de Venta</h4>
                <p>Registra ventas de artículos o lociones preparadas, aplica descuentos y gestiona abonos.</p>
                <span class="dashboard-quick-card__cta">Ir a Ventas</span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($puedeRuta('informeProduccion')): ?>
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-quick-card" onclick="redirect('informeProduccion')">
                <div class="dashboard-quick-card__top">
                    <span class="icon icon-inversion"><i class="fa-solid fa-flask-vial"></i></span>
                    <i class="fa-solid fa-arrow-right dashboard-quick-card__arrow"></i>
                </div>
                <h4>Informe de Producción</h4>
                <p>Consulta gramos disponibles por esencia, tamaños de frasco y cuántas lociones puedes preparar.</p>
                <span class="dashboard-quick-card__cta">Consultar Fórmulas</span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($puedeRuta('gastos')): ?>
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-quick-card" onclick="redirect('gastos')">
                <div class="dashboard-quick-card__top">
                    <span class="icon icon-gasto"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    <i class="fa-solid fa-arrow-right dashboard-quick-card__arrow"></i>
                </div>
                <h4>Gastos de Sede</h4>
                <p>Registra pagos operativos del día como transporte, servicios, papelería o imprevistos.</p>
                <span class="dashboard-quick-card__cta">Registrar Gasto</span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($puedeRuta('locionesAAA') || $puedeRuta('relojes') || $puedeRuta('esencias')): ?>
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-quick-card" onclick="redirect('<?= $puedeRuta('locionesAAA') ? 'locionesAAA' : ($puedeRuta('relojes') ? 'relojes' : 'esencias') ?>')">
                <div class="dashboard-quick-card__top">
                    <span class="icon icon-potencial"><i class="fa-solid fa-boxes-stacked"></i></span>
                    <i class="fa-solid fa-arrow-right dashboard-quick-card__arrow"></i>
                </div>
                <h4>Inventario y Catálogo</h4>
                <p>Revisa existencias de Relojes, Lociones AAA, Esencias e Insumos disponibles en bodega.</p>
                <span class="dashboard-quick-card__cta">Explorar Inventario</span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- PANEL OPERATIVO: ACTIVIDAD RECIENTE + GUÍA Y ESTADO DE SEDE -->
    <div class="row g-4 align-items-stretch">
        <!-- Últimas ventas registradas -->
        <div class="col-lg-7">
            <div class="dashboard-panel h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h3 class="m-0">Actividad Reciente de Ventas</h3>
                        <p class="subtitulo-seccion small mb-0">Últimos movimientos registrados en el turno</p>
                    </div>
                    <?php if ($puedeRuta('ventas')): ?>
                        <button type="button" class="btn btn-sm btn-ventas-secundario" onclick="redirect('ventas')">
                            Ver todas <i class="fa-solid fa-chevron-right ms-1"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table id="tablaHomeVentasRecientes" class="table table-dark table-striped table-hover table-bordered custom-table text-center align-middle mb-0">
                        <thead>
                            <tr>
                                <th>N° Venta</th>
                                <th>Cliente</th>
                                <th>Sede</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="homeVentasRecientesBody">
                            <tr>
                                <td colspan="5" class="py-4 subtitulo-seccion fst-italic">
                                    Sin movimientos recientes para mostrar en este turno.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Estado operativo y flujo rápido para Vendedor / Administrador -->
        <div class="col-lg-5">
            <div class="dashboard-panel h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="m-0">Guía Operativa del Turno</h3>
                            <p class="subtitulo-seccion small mb-0">Flujo recomendado para atención y control de caja</p>
                        </div>
                        <span class="icon icon-potencial"><i class="fa-solid fa-clipboard-check"></i></span>
                    </div>

                    <div class="dashboard-step-list">
                        <div class="dashboard-step-item">
                            <span class="dashboard-step-number">1</span>
                            <div>
                                <strong>Verifica disponibilidad de lociones e insumos</strong>
                                <p class="mb-0">Antes de preparar una loción, revisa en <em>Informe de Producción</em> los gramos de esencia y frascos disponibles en tu sede.</p>
                            </div>
                        </div>

                        <div class="dashboard-step-item">
                            <span class="dashboard-step-number">2</span>
                            <div>
                                <strong>Registra cada venta y método de pago</strong>
                                <p class="mb-0">Ingresa si la venta es de contado o a crédito. Si el cliente realiza un pago parcial, regístralo como abono para mantener la cartera al día.</p>
                            </div>
                        </div>

                        <div class="dashboard-step-item">
                            <span class="dashboard-step-number">3</span>
                            <div>
                                <strong>Reporta los gastos de caja del día</strong>
                                <p class="mb-0">Cualquier salida de dinero de la sede debe quedar registrada en el módulo de <em>Gastos</em> con su categoría y soporte.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($puedeRuta('historialVentas') || $puedeRuta('balanceMensual') || $puedeRuta('configuracion')): ?>
                <div class="dashboard-admin-bar mt-4">
                    <span class="dashboard-admin-bar__label">
                        <i class="fa-solid fa-crown text-warning me-2"></i>Accesos de Administración:
                    </span>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($puedeRuta('historialVentas')): ?>
                            <button type="button" class="btn btn-sm btn-ventas-secundario" onclick="redirect('historialVentas')">
                                <i class="fa-solid fa-clock-rotate-left me-1"></i>Historial
                            </button>
                        <?php endif; ?>
                        <?php if ($puedeRuta('balanceMensual')): ?>
                            <button type="button" class="btn btn-sm btn-ventas-secundario" onclick="redirect('balanceMensual')">
                                <i class="fa-solid fa-scale-balanced me-1"></i>Balance Mensual
                            </button>
                        <?php endif; ?>
                        <?php if ($puedeRuta('compras')): ?>
                            <button type="button" class="btn btn-sm btn-ventas-secundario" onclick="redirect('compras')">
                                <i class="fa-solid fa-cart-plus me-1"></i>Compras
                            </button>
                        <?php endif; ?>
                        <?php if ($puedeRuta('configuracion')): ?>
                            <button type="button" class="btn btn-sm btn-ventas-secundario" onclick="redirect('configuracion')">
                                <i class="fa-solid fa-gear me-1"></i>Configuración
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
