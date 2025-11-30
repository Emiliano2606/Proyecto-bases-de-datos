document.addEventListener("DOMContentLoaded", function() {
    const selectEspecie = document.querySelector("select[name='especie_serpiente']");
    const tamanoInput = document.getElementById("tamano_serpiente");
    const clasificacionInput = document.getElementById("clasificacion_serpiente");
    const estatusInput = document.getElementById("estatus_serpiente");
    const fuenteCalorSelect = document.getElementById("fuente_calor_serpiente");
    const terrarioSelect = document.getElementById("tipo_terrario_serpiente");
    const alimentosSelect = document.getElementById("alimentos_serpiente");
    const vecesComidaInput = document.getElementById("veces_comida_serpiente");

    // Cargar el JSON de serpientes
    fetch("../razas_serpientes.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas de serpientes.");
            return response.json();
        })
        .then(data => {
            // Llenar el select con las especies
            data.forEach(serpiente => {
                const option = document.createElement("option");
                option.value = serpiente.id_raza;
                option.textContent = serpiente.nombre;
                selectEspecie.appendChild(option);
            });

            // Evento al seleccionar una especie
            selectEspecie.addEventListener("change", function() {
                const serpienteSeleccionada = data.find(s => s.id_raza == this.value);

                if (serpienteSeleccionada) {
                    tamanoInput.value = serpienteSeleccionada["tamano_sugerido"] || "";
                    clasificacionInput.value = serpienteSeleccionada["clasificación_taxonómica"] || "";
                    estatusInput.value = serpienteSeleccionada["estatus_conservación"] || "";

                    // Fuente de calor
                    Array.from(fuenteCalorSelect.options).forEach(opt => {
                        opt.selected = opt.value === serpienteSeleccionada["fuente_calor_sugerida"];
                    });

                    // Tipo de terrario
                    Array.from(terrarioSelect.options).forEach(opt => {
                        opt.selected = opt.value === serpienteSeleccionada["tipo_terrario_sugerido"];
                    });

                    // Alimentos
                    Array.from(alimentosSelect.options).forEach(opt => {
                        opt.selected = serpienteSeleccionada["dieta_base"].includes(opt.value);
                    });

                    vecesComidaInput.value = serpienteSeleccionada["veces_comida_sugeridas"] || "";


                } else {
                    tamanoInput.value = "";
                    clasificacionInput.value = "";
                    estatusInput.value = "";
                    fuenteCalorSelect.selectedIndex = 0;
                    terrarioSelect.selectedIndex = 0;
                    alimentosSelect.selectedIndex = 0;
                    vecesComidaInput.value = "";
                }
            });
        })
        .catch(error => console.error("Error al cargar las razas de serpientes:", error));
});