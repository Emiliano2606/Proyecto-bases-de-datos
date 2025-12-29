// ==============================================
// SISTEMA DE AUTOCOMPLETADO - SERPIENTES
// ==============================================

document.addEventListener("DOMContentLoaded", function() {
    let datosSerpientes = [];

    // 1. Cargar el JSON una sola vez al inicio
    fetch("../razas_serpientes.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas de serpientes.");
            return response.json();
        })
        .then(data => {
            datosSerpientes = data;
            console.log("🐍 Datos de serpientes cargados");

            // Llenar los selects que ya existan en el DOM al cargar (la primera serpiente)
            document.querySelectorAll("select[name^='especie_serpiente']").forEach(select => {
                llenarSelectSerpiente(select, datosSerpientes);
            });
        })
        .catch(error => console.error("Error:", error));

    // 2. Función para llenar opciones en el select
    function llenarSelectSerpiente(select, data) {
        if (select.options.length > 1) return; // Evitar duplicar si ya tiene opciones
        data.forEach(serpiente => {
            const option = document.createElement("option");
            option.value = serpiente.id_raza;
            option.textContent = serpiente.nombre;
            select.appendChild(option);
        });
    }

    // 3. DELEGACIÓN DE EVENTOS: Escuchar cambios en cualquier parte del documento
    document.addEventListener("change", function(e) {
        // Detectar si el cambio fue en un select de especie_serpiente
        if (e.target && e.target.name && e.target.name.startsWith("especie_serpiente")) {
            const selectActual = e.target;
            const seccionPadre = selectActual.closest('.section'); // Buscamos el contenedor del animal actual
            const idSeleccionado = selectActual.value;

            const serpienteInfo = datosSerpientes.find(s => s.id_raza == idSeleccionado);

            // Buscamos los campos usando selectores que coincidan con el inicio del nombre
            const tamanoInput = seccionPadre.querySelector("[name^='tamano_serpiente']");
            const clasificacionInput = seccionPadre.querySelector("[name^='clasificacion_serpiente']");
            const estatusInput = seccionPadre.querySelector("[name^='estatus_serpiente']");
            const fuenteCalorSelect = seccionPadre.querySelector("[name^='fuente_calor_serpiente']");
            const terrarioSelect = seccionPadre.querySelector("[name^='tipo_terrario_serpiente']");
            const alimentosSelect = seccionPadre.querySelector("[name^='alimentos_serpiente']");
            const vecesComidaInput = seccionPadre.querySelector("[name^='veces_comida_serpiente']");

            if (serpienteInfo) {
                // Autocompletar con datos del JSON
                if (tamanoInput) tamanoInput.value = serpienteInfo["tamano_sugerido"] || "";
                if (clasificacionInput) clasificacionInput.value = serpienteInfo["clasificación_taxonómica"] || "";
                if (estatusInput) estatusInput.value = serpienteInfo["estatus_conservación"] || "";
                if (vecesComidaInput) vecesComidaInput.value = serpienteInfo["veces_comida_sugeridas"] || "";

                // Fuente de calor (Select)
                if (fuenteCalorSelect) {
                    Array.from(fuenteCalorSelect.options).forEach(opt => {
                        opt.selected = opt.value === serpienteInfo["fuente_calor_sugerida"];
                    });
                }

                // Tipo de terrario (Select)
                if (terrarioSelect) {
                    Array.from(terrarioSelect.options).forEach(opt => {
                        opt.selected = opt.value === serpienteInfo["tipo_terrario_sugerido"];
                    });
                }

                // Alimentos / Dieta base (Select)
                if (alimentosSelect) {
                    Array.from(alimentosSelect.options).forEach(opt => {
                        // Verificamos si el valor de la opción está incluido en la dieta base del JSON
                        opt.selected = serpienteInfo["dieta_base"].includes(opt.value);
                    });
                }
            } else {
                // Limpiar si no hay selección (opción por defecto)
                [tamanoInput, clasificacionInput, estatusInput, vecesComidaInput].forEach(i => { if (i) i.value = ""; });
                [fuenteCalorSelect, terrarioSelect, alimentosSelect].forEach(s => { if (s) s.selectedIndex = 0; });
            }
        }
    });

    // 4. MutationObserver: Detectar cuando se agrega una nueva serpiente (_2, _3, etc.)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) { // Si es un elemento HTML
                    const nuevosSelects = node.querySelectorAll("select[name^='especie_serpiente']");
                    nuevosSelects.forEach(sel => llenarSelectSerpiente(sel, datosSerpientes));
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});