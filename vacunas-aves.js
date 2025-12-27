const vacunasAve = [
    { nombre: "Newcastle (NDV)", valor: 30 },
    { nombre: "Viruela Aviar", valor: 31 },
    { nombre: "Herpesvirus del Pato (DVE)", valor: 32 },
    { nombre: "Gumboro", valor: 33 },
    { nombre: "Bronquitis Infecciosa Aviar (IBV)", valor: 34 },
    { nombre: "Cólera Aviar", valor: 35 },
    { nombre: "Micoplasmosis", valor: 36 },
    { nombre: "Paramixovirus-1", valor: 37 },
    { nombre: "Psitacosis", valor: 38 },
    { nombre: "Pacheco's Disease", valor: 39 }
];

function cargarVacunasAve(contenedor) {
    if (!contenedor) return;
    contenedor.innerHTML = '';

    const seccionPadre = contenedor.closest('.section');
    const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

    vacunasAve.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox" name="vacunas_ave${sufijo}[]" value="${vacuna.valor}" class="vacuna-ave-checkbox-input">
            <label style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        contenedor.appendChild(div);
    });
}

// Delegación de eventos para Aves
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('vacuna-ave-checkbox-input')) {
        const checkbox = e.target;
        const seccionPadre = checkbox.closest('.section');
        const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas') || seccionPadre.querySelector('[id^="fechas-vacunas-container3"]');
        const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

        if (containerFechas) {
            actualizarFechasAve(seccionPadre, containerFechas, sufijo);
        }
    }
});

function actualizarFechasAve(seccion, contenedor, sufijo) {
    // CORRECCIÓN AQUÍ: Agregado "-ave" para que coincida con el input
    const seleccionados = seccion.querySelectorAll('.vacuna-ave-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        const vacunaInfo = vacunasAve.find(v => v.valor === Number(checkbox.value));

        if (vacunaInfo) {
            const div = document.createElement('div');
            div.className = 'mt-2';
            div.innerHTML = `
                <label class="label-select d-block">Fecha de ${vacunaInfo.nombre} *</label>
                <input type="date" class="namee" name="fecha_${vacunaInfo.valor}${sufijo}" required>
            `;
            contenedor.appendChild(div);
        }
    });
}

// Inicialización para el bloque original
document.addEventListener('DOMContentLoaded', function() {
    const originalContainer = document.getElementById('lista-vacunas-checkbox3');
    if (originalContainer) {
        cargarVacunasAve(originalContainer);
    }
});