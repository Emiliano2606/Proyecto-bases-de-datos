// === Función para renombrar IDs internos ===
function renombrarIDsInternos(clon, contador) {

    // Renombrar IDs internos
    clon.querySelectorAll("[id]").forEach(el => {
        el.id = el.id + "_" + contador;
    });

    // Renombrar NAME internos
    clon.querySelectorAll("[name]").forEach(el => {
        el.name = el.name + "_" + contador;
    });
}


// === Selección de botones y elementos ===
const botonPerro = document.getElementById("agregarMascota");
const botonGato = document.getElementById("agregarMascotaGato");
const selectorTipo = document.getElementById("tipoMascota");
const contenedorPerros = document.getElementById("mascotasContainer");
const contenedorGatos = document.getElementById("mascotasContainerGato");

// Ocultamos ambos botones al inicio
botonPerro.style.display = "none";
botonGato.style.display = "none";

// Escuchamos cambios en el selector
selectorTipo.addEventListener("change", () => {
    const tipo = selectorTipo.value;

    // Ocultamos ambos primero
    botonPerro.style.display = "none";
    botonGato.style.display = "none";

    // Mostramos solo el botón correspondiente
    if (tipo === "Perro") {
        botonPerro.style.display = "block";
    } else if (tipo === "Gato") {
        botonGato.style.display = "block";
    }
});

// === Función para agregar y eliminar mascotas (Perro) ===
let contadorPerro = 1;

botonPerro.addEventListener("click", () => {
    const original = document.querySelector("#sectionPerro");
    if (!original) {
        console.error("❌ No se encontró el bloque #sectionPerro");
        return;
    }

    contadorPerro++;
    const clon = original.cloneNode(true);
    clon.id = `sectionPerro_${contadorPerro}`;

    renombrarIDsInternos(clon, contadorPerro);

    // Limpiar los campos del clon
    clon.querySelectorAll("input, select").forEach(campo => campo.value = "");

    // Crear botón eliminar
    const botonEliminar = document.createElement("button");
    botonEliminar.textContent = "🗑️ Eliminar esta mascota";
    botonEliminar.className = "sape eliminarMascota p-2 mt-3";
    botonEliminar.type = "button";
    botonEliminar.addEventListener("click", () => clon.remove());

    clon.appendChild(botonEliminar);

    // Agregar el clon al contenedor de perros
    contenedorPerros.appendChild(clon);
});

// === Función para agregar y eliminar mascotas (Gato) ===
let contadorGato = 1;

botonGato.addEventListener("click", () => {
    const original = document.querySelector("#sectionGato");
    if (!original) {
        console.error("❌ No se encontró el bloque #sectionGato");
        return;
    }

    contadorGato++;
    const clon = original.cloneNode(true);
    clon.id = `sectionGato_${contadorGato}`;

    renombrarIDsInternos(clon, contadorGato);

    // Limpiar los campos del clon
    clon.querySelectorAll("input, select").forEach(campo => campo.value = "");

    // Crear botón eliminar
    const botonEliminar = document.createElement("button");
    botonEliminar.textContent = "🗑️ Eliminar esta mascota";
    botonEliminar.className = "sape eliminarMascota p-2 mt-3";
    botonEliminar.type = "button";
    botonEliminar.addEventListener("click", () => clon.remove());

    clon.appendChild(botonEliminar);

    // Agregar el clon al contenedor de gatos
    contenedorGatos.appendChild(clon);
});

// === Función para agregar y eliminar mascotas (Ave) ===
let contadorAve = 1;

const botonAve = document.querySelector("#agregarMascotaAve");
const contenedorAves = document.querySelector("#mascotasContainerAve");

botonAve.addEventListener("click", () => {
    const original = document.querySelector("#sectionAve");
    if (!original) {
        console.error("❌ No se encontró el bloque #sectionAve");
        return;
    }

    contadorAve++;
    const clon = original.cloneNode(true);
    clon.id = `sectionAve_${contadorAve}`;

    renombrarIDsInternos(clon, contadorAve);

    // Limpiar los campos del clon
    clon.querySelectorAll("input, select, textarea").forEach(campo => campo.value = "");

    // Crear botón eliminar
    const botonEliminar = document.createElement("button");
    botonEliminar.textContent = "🗑️ Eliminar esta mascota";
    botonEliminar.className = "sape eliminarMascota p-2 mt-3";
    botonEliminar.type = "button";
    botonEliminar.addEventListener("click", () => clon.remove());

    clon.appendChild(botonEliminar);

    // Agregar el clon al contenedor de aves
    contenedorAves.appendChild(clon);
});

document.addEventListener("DOMContentLoaded", function() {

    const botonLagarto = document.getElementById("agregarMascotaLagarto");
    const contenedorLagarto = document.getElementById("mascotasContainerLagarto");
    const bloqueOriginal = document.getElementById("sectionLagarto");

    let contadorLagarto = 1;

    botonLagarto.addEventListener("click", () => {

        const clon = bloqueOriginal.cloneNode(true);
        contadorLagarto++;

        clon.id = `sectionLagarto_${contadorLagarto}`;

        renombrarIDsInternos(clon, contadorLagarto);

        clon.querySelectorAll("input, select").forEach(campo => campo.value = "");

        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = "🗑️ Eliminar este lagarto";
        btnEliminar.type = "button";
        btnEliminar.className = "sape eliminarMascota p-2 mt-3";

        btnEliminar.addEventListener("click", () => clon.remove());

        clon.appendChild(btnEliminar);

        contenedorLagarto.appendChild(clon);

    });

});

document.addEventListener("DOMContentLoaded", function() {

    /* =====================
       SECCIÓN SERPIENTE
    ====================== */
    const botonSerpiente = document.getElementById("agregarMascotaSerpiente");
    const contenedorSerpiente = document.getElementById("mascotasContainerSerpiente");
    const bloqueOriginalSerpiente = document.getElementById("sectionSerpiente");

    let contadorSerpiente = 1;

    botonSerpiente.addEventListener("click", () => {

        const clon = bloqueOriginalSerpiente.cloneNode(true);
        contadorSerpiente++;

        clon.id = `sectionSerpiente_${contadorSerpiente}`;

        renombrarIDsInternos(clon, contadorSerpiente);

        clon.querySelectorAll("input, select").forEach(campo => campo.value = "");

        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = "🗑️ Eliminar esta serpiente";
        btnEliminar.type = "button";
        btnEliminar.className = "sape eliminarMascota p-2 mt-3";

        btnEliminar.addEventListener("click", () => clon.remove());

        clon.appendChild(btnEliminar);

        contenedorSerpiente.appendChild(clon);

    });

    /* =====================
       SECCIÓN TORTUGA
    ====================== */

    const botonTortuga = document.getElementById("agregarMascotaTortuga");
    const contenedorTortuga = document.getElementById("mascotasContainerTortuga");
    const bloqueOriginalTortuga = document.getElementById("sectionTortuga");

    let contadorTortuga = 1;

    botonTortuga.addEventListener("click", () => {

        const clon = bloqueOriginalTortuga.cloneNode(true);
        contadorTortuga++;

        clon.id = `sectionTortuga_${contadorTortuga}`;

        renombrarIDsInternos(clon, contadorTortuga);

        clon.querySelectorAll("input, select").forEach(campo => campo.value = "");

        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = "🗑️ Eliminar esta tortuga";
        btnEliminar.type = "button";
        btnEliminar.className = "sape eliminarMascota p-2 mt-3";

        btnEliminar.addEventListener("click", () => clon.remove());

        clon.appendChild(btnEliminar);

        contenedorTortuga.appendChild(clon);

    });

});