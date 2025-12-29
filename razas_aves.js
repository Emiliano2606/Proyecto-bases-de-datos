document.addEventListener("DOMContentLoaded", function() {
    // 1. CARGAR EL ARCHIVO JSON DE AVES (una sola vez)
    let datosAves = null;

    fetch("../razas_aves.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas (Estado: " + response.status + ")");
            return response.json();
        })
        .then(data => {
            datosAves = data;

            // 2. INICIALIZAR EL SELECT ORIGINAL
            inicializarSelectAve(document.querySelector("select[name='especie_ave']"), data);

            // 3. DELEGACIÓN DE EVENTOS PARA CLONES FUTUROS
            document.addEventListener('change', function(e) {
                // Detectar cuando se selecciona una especie en CUALQUIER select de ave
                if (e.target && e.target.name && e.target.name.startsWith('especie_ave')) {
                    const select = e.target;

                    // Si el select ya tiene opciones (fue clonado), no hacer nada
                    if (select.querySelector('option[value]')) {
                        // Solo procesar el cambio si tenemos datos
                        if (datosAves) {
                            procesarCambioEspecie(select, datosAves);
                        }
                    } else {
                        // Si está vacío (nuevo clon), inicializarlo
                        inicializarSelectAve(select, datosAves);
                    }
                }
            });

            // 4. INICIALIZAR SELECTS QUE YA EXISTAN (por si hay clones al recargar)
            const selectsExistentes = document.querySelectorAll("select[name^='especie_ave']");
            selectsExistentes.forEach((select, index) => {
                if (index > 0) { // El primero ya se inicializó
                    inicializarSelectAve(select, data);
                }
            });

        })
        .catch(error => console.error("Error al cargar o procesar las razas:", error));

    // FUNCIÓN PARA INICIALIZAR UN SELECT DE AVE
    function inicializarSelectAve(selectElement, data) {
        if (!selectElement || !data) return;

        // Limpiar opciones existentes (excepto la primera)
        while (selectElement.options.length > 1) {
            selectElement.remove(1);
        }

        // Agregar opciones de aves
        data.forEach(ave => {
            const option = document.createElement("option");
            option.value = ave.id_ave;
            option.textContent = ave.ave;
            selectElement.appendChild(option);
        });

        // Si ya tiene un valor seleccionado, procesarlo
        if (selectElement.value) {
            procesarCambioEspecie(selectElement, data);
        }
    }

    // FUNCIÓN PARA PROCESAR EL CAMBIO DE ESPECIE
    function procesarCambioEspecie(selectElement, data) {
        const idSeleccionado = selectElement.value;
        const seccionPadre = selectElement.closest('.section');

        if (!seccionPadre) return;

        // Buscar los inputs de esta sección específica
        // Usamos IDs relativos o buscamos por nombre con sufijo
        const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

        // Buscar los inputs correctos para esta sección
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