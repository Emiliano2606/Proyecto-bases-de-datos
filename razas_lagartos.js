document.addEventListener("DOMContentLoaded", function() {
    const selectEspecie = document.querySelector("select[name='especie_lagarto']");
    const tamanoInput = document.getElementById("tamano_lagarto");
    const clasificacionInput = document.getElementById("clasificacion_lagarto");
    const estatusInput = document.getElementById("estatus_lagarto");
    const requerimientosInput = document.getElementById("requerimientos_ambientales_lagarto");
    const fuenteCalorSelect = document.getElementById("fuente_calor_lagarto");
    const terrarioSelect = document.getElementById("tipo_terrario_lagarto");
    const dietaSelect = document.getElementById("dieta_lagarto");

    // Cargar el JSON de lagartos
    fetch("../razas_lagartos.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas de lagartos.");
            return response.json();
        })
        .then(data => {
            // Llenar el select con las especies
            data.forEach(lagarto => {
                const option = document.createElement("option");
                option.value = lagarto.id_raza;
                option.textContent = lagarto.nombre;
                selectEspecie.appendChild(option);
            });

            // Evento al seleccionar una especie
            selectEspecie.addEventListener("change", function() {
                const lagartoSeleccionado = data.find(l => l.id_raza == this.value);

                if (lagartoSeleccionado) {
                    tamanoInput.value = lagartoSeleccionado["tamaño_típico"] || "";
                    clasificacionInput.value = lagartoSeleccionado["clasificación_taxonómica"] || "";
                    estatusInput.value = lagartoSeleccionado["estatus_conservación"] || "";
                    requerimientosInput.value = lagartoSeleccionado["requerimiento_ambiental_clave"] || "";

                    // Fuente de calor
                    Array.from(fuenteCalorSelect.options).forEach(opt => {
                        opt.selected = opt.value === lagartoSeleccionado["fuente_calor_sugerida"];
                    });

                    // Tipo de terrario
                    Array.from(terrarioSelect.options).forEach(opt => {
                        opt.selected = opt.value === lagartoSeleccionado["tipo_terrario_sugerido"];
                    });

                    // Dieta base (seleccionamos las coincidencias)
                    Array.from(dietaSelect.options).forEach(opt => {
                        opt.selected = lagartoSeleccionado["dieta_base"].includes(opt.value);
                    });

                } else {
                    tamanoInput.value = "";
                    clasificacionInput.value = "";
                    estatusInput.value = "";
                    requerimientosInput.value = "";
                    fuenteCalorSelect.selectedIndex = 0;
                    terrarioSelect.selectedIndex = 0;
                    dietaSelect.selectedIndex = 0;
                }
            });
        })
        .catch(error => console.error("Error al cargar las razas de lagartos:", error));
});