//  Función para renombrar IDs y Names internos 
function renombrarIDsInternos(clon, contador) {
    // IDs: siempre les añadimos sufijo 
    clon.querySelectorAll("[id]").forEach(el => {
        if (!el.id) return;
        // Evitar duplicar el sufijo si ya existe
        if (!el.id.match(new RegExp("_" + contador + "$"))) {
            el.id = el.id + "_" + contador;
        }
    });

    // Names: solo renombramos 

    clon.querySelectorAll("[name]").forEach(el => {
        const name = el.getAttribute('name');
        if (!name) return;
        // Si es un array (termina con []) lo mantenemos igual
        if (name.endsWith('[]')) return;

        if (/_\d+$/.test(name)) {
            el.name = name.replace(/_\d+$/, '_' + contador);
        } else {
            el.name = name + '_' + contador;
        }
    });
}

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

const contadores = { "Perro": 1, "Gato": 1, "Ave": 1, "Lagarto": 1, "Serpiente": 1, "Tortuga": 1 };

Object.values(botonesEspecies).forEach(btn => { if (btn) btn.style.display = "none"; });

selectorTipo.addEventListener("change", () => {
    Object.values(botonesEspecies).forEach(btn => { if (btn) btn.style.display = "none"; });

    const valorSeleccionado = selectorTipo.value;

    if (valorSeleccionado === "Lagarto" || valorSeleccionado === "Serpiente" || valorSeleccionado === "Tortuga") {
        const btnActivo = botonesEspecies[valorSeleccionado];
        if (btnActivo) btnActivo.style.display = "block";
    } else {
        // Para Perro, Gato, Ave
        const btnActivo = botonesEspecies[valorSeleccionado];
        if (btnActivo) btnActivo.style.display = "block";
    }
});

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


        const contVacunas = clon.querySelector('[id^="lista-vacunas-checkbox"]');
        const contFechas = clon.querySelector('.fechas-vacunas-dinamicas');

        if (contVacunas) contVacunas.innerHTML = '';
        if (contFechas) contFechas.innerHTML = '';

        renombrarIDsInternos(clon, num);

        clon.querySelectorAll("input, select, textarea").forEach(campo => {
            if (campo.type !== 'checkbox' && campo.type !== 'radio') campo.value = "";
            if (campo.type === 'checkbox') campo.checked = false;
        });


        if (especie === "Perro") {
            cargarVacunasEnContenedor(clon.querySelector(`[id^="lista-vacunas-checkbox"]`));
        } else if (especie === "Gato") {

            if (typeof cargarVacunasGatoEnContenedor === 'function') {
                cargarVacunasGatoEnContenedor(clon.querySelector(`[id^="lista-vacunas-checkbox"]`));
            }
        } else if (especie === "Ave") {
            if (typeof cargarVacunasAveEnContenedor === 'function') {
                cargarVacunasAveEnContenedor(clon.querySelector(`[id^="lista-vacunas-checkbox"]`));
            }
        }

        if (especie === "Perro") {
            cargarVacunasEnContenedor(clon.querySelector('[id^="lista-vacunas-checkbox"]'));
        } else if (especie === "Gato") {
            cargarVacunasGato(clon.querySelector('[id^="lista-vacunas-checkbox2"]'));
        } else if (especie === "Ave") {
            cargarVacunasAve(clon.querySelector('[id^="lista-vacunas-checkbox3"]'));
        } else if (especie === "Lagarto") {
            const contVacunasLag = clon.querySelector('[id^="lista-vacunas-checkbox4"]');
            const contFechasLag = clon.querySelector('.fechas-vacunas-dinamicas');

            if (contVacunasLag) contVacunasLag.innerHTML = '';
            if (contFechasLag) contFechasLag.innerHTML = '';

            cargarVacunasLagarto(contVacunasLag);
        } else if (especie === "Serpiente") {
            const contVacunasSerp = clon.querySelector('[id^="lista-vacunas-checkbox5"]');
            const contFechasSerp = clon.querySelector('[id^="fechas-vacunas-container5"]') || clon.querySelector('.fechas-vacunas-dinamicas');

            if (contVacunasSerp) contVacunasSerp.innerHTML = '';
            if (contFechasSerp) contFechasSerp.innerHTML = '';

            cargarVacunasSerpiente(contVacunasSerp);
        } else if (especie === "Tortuga") {
            const contVacunasTor = clon.querySelector('[id^="lista-vacunas-checkbox6"]');
            const contFechasTor = clon.querySelector('[id^="fechas-vacunas-container6"]') || clon.querySelector('.fechas-vacunas-dinamicas');

            if (contVacunasTor) contVacunasTor.innerHTML = '';
            if (contFechasTor) contFechasTor.innerHTML = '';

            cargarVacunasTortuga(contVacunasTor);
        }
        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = `🗑️ Eliminar esta mascota`;
        btnEliminar.className = "sape eliminarMascota p-2 mt-3";
        btnEliminar.type = "button";
        btnEliminar.onclick = () => clon.remove();
        clon.appendChild(btnEliminar);

        contenedores[especie].appendChild(clon);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    configurarBotonAgregar("Perro", "sectionPerro");
    configurarBotonAgregar("Gato", "sectionGato");
    configurarBotonAgregar("Ave", "sectionAve");
    configurarBotonAgregar("Lagarto", "sectionLagarto");
    configurarBotonAgregar("Serpiente", "sectionSerpiente");
    configurarBotonAgregar("Tortuga", "sectionTortuga");

    const contVacunasInicial = document.getElementById('lista-vacunas-checkbox');
    if (contVacunasInicial) cargarVacunasEnContenedor(contVacunasInicial);
});