document.addEventListener("DOMContentLoaded", function() {
    const selectEspecie = document.getElementById("especie_tortuga");
    const tamanoInput = document.getElementById("tamano_tortuga");
    const clasificacionInput = document.getElementById("clasificacion_tortuga");
    const estatusInput = document.getElementById("estatus_tortuga");
    const fuenteCalorSelect = document.getElementById("fuente_calor_tortuga");
    const tipoTerrarioInput = document.getElementById("tipo_terrario_tortuga");
    const alimentosSelect = document.getElementById("alimentos_tortuga");
    const tratamientosSelect = document.getElementById("tratamientos_tortuga");
    const vecesComidaInput = document.getElementById("veces_comida_tortuga");
    const dimensionesTerrarioInput = document.getElementById("dimensiones_terrario_tortuga");

    fetch("../razas_tortuga.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas de tortugas.");
            return response.json();
        })
        .then(data => {
            // Llenar select de especies
            data.forEach(tortuga => {
                const option = document.createElement("option");
                option.value = tortuga.id_raza;
                option.textContent = tortuga.nombre;
                selectEspecie.appendChild(option);
            });

            selectEspecie.addEventListener("change", function() {
                const tortugaSeleccionada = data.find(t => t.id_raza == this.value);

                if (tortugaSeleccionada) {
                    // Campos de texto
                    tamanoInput.value = tortugaSeleccionada["tamano_sugerido"] || "";
                    clasificacionInput.value = tortugaSeleccionada["clasificación_taxonómica"] || "";
                    estatusInput.value = tortugaSeleccionada["estatus_conservación"] || "";
                    vecesComidaInput.value = tortugaSeleccionada["veces_comida_sugeridas"] || "";
                    tipoTerrarioInput.value = tortugaSeleccionada["tipo_terrario_sugerido"] || "";
                    dimensionesTerrarioInput.value = ""; // no hay info en el JSON

                    // Fuente de calor
                    Array.from(fuenteCalorSelect.options).forEach(opt => {
                        opt.selected = opt.value === tortugaSeleccionada["fuente_calor_sugerida"];
                    });

                    // Alimentos (llenar y seleccionar desde JSON)
                    alimentosSelect.innerHTML = "";
                    tortugaSeleccionada["dieta_base"].forEach(alimento => {
                        const opt = document.createElement("option");
                        opt.value = alimento;
                        opt.textContent = alimento;
                        opt.selected = true;
                        alimentosSelect.appendChild(opt);
                    });

                    // Tratamientos (llenar y seleccionar desde JSON)
                    tratamientosSelect.innerHTML = "";
                    const tratamiento = tortugaSeleccionada["tratamiento_sugerido"];
                    if (tratamiento) {
                        const opt = document.createElement("option");
                        opt.value = tratamiento;
                        opt.textContent = tratamiento;
                        opt.selected = true;
                        tratamientosSelect.appendChild(opt);
                    }

                } else {
                    // Limpiar todo si no hay selección
                    tamanoInput.value = "";
                    clasificacionInput.value = "";
                    estatusInput.value = "";
                    vecesComidaInput.value = "";
                    tipoTerrarioInput.value = "";
                    dimensionesTerrarioInput.value = "";
                    fuenteCalorSelect.selectedIndex = 0;
                    alimentosSelect.innerHTML = "";
                    tratamientosSelect.innerHTML = "";
                }
            });
        })
        .catch(error => console.error("Error al cargar las razas de tortugas:", error));
});