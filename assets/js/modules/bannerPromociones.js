const bannerPromocionesAPI = {
    client: new SimpleAPI(CONFIG.AJAX.PROMOCIONES),

    async obtenerBanners() {
        const response = await this.client.get({ accion: "obtenerBanners" });
        return response.data || [];
    },

    async registroBanner(formData) {
        return await this.client.post(formData);
    }
};

/* 
    CAPA VIEW - Solo responsable de renderizar HTML y manipular en el DOM
 */

const bannerPromocionesView = {
    renderizarTablaBanners(banners) {
        AppUI.initDataTable("#tablaBanners", {
            data: banners || [],
            columns: [
                { data: "id_banner_promocion" },
                { data: "nombre_banner_promocion" },
                { data: "fecha_inicio" },
                { data: "fecha_fin" },
                {
                    data: "img_banner_promocion",
                    render(data, _type, row) {
                        if (!data) return "-";
                        const nombre = row?.nombre_banner_promocion || "Banner";
                        const src = `${CONFIG.BASE_URL}assets/img/banners/${data}`;
                        return `<img src="${src}" alt="${nombre}" style="width: 100px; height: auto;">`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    defaultContent: "-"
                }
            ],
            order: [[0, "desc"]]
        });
    }
};


/* 
    Modulo bannerPromocionesModule - Responsable de la lógica del módulo, eventos y comunicación entre API y View
 */

const bannerPromocionesModule = {
    modalBanner: null,

    init() {
        this.modalBanner = AppUI.createModal("modalRegistrarBanner");
        this.bindEvents();
        this.cargarBanners();
    },

    bindEvents() {
        $("#registroDeBanner").on("submit", (e) => this.registrarBanner(e));
        $("#modalRegistrarBanner").on("hidden.bs.modal", () => AppUI.resetForm("#registroDeBanner"));
    },

    async cargarBanners() {
        try {
            const banners = await bannerPromocionesAPI.obtenerBanners();
            bannerPromocionesView.renderizarTablaBanners(banners);
        } catch (error) {
            Logger.error("Error al cargar banners", error);
            Alerts.error("Error", "No se pudieron cargar los banners");
        }
    },

    async registrarBanner(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        formData.append("accion", "registrarBannerPromocion");

        if (!formData.get("nombreBanner") || !formData.get("imgBanner") || !formData.get("fechaInicio") || !formData.get("fechaFin")) {
            Alerts.warning("Campos incompletos", "Por favor, complete todos los campos requeridos.");
            return;
        }

        const confirmacion = await Alerts.confirmation(
            "Registrar este banner?",
            "Estas seguro de que deseas registrar este banner?"
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await bannerPromocionesAPI.registroBanner(formData);

            if (response.success) {
                Alerts.success("Banner registrado", response.message || "Registro exitoso");
                if (this.modalBanner) this.modalBanner.hide();
                await this.cargarBanners();
            } else {
                Alerts.error("Error", response.message || "No se pudo registrar el banner");
            }
        } catch (error) {
            Logger.error("Error al registrar banner", error);
            Alerts.error("Error", "No se pudo registrar el banner");
        }
    }
};

window.bannerPromocionesModule = bannerPromocionesModule;




