// === Función para renombrar IDs y Names internos ===
function renombrarIDsInternos(clon, contador) {
    // IDs: siempre les añadimos sufijo para mantener unicidad
    clon.querySelectorAll("[id]").forEach(el => {
        if (!el.id) return;
        // Evitar duplicar el sufijo si ya existe
        if (!el.id.match(new RegExp("_" + contador + "$"))) {
            el.id = el.id + "_" + contador;
        }
    });

    // Names: solo renombramos los que NO son arreglos (no terminan en [])
    // Los campos tipo array (ej: nombre_gato[]) deben mantenerse iguales
    clon.querySelectorAll("[name]").forEach(el => {
        const name = el.getAttribute('name');
        if (!name) return;
        // Si es un array (termina con []) lo mantenemos igual
        if (name.endsWith('[]')) return;

        // Si ya contiene un sufijo numerico, lo reemplazamos por el actual
        if (/_\d+$/.test(name)) {
            el.name = name.replace(/_\d+$/, '_' + contador);
        } else {
            el.name = name + '_' + contador;
        }
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
    // Ocultamos todos primero
    Object.values(botonesEspecies).forEach(btn => { if (btn) btn.style.display = "none"; });

    const valorSeleccionado = selectorTipo.value;

    // Si es un reptil, necesitamos ver qué sub-tipo se elige
    if (valorSeleccionado === "Lagarto" || valorSeleccionado === "Serpiente" || valorSeleccionado === "Tortuga") {
        const btnActivo = botonesEspecies[valorSeleccionado];
        if (btnActivo) btnActivo.style.display = "block";
    } else {
        // Para Perro, Gato, Ave
        const btnActivo = botonesEspecies[valorSeleccionado];
        if (btnActivo) btnActivo.style.display = "block";
    }
});

// NUEVO: Escuchador para el sub-selector de reptiles (si el usuario cambia el tipo dentro de reptil)
const selectorSubReptil = document.getElementById("tipoReptil");
if (selectorSubReptil) {
    selectorSubReptil.addEventListener("change", () => {
        // Ocultar botones de reptiles
        if (botonesEspecies["Lagarto"]) botonesEspecies["Lagarto"].style.display = "none";
        if (botonesEspecies["Serpiente"]) botonesEspecies["Serpiente"].style.display = "none";
        if (botonesEspecies["Tortuga"]) botonesEspecies["Tortuga"].style.display = "none";

        // Mostrar el específico
        const btnActivo = botonesEspecies[selectorSubReptil.value];
        if (btnActivo) btnActivo.style.display = "block";
    });
}

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

        // --- LIMPIEZA DE CONTENEDORES DINÁMICOS ---
        // Esto evita que se copien las vacunas y fechas de la mascota anterior
        const contVacunas = clon.querySelector('[id^="lista-vacunas-checkbox"]');
        const contFechas = clon.querySelector('.fechas-vacunas-dinamicas');

        if (contVacunas) contVacunas.innerHTML = ''; // Borra los checkboxes copiados
        if (contFechas) contFechas.innerHTML = ''; // Borra las fechas copiadas

        renombrarIDsInternos(clon, num);

        // Limpiar campos (inputs normales)
        clon.querySelectorAll("input, select, textarea").forEach(campo => {
            if (campo.type !== 'checkbox' && campo.type !== 'radio') campo.value = "";
            if (campo.type === 'checkbox') campo.checked = false;
        });

        // --- RECARGA DE VACUNAS LIMPIAS ---
        // Ahora cargamos las vacunas desde cero para este clon nuevo
        if (especie === "Perro") {
            cargarVacunasEnContenedor(clon.querySelector(`[id^="lista-vacunas-checkbox"]`));
        } else if (especie === "Gato") {
            // Asegúrate de tener la función cargarVacunasGatoEnContenedor disponible
            if (typeof cargarVacunasGatoEnContenedor === 'function') {
                cargarVacunasGatoEnContenedor(clon.querySelector(`[id^="lista-vacunas-checkbox"]`));
            }
        } else if (especie === "Ave") {
            if (typeof cargarVacunasAveEnContenedor === 'function') {
                cargarVacunasAveEnContenedor(clon.querySelector(`[id^="lista-vacunas-checkbox"]`));
            }
        }
        // ... dentro de configurarBotonAgregar (después de clonar y renombrar) ...

        if (especie === "Perro") {
            cargarVacunasEnContenedor(clon.querySelector('[id^="lista-vacunas-checkbox"]'));
        } else if (especie === "Gato") {
            cargarVacunasGato(clon.querySelector('[id^="lista-vacunas-checkbox2"]'));
        } else if (especie === "Ave") {
            cargarVacunasAve(clon.querySelector('[id^="lista-vacunas-checkbox3"]'));
        } else if (especie === "Lagarto") {
            // Buscamos dentro del CLON
            const contVacunasLag = clon.querySelector('[id^="lista-vacunas-checkbox4"]');
            const contFechasLag = clon.querySelector('.fechas-vacunas-dinamicas');

            if (contVacunasLag) contVacunasLag.innerHTML = '';
            if (contFechasLag) contFechasLag.innerHTML = '';

            // Cargamos los checkboxes nuevos
            cargarVacunasLagarto(contVacunasLag);
        } else if (especie === "Serpiente") {
            // 1. Ubicamos los contenedores dentro del clon
            const contVacunasSerp = clon.querySelector('[id^="lista-vacunas-checkbox5"]');
            const contFechasSerp = clon.querySelector('[id^="fechas-vacunas-container5"]') || clon.querySelector('.fechas-vacunas-dinamicas');

            // 2. Limpieza total para que el clon nazca vacío
            if (contVacunasSerp) contVacunasSerp.innerHTML = '';
            if (contFechasSerp) contFechasSerp.innerHTML = '';

            // 3. Cargamos los checkboxes frescos con el sufijo correcto
            cargarVacunasSerpiente(contVacunasSerp);
        } else if (especie === "Tortuga") {
            // 1. Ubicamos los contenedores específicos en el clon
            const contVacunasTor = clon.querySelector('[id^="lista-vacunas-checkbox6"]');
            const contFechasTor = clon.querySelector('[id^="fechas-vacunas-container6"]') || clon.querySelector('.fechas-vacunas-dinamicas');

            // 2. Limpieza preventiva
            if (contVacunasTor) contVacunasTor.innerHTML = '';
            if (contFechasTor) contFechasTor.innerHTML = '';

            // 3. Carga de datos limpios con sufijo actualizado
            cargarVacunasTortuga(contVacunasTor);
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