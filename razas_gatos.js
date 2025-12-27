document.addEventListener("DOMContentLoaded", function() {
    let razasDataGatos = []; // Guardamos los datos globalmente para no hacer fetch cada vez

    // 1. Cargar el JSON una sola vez al inicio
    fetch("../razas_gatos.json")
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo.");
            return response.json();
        })
        .then(data => {
            razasDataGatos = data;
            // Llenar el primer select (el que ya existe en el HTML)
            const primerSelect = document.querySelector("select[name='raza_del_gato']");
            if (primerSelect) llenarOpcionesRaza(primerSelect);
        })
        .catch(error => console.error("Error al cargar las razas:", error));

    // Función para llenar los options de cualquier select de raza gato
    function llenarOpcionesRaza(selectElement) {
        razasDataGatos.forEach(raza => {
            const option = document.createElement("option");
            option.value = raza.id_raza;
            option.textContent = raza.nombre;
            selectElement.appendChild(option);
        });
    }

    // 2. ESCUCHA DE EVENTOS DINÁMICA (Delegación)
    document.addEventListener("change", function(event) {
        // Verificamos si el cambio fue en un select de raza de gato
        // Buscamos por el atributo name porque el ID puede cambiar a raza_del_gato_2, etc.
        if (event.target && event.target.name.startsWith("raza_del_gato")) {

            const selectActual = event.target;
            // Extraemos el sufijo (si es raza_del_gato_2, el sufijo es _2. Si es raza_del_gato, es vacío)
            const sufijo = selectActual.name.replace("raza_del_gato", "");

            // Buscamos los campos correspondientes usando ese sufijo
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

                // Rango de peso
                if (pesoSelect) {
                    pesoSelect.innerHTML = "";
                    if (razaSeleccionada.rango_peso) {
                        const pesoOption = document.createElement("option");
                        pesoOption.textContent = razaSeleccionada.rango_peso + " kg";
                        pesoSelect.appendChild(pesoOption);
                    }
                }

                // Tipo de pelaje
                if (pelajeSelect) {
                    pelajeSelect.innerHTML = "";
                    if (razaSeleccionada.tipo_pelaje) {
                        const pelajeOption = document.createElement("option");
                        pelajeOption.textContent = razaSeleccionada.tipo_pelaje;
                        pelajeSelect.appendChild(pelajeOption);
                    }
                }

                // Características físicas
                if (caracterInput) {
                    // Si es un INPUT usa .value, si es un DIV/SPAN usa .innerHTML
                    if (caracterInput.tagName === "INPUT") {
                        caracterInput.value = razaSeleccionada.caracteristicas || "";
                    } else {
                        caracterInput.innerHTML = razaSeleccionada.caracteristicas || "";
                    }
                }

            } else {
                // Limpiar campos si no hay selección
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