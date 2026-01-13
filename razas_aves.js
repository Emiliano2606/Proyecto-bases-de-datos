document.addEventListener("DOMContentLoaded", function() {
    // 1. CARGAR EL ARCHIVO JSON DE AVES 
    let datosAves = null;

    fetch("../razas_aves.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas (Estado: " + response.status + ")");
            return response.json();
        })
        .then(data => {
            datosAves = data;

            inicializarSelectAve(document.querySelector("select[name='especie_ave']"), data);

            document.addEventListener('change', function(e) {
                if (e.target && e.target.name && e.target.name.startsWith('especie_ave')) {
                    const select = e.target;

                    if (select.querySelector('option[value]')) {
                        if (datosAves) {
                            procesarCambioEspecie(select, datosAves);
                        }
                    } else {
                        inicializarSelectAve(select, datosAves);
                    }
                }
            });

            const selectsExistentes = document.querySelectorAll("select[name^='especie_ave']");
            selectsExistentes.forEach((select, index) => {
                if (index > 0) {
                    inicializarSelectAve(select, data);
                }
            });

        })
        .catch(error => console.error("Error al cargar o procesar las razas:", error));

    function inicializarSelectAve(selectElement, data) {
        if (!selectElement || !data) return;

        while (selectElement.options.length > 1) {
            selectElement.remove(1);
        }

        data.forEach(ave => {
            const option = document.createElement("option");
            option.value = ave.id_ave;
            option.textContent = ave.ave;
            selectElement.appendChild(option);
        });

        if (selectElement.value) {
            procesarCambioEspecie(selectElement, data);
        }
    }

    function procesarCambioEspecie(selectElement, data) {
        const idSeleccionado = selectElement.value;
        const seccionPadre = selectElement.closest('.section');

        if (!seccionPadre) return;


        const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';


        const grupoInput = seccionPadre.querySelector('[id="grupo_taxonomico"], [name="grupo_taxonomico' + sufijo + '"]');
        const conservacionInput = seccionPadre.querySelector('[id="conservacion_ave"], [name="estatus_conservacion' + sufijo + '"]');
        const clasificacionInput = seccionPadre.querySelector('[id="clasificacion_autoridades"], [name="clasificacion_autoridades' + sufijo + '"]');

        if (!datosAves) return;

        const aveSeleccionada = datosAves.find(a => a.id_ave == idSeleccionado);

        if (aveSeleccionada) {
            if (grupoInput) grupoInput.value = aveSeleccionada.grupo_taxonómico || "";
            if (conservacionInput) conservacionInput.value = aveSeleccionada.estatus_conservación || "";
            if (clasificacionInput) clasificacionInput.value = aveSeleccionada.clasificación_autoridades || "";
        } else {
            if (grupoInput) grupoInput.value = "";
            if (conservacionInput) conservacionInput.value = "";
            if (clasificacionInput) clasificacionInput.value = "";
        }
    }
});