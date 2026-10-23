/**
 * homeDashboard.js - Indicadores, ventas recientes y alerta de stock bajo del inicio
 * Capas: API (AJAX) / View (DOM) / Module (coordina)
 */

const homeDashboardAPI = {
  obtenerResumen() {
    return $.ajax({
      url: `${CONFIG.BASE_URL}ajax/homeAjax.php`,
      method: "GET",
      data: { accion: "obtenerResumen" }
    }).then(response => {
      const datos = typeof response === "string" ? JSON.parse(response) : response;
      return datos.data || {};
    });
  }
};

const homeDashboardView = {
  escapar(texto) {
    return $("<div>").text(texto ?? "").html();
  },

  formatearPesos(valor) {
    return `$ ${FormatUtils.separaMiles(Math.round(Number(valor) || 0))}`;
  },

  mostrarKpis(resumen) {
    const ventas = resumen.ventasHoy || 0;
    const pendientes = resumen.ventasPendientes || 0;

    $("#homeVentasHoy").text(this.formatearPesos(resumen.totalVentasHoy));
    $("#homeCantidadVentasHoy").text(`${ventas} ${ventas === 1 ? "venta registrada" : "ventas registradas"} hoy`);
    $("#homeRecaudoHoy").text(this.formatearPesos(resumen.recaudoHoy));
    $("#homePorCobrar").text(this.formatearPesos(resumen.porCobrar));
    $("#homeCantidadPendientes").text(`${pendientes} ${pendientes === 1 ? "venta a crédito pendiente" : "ventas a crédito pendientes"}`);
    $("#homeEsenciasActivas").text(resumen.esenciasEnStock || 0);
  },

  mostrarVentasRecientes(ventas) {
    const tbody = $("#homeVentasRecientesBody");

    if (!ventas.length) {
      tbody.html(`
        <tr>
          <td colspan="5" class="py-4 subtitulo-seccion fst-italic">
            Sin movimientos recientes para mostrar en este turno.
          </td>
        </tr>
      `);
      return;
    }

    const estados = { pagada: "bg-success", pendiente: "bg-warning text-dark", anulada: "bg-danger" };

    tbody.html(ventas.map(v => `
      <tr>
        <td>${this.escapar(v.numero_venta)}</td>
        <td>${this.escapar(v.cliente)}</td>
        <td>${this.escapar(v.nombre_sede)}</td>
        <td>${this.formatearPesos(v.total_venta)}</td>
        <td><span class="badge ${estados[v.estado_venta] || "bg-secondary"}">${this.escapar(v.estado_venta)}</span></td>
      </tr>
    `).join(""));
  },

  /** Muestra la alerta de stock bajo. Devuelve cuántos productos están bajos. */
  mostrarStockBajo(stockBajo, umbrales) {
    const unidades = stockBajo?.unidades || [];
    const esencias = stockBajo?.esencias || [];
    const total = unidades.length + esencias.length;

    const alerta = $("#homeAlertaStock");
    if (total === 0) {
      alerta.addClass("d-none");
      return 0;
    }

    $("#homeAlertaStockResumen").text(
      `${total} ${total === 1 ? "producto está" : "productos están"} por debajo del mínimo ` +
      `(${umbrales.unidades} unidades o ${umbrales.gramos} g).`
    );

    const filaUnidades = unidades.map(p => `
      <li class="dashboard-stock-alert__item">
        <div class="dashboard-stock-alert__info">
          <i class="fa-solid fa-box-open dashboard-stock-alert__bullet"></i>
          <strong>${this.escapar(p.nombre_producto)} - ${this.escapar(p.nombre_presentacion)}</strong>
          <span class="dashboard-stock-alert__sede"><i class="fa-solid fa-location-dot me-1"></i>${this.escapar(p.nombre_sede)}</span>
        </div>
        <span class="stock-pill stock-pill-danger">${Number(p.stock)} und.</span>
      </li>
    `);
    const filaEsencias = esencias.map(p => `
      <li class="dashboard-stock-alert__item">
        <div class="dashboard-stock-alert__info">
          <i class="fa-solid fa-flask-vial dashboard-stock-alert__bullet"></i>
          <strong>${this.escapar(p.nombre_producto)} - ${this.escapar(p.nombre_presentacion)}</strong>
          <span class="dashboard-stock-alert__sede"><i class="fa-solid fa-location-dot me-1"></i>${this.escapar(p.nombre_sede)}</span>
        </div>
        <span class="stock-pill stock-pill-gold">${FormatUtils.separaMiles(Math.round(Number(p.stock)))} g</span>
      </li>
    `);

    $("#homeAlertaStockLista").html([...filaUnidades, ...filaEsencias].join(""));
    alerta.removeClass("d-none");
    return total;
  }
};

const homeDashboardModule = {
  init() {
    this.bindEvents();
    this.cargar();
  },

  bindEvents() {
    $("#homeAlertaStockCerrar").on("click", () => $("#homeAlertaStock").addClass("d-none"));
  },

  async cargar() {
    try {
      const resumen = await homeDashboardAPI.obtenerResumen();

      homeDashboardView.mostrarKpis(resumen);
      homeDashboardView.mostrarVentasRecientes(resumen.ventasRecientes || []);

      const alertas = homeDashboardView.mostrarStockBajo(resumen.stockBajo, resumen.umbrales || {});
      if (alertas > 0) {
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: "warning",
          title: `Stock bajo en ${alertas} ${alertas === 1 ? "producto" : "productos"}`,
          showConfirmButton: false,
          timer: 5000,
          timerProgressBar: true
        });
      }
    } catch (error) {
      console.error("Error al cargar el resumen del inicio:", error);
    }
  }
};

window.homeDashboardModule = homeDashboardModule;
