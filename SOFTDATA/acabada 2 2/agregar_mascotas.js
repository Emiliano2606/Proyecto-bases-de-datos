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