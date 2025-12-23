// === Función para renombrar IDs y Names internos ===
function renombrarIDsInternos(clon, contador) {
    clon.querySelectorAll("[id]").forEach(el => {
        el.id = el.id + "_" + contador;
    });
    clon.querySelectorAll("[name]").forEach(el => {
        el.name = el.name + "_" + contador;
    });
}

// === Selección de elementos del DOM ===
const selectorTipo = document.getElementById("tipoMascota");
const botonesEspecies = {
    "Perro": document.getElementById("agregarMascota"),
    "Gato": document.getElementById("agregarMascotaGato"),
    "Ave": document.getElementById("agregarMascotaAve"),
    "Lagarto": document.getElementById("agregarMascotaLagarto"),
    "Serpiente": document.getElementById("agregarMascotaSerpiente"),
    "Tortuga": document.getElementById("agregarMascotaTortuga")
};

const contenedores = {
    "Perro": document.getElementById("mascotasContainer"),
    "Gato": document.getElementById("mascotasContainerGato"),
    "Ave": document.getElementById("mascotasContainerAve"),
    "Lagarto": document.getElementById("mascotasContainerLagarto"),
    "Serpiente": document.getElementById("mascotasContainerSerpiente"),
    "Tortuga": document.getElementById("mascotasContainerTortuga")
};

// Contadores para cada especie
const contadores = { "Perro": 1, "Gato": 1, "Ave": 1, "Lagarto": 1, "Serpiente": 1, "Tortuga": 1 };

// Ocultar todos los botones al inicio
Object.values(botonesEspecies).forEach(btn => { if (btn) btn.style.display = "none"; });

// Mostrar botón según especie seleccionada
selectorTipo.addEventListener("change", () => {
    Object.values(botonesEspecies).forEach(btn => { if (btn) btn.style.display = "none"; });
    const btnActivo = botonesEspecies[selectorTipo.value];
    if (btnActivo) btnActivo.style.display = "block";
});

/**
 * Función genérica para clonar cualquier sección de especie
 */
function configurarBotonAgregar(especie, idSeccionOriginal) {
    const boton = botonesEspecies[especie];
    if (!boton) return;

    boton.addEventListener("click", () => {
        const original = document.querySelector(`#${idSeccionOriginal}`);
        if (!original) return;

        contadores[especie]++;
        const num = contadores[especie];
        const clon = original.cloneNode(true);

        clon.id = `${idSeccionOriginal}_${num}`;
        renombrarIDsInternos(clon, num);

        // Limpiar campos
        clon.querySelectorAll("input, select, textarea").forEach(campo => {
            if (campo.type !== 'checkbox' && campo.type !== 'radio') campo.value = "";
            if (campo.type === 'checkbox') campo.checked = false;
        });

        // Caso especial: Reiniciar vacunas si es Perro
        if (especie === "Perro") {
            const contVacunas = clon.querySelector('[id^="lista-vacunas-checkbox"]');
            const contFechas = clon.querySelector('[id^="fechas-vacunas-container"]');
            if (contFechas) contFechas.innerHTML = '';
            if (contVacunas) cargarVacunasEnContenedor(contVacunas);
        }

        // Botón eliminar
        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = `🗑️ Eliminar esta mascota`;
        btnEliminar.className = "sape eliminarMascota p-2 mt-3";
        btnEliminar.type = "button";
        btnEliminar.onclick = () => clon.remove();
        clon.appendChild(btnEliminar);

        contenedores[especie].appendChild(clon);
    });
}

// Inicializar botones para cada especie
document.addEventListener("DOMContentLoaded", () => {
    configurarBotonAgregar("Perro", "sectionPerro");
    configurarBotonAgregar("Gato", "sectionGato");
    configurarBotonAgregar("Ave", "sectionAve");
    configurarBotonAgregar("Lagarto", "sectionLagarto");
    configurarBotonAgregar("Serpiente", "sectionSerpiente");
    configurarBotonAgregar("Tortuga", "sectionTortuga");

    // Cargar vacunas iniciales para el primer perro
    const contVacunasInicial = document.getElementById('lista-vacunas-checkbox');
    if (contVacunasInicial) cargarVacunasEnContenedor(contVacunasInicial);
});