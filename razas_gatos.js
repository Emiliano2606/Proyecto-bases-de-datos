document.addEventListener("DOMContentLoaded", function() {
    let razasDataGatos = [];

    fetch("../razas_gatos.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo.");
            return response.json();
        })
        .then(data => {
            razasDataGatos = data;
            const primerSelect = document.querySelector("select[name='raza_del_gato']");
            if (primerSelect) llenarOpcionesRaza(primerSelect);
        })
        .catch(error => console.error("Error al cargar las razas:", error));

    function llenarOpcionesRaza(selectElement) {
        razasDataGatos.forEach(raza => {
            const option = document.createElement("option");
            option.value = raza.id_raza;
            option.textContent = raza.nombre;
            selectElement.appendChild(option);
        });
    }

    document.addEventListener("change", function(event) {

        if (event.target && event.target.name.startsWith("raza_del_gato")) {

            const selectActual = event.target;
            const sufijo = selectActual.name.replace("raza_del_gato", "");

            const grupoInput = document.getElementById("grupo_gato" + sufijo);
            const registroInput = document.getElementById("seccion_gato" + sufijo);
            const tamanoInput = document.getElementById("tamano_gato" + sufijo);
            const pesoSelect = document.getElementById("peso_gato" + sufijo);
            const pelajeSelect = document.getElementById("tipopelaje_gato" + sufijo);
            const caracterInput = document.getElementById("caracterfisicas_gato" + sufijo);

            const razaSeleccionada = razasDataGatos.find(r => r.id_raza == selectActual.value);

            if (razaSeleccionada) {
                if (grupoInput) grupoInput.value = razaSeleccionada.grupo_raza || "";
                if (registroInput) registroInput.value = razaSeleccionada.registro_principal || "";
                if (tamanoInput) tamanoInput.value = razaSeleccionada.tamano || "";

                if (pesoSelect) {
                    pesoSelect.innerHTML = "";
                    if (razaSeleccionada.rango_peso) {
                        const pesoOption = document.createElement("option");
                        pesoOption.textContent = razaSeleccionada.rango_peso + " kg";
                        pesoSelect.appendChild(pesoOption);
                    }
                }

                if (pelajeSelect) {
                    pelajeSelect.innerHTML = "";
                    if (razaSeleccionada.tipo_pelaje) {
                        const pelajeOption = document.createElement("option");
                        pelajeOption.textContent = razaSeleccionada.tipo_pelaje;
                        pelajeSelect.appendChild(pelajeOption);
                    }
                }

                if (caracterInput) {
                    if (caracterInput.tagName === "INPUT") {
                        caracterInput.value = razaSeleccionada.caracteristicas || "";
                    } else {
                        caracterInput.innerHTML = razaSeleccionada.caracteristicas || "";
                    }
                }

            } else {
                if (grupoInput) grupoInput.value = "";
                if (registroInput) registroInput.value = "";
                if (tamanoInput) tamanoInput.value = "";
                if (pesoSelect) pesoSelect.innerHTML = "";
                if (pelajeSelect) pelajeSelect.innerHTML = "";
                if (caracterInput) {
                    caracterInput.value = "";
                    caracterInput.innerHTML = "";
                }
            }
        }
    });
});