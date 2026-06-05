/* 
    CAPA API - Solo respomsable de comunicarse con el servidor.
 */

const BASE_URL = "/PROYECTO_JM-ML/distribuciones_jm/jm_distribuciones_project";
const marcasAPI = {

    registrarMarcaAPI(formData){
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${BASE_URL}/ajax/catalogoAjax.php`,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos);
                },
                error(error) {
                    reject(error);
                }
            })
        });
    },

    obtenerMarcas(){
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${BASE_URL}/ajax/catalogoAjax.php`,
                method: "GET",
                data: { accion: "listadoMarcas" },
                success(response) {
                    const datos = JSON.parse(response);
                    resolve(datos.data || []);
                },
                error(error) {
                    reject(error);
                }
            });
        });
    }
};


/* 
    CAPA VIEW - Solo responsable de renderizar HTML y manipular el DOM
 */

const MarcasView = {
    mostrarMarcas(marcas) {
        console.log(marcas);
        
        const $container = $("#marcasTableBody");
        $container.empty();

        if (marcas.length === 0) {
            $container.append("<p>No hay marcas registradas.</p>");
            return;
        }

        marcas.forEach(marca => {
            const marcaHTML = `
                <tr>
                    <td>${marca.id_marca}</td>
                    <td>${marca.nombre_marca}</td>
                    <td><img src="${BASE_URL}/assets/img/marcas/${marca.img_marca}" alt="${marca.nombre_marca}" width="100"></td>
                </tr>
            `;
            $container.append(marcaHTML);
        });
    }
};

/* 
    MODULO DE MARCAS - Coordina API y views, maneja estados y logica del negocio 
 */

const marcasModule = {
    init(){
        this.bindEvents();
        this.cargarMarcas();
    },

    bindEvents(){
        $("#formularioRegistroMarca").on("submit", (e) => this.registrarMarca(e));
    },

    async registrarMarca(e){
        e.preventDefault();

        const formData = new FormData(e.target);
        const nombreMarca = (formData.get('nombreMarca') || '').toString().trim();
        const imagenMarca = formData.get('imagenMarca');

        if (!nombreMarca || !(imagenMarca instanceof File) || imagenMarca.size === 0) {
            Alerts.error("Por favor, complete el formulario.");
            return;
        }

        formData.set('accion', 'registroMarca');

        try {
            const response = await marcasAPI.registrarMarcaAPI(formData);
            if (response.success) {
                Alerts.success('Marca registrada', response.message || 'La marca se registró correctamente.');
                e.target.reset();
            } else {
                Alerts.error('Error', response.message || 'No se pudo registrar la marca.');
            }
        } catch (error) {
            console.error('Error al registrar marca:', error);
            Alerts.error('Error', 'No se pudo registrar la marca.');
        }

    },

    async cargarMarcas() {
        try {
            const marcas = await marcasAPI.obtenerMarcas();
            MarcasView.mostrarMarcas(marcas);
        } catch (error) {
            console.error('Error al cargar marcas:', error);
            Alerts.error('Error', 'No se pudieron cargar las marcas.');
        }
    }
}