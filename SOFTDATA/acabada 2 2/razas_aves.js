document.addEventListener("DOMContentLoaded", function() {
    // 1. SELECTORES DE ELEMENTOS HTML
    // Se selecciona el <select>
    const selectAve = document.querySelector("select[name='especie_ave']");

    // Se seleccionan los <input> de solo lectura por su ID
    const grupoInput = document.getElementById("grupo_taxonomico");
    const conservacionInput = document.getElementById("conservacion_ave");
    const clasificacionInput = document.getElementById("clasificacion_autoridades");

    // Verificar que el SELECT existe para evitar errores
    if (!selectAve) {
        console.error("Error: El elemento SELECT con name='especie_ave' no fue encontrado.");
        return;
    }

    // 2. CARGAR EL ARCHIVO JSON DE AVES
    // NOTA: La ruta '../razas_aves.json' debe ser correcta
    fetch("../razas_aves.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas (Estado: " + response.status + ")");
            return response.json();
        })
        .then(data => {
            // 'data' es el array completo de todas las aves

            // 3. LLENAR EL SELECT CON LAS AVES
            data.forEach(ave => {
                const option = document.createElement("option");
                option.value = ave.id_ave;
                option.textContent = ave.ave;
                selectAve.appendChild(option);
            });

            // 4. EVENTO AL SELECCIONAR UN AVE (RELLENAR CAMPOS)
            selectAve.addEventListener("change", function() {
                // El valor seleccionado es el ID del ave
                const idSeleccionado = this.value;

                // Buscar el objeto de ave que coincida con ese ID
                const aveSeleccionada = data.find(a => a.id_ave == idSeleccionado);

                if (aveSeleccionada) {
                    // ASIGNACIÓN CORRECTA DE VALORES USANDO LOS NOMBRES DEL JSON
                    grupoInput.value = aveSeleccionada.grupo_taxonómico || "";
                    conservacionInput.value = aveSeleccionada.estatus_conservación || "";
                    clasificacionInput.value = aveSeleccionada.clasificación_autoridades || "";
                } else {
                    // Limpiar los campos si se selecciona la opción "Selecciona especie"
                    grupoInput.value = "";
                    conservacionInput.value = "";
                    clasificacionInput.value = "";
                }
            });

            // Opcional: Ejecutar el evento 'change' una vez si ya hay un valor seleccionado al cargar la página
            if (selectAve.value) {
                selectAve.dispatchEvent(new Event('change'));
            }

        })
        .catch(error => console.error("Error al cargar o procesar las razas:", error));
});