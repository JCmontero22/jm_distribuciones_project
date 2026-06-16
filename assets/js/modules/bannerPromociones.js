const bannerPromocionesAPI = {
    client: new SimpleAPI(CONFIG.AJAX.PROMOCIONES),

    async obtenerBanners() {
        const response = await this.client.get({ accion: "obtenerBanners" });
        return response.data || [];
    },

    async registroBanner(formData) {
        return await this.client.post(formData);
    },

    async obtenerBannerID(payload) {
        const response = await this.client.get({ ...payload, accion: "obtenerBannerID" });
        return response.data || null;
    },

    async editarBanner(formData) {
        return await this.client.post(formData);
    },

    async eliminarBanner(payload) {
        return await this.client.post({ ...payload, accion: "eliminarBanner" });
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
                        const id = row.id_banner_promocion
                        if (!data) return "-";
                        const nombre = row?.nombre_banner_promocion || "Banner";
                        const src = `../../imagenes-jm-distribuciones/bannerPromociones/${data}`;
                        return `<img src="${src}" alt="${nombre}" style="width: 100px; height: auto;">`;
                    }
                },
                {
                     button: "Acciones",
                    render(data, _type, row) {
                        const id = row.id_banner_promocion;
                        return `<button class="btn btn-sm btn-primary btn_editar" data-id="${id}"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-sm btn-danger btn_eliminar" data-id="${id}"><i class="fa-regular fa-trash-can"></i></button>`;
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
    },

    renderizarFormularioEdicion(banner) {
        if (!banner) {
            Alerts.error("Error", "No se pudo cargar la información del banner");
            return;
        }

        $("#id_banner_promocion").val(banner[0].id_banner_promocion);
        $("#nombreBanner").val(banner[0].nombre_banner_promocion);
        $("#fechaInicio").val(banner[0].fecha_inicio);
        $("#fechaFin").val(banner[0].fecha_fin);
        $("#btnRegistrarDetalles").text("Actualizar Banner");
        $("#btnRegistrarDetalles").data("id", banner[0].id_banner_promocion);
        // Nota: Para la imagen, normalmente no se pre-carga en el input file por seguridad, pero podrías mostrar una vista previa si lo deseas.
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
        $("#modalRegistrarBanner").on("hidden.bs.modal", () => {
            AppUI.resetForm("#registroDeBanner");
            $("#id_banner_promocion").val("");
            $("#btnRegistrarDetalles").text("Registrar Banner");
        });
        $(document).on('click', '.btn_editar', (e) =>{
            const id = $(e.currentTarget).data("id");
            this.obtenerDetalleBanner(id);
        });
        $(document).on('click', '.btn_eliminar', (e) => {
            const id = $(e.currentTarget).data("id");
            this.eliminarBanner(id);
        });

    },

    async cargarBanners() {
        try {
            const banners = await bannerPromocionesAPI.obtenerBanners();
            bannerPromocionesView.renderizarTablaBanners(banners);
        } catch (error) {
            Alerts.error("Error", "No se pudieron cargar los banners");
        }
    },

    async registrarBanner(e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const idBanner = formData.get("id_banner_promocion");
        const esEdicion = !!idBanner;
        formData.set("accion", esEdicion ? "actualizarBanner" : "registrarBannerPromocion");

        if (!formData.get("nombreBanner") || !formData.get("fechaInicio") || !formData.get("fechaFin")) {
            Alerts.warning("Campos incompletos", "Por favor, complete todos los campos requeridos.");
            return;
        }

        if (!esEdicion && !formData.get("imgBanner")) {
            Alerts.warning("Campos incompletos", "Por favor, seleccione la imagen del banner.");
            return;
        }

        const confirmacion = await Alerts.confirmation(
            esEdicion ? "Actualizar este banner?" : "Registrar este banner?",
            esEdicion ? "Estas seguro de que deseas actualizar este banner?" : "Estas seguro de que deseas registrar este banner?"
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await bannerPromocionesAPI.registroBanner(formData);

            if (response.success) {
                Alerts.success(esEdicion ? "Banner actualizado" : "Banner registrado", response.message || "Operación exitosa");
                if (this.modalBanner) this.modalBanner.hide();
                await this.cargarBanners();
            } else {
                Alerts.error("Error", response.message || (esEdicion ? "No se pudo actualizar el banner" : "No se pudo registrar el banner"));
            }
        } catch (error) {
            Logger.error(esEdicion ? "Error al actualizar banner" : "Error al registrar banner", error);
            Alerts.error("Error", esEdicion ? "No se pudo actualizar el banner" : "No se pudo registrar el banner");
        }
    },

    async obtenerDetalleBanner(id) {
        try{
            const response = await bannerPromocionesAPI.obtenerBannerID({ id_banner_promocion: id });
            bannerPromocionesView.renderizarFormularioEdicion(response);
            this.modalBanner.show();
        }catch(error){
            Logger.error("Error al editar banner", error);
            Alerts.error("Error", "No se pudo editar el banner");
        }
    },

    async eliminarBanner(id) {
        const confirmacion = await Alerts.confirmation(
            "Eliminar este banner?",
            "Estas seguro de que deseas eliminar este banner?"
        );

        if (!confirmacion.isConfirmed) return;

        try {
            const response = await bannerPromocionesAPI.eliminarBanner({ id_banner_promocion: id });

            if (response.success) {
                Alerts.success("Banner eliminado", response.message || "Operación exitosa");
                await this.cargarBanners();
            } else {
                Alerts.error("Error", response.message || "No se pudo eliminar el banner");
            }
        } catch (error) {
            Logger.error("Error al eliminar banner", error);
            Alerts.error("Error", "No se pudo eliminar el banner");
        }
    }
};

window.bannerPromocionesModule = bannerPromocionesModule;




