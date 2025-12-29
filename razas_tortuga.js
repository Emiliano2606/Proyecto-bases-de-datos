document.addEventListener("DOMContentLoaded", function() {
    let datosTortugas = [];

    // 1. Cargar el JSON
    fetch("../razas_tortuga.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de tortugas.");
            return response.json();
        })
        .then(data => {
            datosTortugas = data;
            // Llenar los selects iniciales
            document.querySelectorAll("select[name^='especie_tortuga']").forEach(select => {
                llenarSelectEspeciesTortuga(select, datosTortugas);
            });
        })
        .catch(error => console.error("Error:", error));

    function llenarSelectEspeciesTortuga(select, data) {
        if (select.options.length > 1) return;
        data.forEach(t => {
            const option = document.createElement("option");
            option.value = t.id_raza;
            option.textContent = t.nombre;
            select.appendChild(option);
        });
    }

    // 2. Delegación de Eventos para Autocompletado
    document.addEventListener("change", function(e) {
        if (e.target && e.target.name && e.target.name.startsWith("especie_tortuga")) {
            const selectActual = e.target;
            const seccionPadre = selectActual.closest('.section');
            const idSeleccionado = selectActual.value;
            const tortugaInfo = datosTortugas.find(t => t.id_raza == idSeleccionado);

            // Referencias a los campos de la sección
            const tamanoInput = seccionPadre.querySelector("[name^='tamano_tortuga']");
            const clasificacionInput = seccionPadre.querySelector("[name^='clasificacion_tortuga']");
            const estatusInput = seccionPadre.querySelector("[name^='estatus_tortuga']");
            const fuenteCalorSelect = seccionPadre.querySelector("[name^='fuente_calor_tortuga']");
            const tipoTerrarioInput = seccionPadre.querySelector("[name^='tipo_terrario_tortuga']");
            const alimentosSelect = seccionPadre.querySelector("[name^='alimentos_tortuga']");
            const vecesComidaInput = seccionPadre.querySelector("[name^='veces_comida_tortuga']");
            // Nota: tratamiento_tortuga NO se autocompleta por petición del usuario

            if (tortugaInfo) {
                if (tamanoInput) tamanoInput.value = tortugaInfo.tamano_sugerido || "";
                if (clasificacionInput) clasificacionInput.value = tortugaInfo.clasificación_taxonómica || "";
                if (estatusInput) estatusInput.value = tortugaInfo.estatus_conservación || "";
                if (vecesComidaInput) vecesComidaInput.value = tortugaInfo.veces_comida_sugeridas || "";
                if (tipoTerrarioInput) tipoTerrarioInput.value = tortugaInfo.tipo_terrario_sugerido || "";

                // Fuente de Calor
                if (fuenteCalorSelect) {
                    Array.from(fuenteCalorSelect.options).forEach(opt => {
                        opt.selected = opt.value === tortugaInfo.fuente_calor_sugerida;
                    });
                }

                // Alimentos (Seleccionar los que coincidan en el multiselect)
                if (alimentosSelect) {
                    const dietaJson = tortugaInfo.dieta_base || [];
                    Array.from(alimentosSelect.options).forEach(opt => {
                        // Comprueba si el texto o el valor están en el array del JSON
                        opt.selected = dietaJson.includes(opt.value) || dietaJson.includes(opt.text);
                    });
                }
            } else {
                // Limpiar campos si la opción es vacía
                [tamanoInput, clasificacionInput, estatusInput, vecesComidaInput, tipoTerrarioInput].forEach(i => { if (i) i.value = ""; });
                if (fuenteCalorSelect) fuenteCalorSelect.selectedIndex = 0;
                if (alimentosSelect) Array.from(alimentosSelect.options).forEach(o => o.selected = false);
            }
        }
    });

    // 3. MutationObserver para Clones
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    const nuevosSelects = node.querySelectorAll("select[name^='especie_tortuga']");
                    nuevosSelects.forEach(sel => llenarSelectEspeciesTortuga(sel, datosTortugas));
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});