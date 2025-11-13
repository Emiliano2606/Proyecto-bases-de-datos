document.addEventListener("DOMContentLoaded", function() {
    const selectRaza = document.querySelector("select[name='raza_del_gato']");
    const grupoInput = document.getElementById("grupo_gato");
    const registroInput = document.getElementById("seccion_gato");
    const tamanoInput = document.getElementById("tamano_gato");
    const pesoSelect = document.getElementById("peso_gato");
    const pelajeSelect = document.getElementById("tipopelaje_gato");
    const caracterInput = document.getElementById("caracterfisicas_gato");

    // Cargar el JSON de razas
    fetch("../razas_gatos.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo de razas.");
            return response.json();
        })
        .then(data => {
            // Llenar el select con las razas
            data.forEach(raza => {
                const option = document.createElement("option");
                option.value = raza.id_raza;
                option.textContent = raza.nombre;
                selectRaza.appendChild(option);
            });

            // Evento al seleccionar una raza
            selectRaza.addEventListener("change", function() {
                const razaSeleccionada = data.find(r => r.id_raza == this.value);

                if (razaSeleccionada) {
                    grupoInput.value = razaSeleccionada.grupo_raza || "";
                    registroInput.value = razaSeleccionada.registro_principal || "";
                    tamanoInput.value = razaSeleccionada.tamano || "";
                    caracterInput.value = (razaSeleccionada.caracteristicas || "");
                    // Rango de peso
                    pesoSelect.innerHTML = ""; // Limpiar antes de agregar
                    if (razaSeleccionada.rango_peso) {
                        const pesoOption = document.createElement("option");
                        pesoOption.textContent = razaSeleccionada.rango_peso + " kg";
                        pesoSelect.appendChild(pesoOption);
                    }

                    // Tipo de pelaje
                    pelajeSelect.innerHTML = "";
                    if (razaSeleccionada.tipo_pelaje) {
                        const pelajeOption = document.createElement("option");
                        pelajeOption.textContent = razaSeleccionada.tipo_pelaje;
                        pelajeSelect.appendChild(pelajeOption);
                    }
                    // Tipo de pelaje
                    caracterInput.innerHTML = "";
                    if (razaSeleccionada.caracteristicas) {
                        const caracterOption = document.createElement("option");
                        caracterOption.textContent = razaSeleccionada.caracteristicas;
                        caracterInput.appendChild(caracterOption);
                    }
                    // Características físicas

                } else {
                    grupoInput.value = "";
                    registroInput.value = "";
                    tamanoInput.value = "";
                    pesoSelect.innerHTML = "";
                    pelajeSelect.innerHTML = "";
                    caracterInput.value = "";
                }
            });
        })
        .catch(error => console.error("Error al cargar las razas:", error));
});