document.addEventListener("DOMContentLoaded", function() {
    let datosLagartos = [];

    fetch("../razas_lagartos.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas.");
            return response.json();
        })
        .then(data => {
            datosLagartos = data;
            console.log(" Datos de lagartos cargados");

            document.querySelectorAll("select[name^='especie_lagarto']").forEach(select => {
                llenarSelectLagarto(select, datosLagartos);
            });
        })
        .catch(error => console.error("Error:", error));

    function llenarSelectLagarto(select, data) {
        if (select.options.length > 1) return;
        data.forEach(lagarto => {
            const option = document.createElement("option");
            option.value = lagarto.id_raza;
            option.textContent = lagarto.nombre;
            select.appendChild(option);
        });
    }

    document.addEventListener("change", function(e) {
        if (e.target && e.target.name && e.target.name.startsWith("especie_lagarto")) {
            const selectActual = e.target;
            const seccionPadre = selectActual.closest('.section');
            const idSeleccionado = selectActual.value;

            const lagartoInfo = datosLagartos.find(l => l.id_raza == idSeleccionado);

            const tamanoInput = seccionPadre.querySelector("[name^='tamano_lagarto']");
            const clasificacionInput = seccionPadre.querySelector("[name^='clasificacion_lagarto']");
            const estatusInput = seccionPadre.querySelector("[name^='estatus_lagarto']");
            const requerimientosInput = seccionPadre.querySelector("[name^='requerimientos_ambientales_lagarto']");
            const fuenteCalorSelect = seccionPadre.querySelector("[name^='fuente_calor_lagarto']");
            const terrarioSelect = seccionPadre.querySelector("[name^='tipo_terrario_lagarto']");
            const dietaSelect = seccionPadre.querySelector("[name^='dieta_lagarto']");

            if (lagartoInfo) {
                if (tamanoInput) tamanoInput.value = lagartoInfo["tamaño_típico"] || "";
                if (clasificacionInput) clasificacionInput.value = lagartoInfo["clasificación_taxonómica"] || "";
                if (estatusInput) estatusInput.value = lagartoInfo["estatus_conservación"] || "";
                if (requerimientosInput) requerimientosInput.value = lagartoInfo["requerimiento_ambiental_clave"] || "";

                if (fuenteCalorSelect) {
                    Array.from(fuenteCalorSelect.options).forEach(opt => {
                        opt.selected = opt.value === lagartoInfo["fuente_calor_sugerida"];
                    });
                }

                if (terrarioSelect) {
                    Array.from(terrarioSelect.options).forEach(opt => {
                        opt.selected = opt.value === lagartoInfo["tipo_terrario_sugerido"];
                    });
                }

                if (dietaSelect) {
                    Array.from(dietaSelect.options).forEach(opt => {
                        opt.selected = lagartoInfo["dieta_base"].includes(opt.value);
                    });
                }
            } else {
                [tamanoInput, clasificacionInput, estatusInput, requerimientosInput].forEach(i => { if (i) i.value = ""; });
                [fuenteCalorSelect, terrarioSelect, dietaSelect].forEach(s => { if (s) s.selectedIndex = 0; });
            }
        }
    });

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    const nuevosSelects = node.querySelectorAll("select[name^='especie_lagarto']");
                    nuevosSelects.forEach(sel => llenarSelectLagarto(sel, datosLagartos));
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});