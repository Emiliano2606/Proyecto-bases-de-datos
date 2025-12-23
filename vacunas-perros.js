const vacunasPerro = [
    { nombre: "Rabia", valor: "rabia" },
    { nombre: "Moquillo (Distemper)", valor: "moquillo" },
    { nombre: "Parvovirus", valor: "parvovirus" },
    { nombre: "Hepatitis canina (Adenovirus-2)", valor: "hepatitis" },
    { nombre: "Leptospirosis", valor: "leptospirosis" },
    { nombre: "Traqueobronquitis (Bordetella)", valor: "traqueobronquitis" },
    { nombre: "Influenza canina H3N2", valor: "influenza_h3n2" },
    { nombre: "Influenza canina H3N8", valor: "influenza_h3n8" },
    { nombre: "Coronavirus canino", valor: "coronavirus" },
    { nombre: "Giardia", valor: "giardia" },
    { nombre: "Leishmaniosis", valor: "leishmaniosis" },
    { nombre: "Polivalente (Moquillo + Hepatitis + Parvovirus)", valor: "polivalente_basica" },
    { nombre: "Polivalente Completa (Moquillo + Hepatitis + Parvovirus + Leptospirosis)", valor: "polivalente_completa" },
    { nombre: "Triple (Bordetella + Parainfluenza)", valor: "triple_respiratoria" },
    { nombre: "Vacuna contra hongosSSS (Microsporum)", valor: "hongos" }
];

/**
 * Carga los checkboxes de vacunas en un contenedor específico.
 * Detecta automáticamente si el contenedor pertenece a un clon para añadir sufijos.
 */
function cargarVacunasEnContenedor(contenedor) {
    if (!contenedor) return;
    contenedor.innerHTML = '';

    const seccionPadre = contenedor.closest('.section');
    const idPartes = seccionPadre.id.split('_');
    const sufijo = idPartes.length > 1 ? '_' + idPartes[idPartes.length - 1] : '';

    vacunasPerro.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox" name="vacunas_perro${sufijo}[]" value="${vacuna.valor}" class="vacuna-checkbox-input">
            <label style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        contenedor.appendChild(div);
    });
}

/**
 * Escucha cambios en los checkboxes de vacunas usando delegación de eventos.
 * Funciona para elementos originales y clonados.
 */
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('vacuna-checkbox-input')) {
        const seccionPadre = e.target.closest('.section');
        const containerFechas = seccionPadre.querySelector('[id^="fechas-vacunas-container"]');

        const idPartes = seccionPadre.id.split('_');
        const sufijo = idPartes.length > 1 ? '_' + idPartes[idPartes.length - 1] : '';

        actualizarFechasDeEstaMascota(seccionPadre, containerFechas, sufijo);
    }
});

function actualizarFechasDeEstaMascota(seccion, contenedor, sufijo) {
    const seleccionados = seccion.querySelectorAll('.vacuna-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        const vacunaInfo = vacunasPerro.find(v => v.valor === checkbox.value);
        if (vacunaInfo) {
            const div = document.createElement('div');
            div.className = 'mt-2';
            div.innerHTML = `
                <label class="label-select d-block">Fecha de última colocación de ${vacunaInfo.nombre} *</label>
                <input type="date" class="namee" name="fecha_${vacunaInfo.valor}${sufijo}" required>
            `;
            contenedor.appendChild(div);
        }
    });
}