// ==============================================
// SISTEMA DE AUTOCOMPLETADO - LAGARTOS
// ==============================================

document.addEventListener("DOMContentLoaded", function() {
    let datosLagartos = [];

    // 1. Cargar el JSON una sola vez al inicio
    fetch("../razas_lagartos.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas.");
            return response.json();
        })
        .then(data => {
            datosLagartos = data;
            console.log("🦎 Datos de lagartos cargados");

            // Llenar los selects que ya existan en el DOM al cargar
            document.querySelectorAll("select[name^='especie_lagarto']").forEach(select => {
                llenarSelectLagarto(select, datosLagartos);
            });
        })
        .catch(error => console.error("Error:", error));

    // 2. Llenar opciones en el select
    function llenarSelectLagarto(select, data) {
        if (select.options.length > 1) return; // Ya tiene opciones
        data.forEach(lagarto => {
            const option = document.createElement("option");
            option.value = lagarto.id_raza;
            option.textContent = lagarto.nombre;
            select.appendChild(option);
        });
    }

    // 3. DELEGACIÓN DE EVENTOS: Escuchar cambios en cualquier parte del formulario
    document.addEventListener("change", function(e) {
        // Detectar si el cambio fue en un select de especie_lagarto
        if (e.target && e.target.name && e.target.name.startsWith("especie_lagarto")) {
            const selectActual = e.target;
            const seccionPadre = selectActual.closest('.section'); // Buscamos la sección actual (_2, _3, etc)
            const idSeleccionado = selectActual.value;

            const lagartoInfo = datosLagartos.find(l => l.id_raza == idSeleccionado);

            // Buscamos los campos DENTRO de esta sección específica
            const tamanoInput = seccionPadre.querySelector("[name^='tamano_lagarto']");
            const clasificacionInput = seccionPadre.querySelector("[name^='clasificacion_lagarto']");
            const estatusInput = seccionPadre.querySelector("[name^='estatus_lagarto']");
            const requerimientosInput = seccionPadre.querySelector("[name^='requerimientos_ambientales_lagarto']");
            const fuenteCalorSelect = seccionPadre.querySelector("[name^='fuente_calor_lagarto']");
            const terrarioSelect = seccionPadre.querySelector("[name^='tipo_terrario_lagarto']");
            const dietaSelect = seccionPadre.querySelector("[name^='dieta_lagarto']");

            if (lagartoInfo) {
                // Autocompletar con datos del JSON
                if (tamanoInput) tamanoInput.value = lagartoInfo["tamaño_típico"] || "";
                if (clasificacionInput) clasificacionInput.value = lagartoInfo["clasificación_taxonómica"] || "";
                if (estatusInput) estatusInput.value = lagartoInfo["estatus_conservación"] || "";
                if (requerimientosInput) requerimientosInput.value = lagartoInfo["requerimiento_ambiental_clave"] || "";

                // Fuente de calor (Select)
                if (fuenteCalorSelect) {
                    Array.from(fuenteCalorSelect.options).forEach(opt => {
                        opt.selected = opt.value === lagartoInfo["fuente_calor_sugerida"];
                    });
                }

                // Tipo de terrario (Select)
                if (terrarioSelect) {
                    Array.from(terrarioSelect.options).forEach(opt => {
                        opt.selected = opt.value === lagartoInfo["tipo_terrario_sugerido"];
                    });
                }

                // Dieta base (Select múltiple o simple)
                if (dietaSelect) {
                    Array.from(dietaSelect.options).forEach(opt => {
                        opt.selected = lagartoInfo["dieta_base"].includes(opt.value);
                    });
                }
            } else {
                // Limpiar si no hay selección
                [tamanoInput, clasificacionInput, estatusInput, requerimientosInput].forEach(i => { if (i) i.value = ""; });
                [fuenteCalorSelect, terrarioSelect, dietaSelect].forEach(s => { if (s) s.selectedIndex = 0; });
            }
        }
    });

    // 4. Observar si se agregan nuevas secciones (Lagarto 2, 3...) para llenar sus selects
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) { // Si es un elemento HTML
                    const nuevosSelects = node.querySelectorAll("select[name^='especie_lagarto']");
                    nuevosSelects.forEach(sel => llenarSelectLagarto(sel, datosLagartos));
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});