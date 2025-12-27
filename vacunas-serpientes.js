const vacunasSerpiente = [
    { nombre: "Chequeo Parasitario (Coprológico)", valor: 52 },
    { nombre: "Desparasitación Interna", valor: 53 },
    { nombre: "Tratamiento por Ácaros", valor: 54 },
    { nombre: "Examen de Estomatitis Infecciosa", valor: 55 },
    { nombre: "Tratamiento por Neumonía", valor: 56 },
    { nombre: "Suplemento Vitamínico A", valor: 57 },
    { nombre: "Chequeo de Ecdisis (Muda)", valor: 58 },
    { nombre: "Prueba de Inclusión Corporal (IBD)", valor: 59 },
    { nombre: "Revisión Anual General", valor: 60 },
    { nombre: "Antibiótico de amplio espectro", valor: 61 }
];

function cargarVacunasSerpiente(contenedor) {
    if (!contenedor) return;
    contenedor.innerHTML = '';

    const seccionPadre = contenedor.closest('.section');
    const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

    vacunasSerpiente.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox" name="vacunas_serpiente${sufijo}[]" value="${vacuna.valor}" class="vacuna-serpiente-checkbox-input">
            <label style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        contenedor.appendChild(div);
    });
}

// Delegación de eventos para Serpientes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('vacuna-serpiente-checkbox-input')) {
        const checkbox = e.target;
        const seccionPadre = checkbox.closest('.section');
        const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas') || seccionPadre.querySelector('[id^="fechas-vacunas-container5"]');
        const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

        if (containerFechas) {
            actualizarFechasSerpiente(seccionPadre, containerFechas, sufijo);
        }
    }
});

function actualizarFechasSerpiente(seccion, contenedor, sufijo) {
    // 1. Selector corregido para la clase específica de serpiente
    const seleccionados = seccion.querySelectorAll('.vacuna-serpiente-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        // 2. CORRECCIÓN: Usar Number() para comparar con los valores 52-61
        const vacunaInfo = vacunasSerpiente.find(v => v.valor === Number(checkbox.value));

        if (vacunaInfo) {
            const div = document.createElement('div');
            div.className = 'mt-2';
            div.innerHTML = `
                <label class="label-select d-block">Fecha de ${vacunaInfo.nombre} *</label>
                <input type="date" class="namee" name="fecha_${vacunaInfo.valor}${sufijo}" required>
            `;
            // Dejamos name="fecha_52_1" (ejemplo), quitando el texto "serpiente"
            contenedor.appendChild(div);
        }
    });
}

// Carga inicial para la primera sección
document.addEventListener('DOMContentLoaded', function() {
    const originalContainer = document.getElementById('lista-vacunas-checkbox5');
    if (originalContainer) {
        cargarVacunasSerpiente(originalContainer);
    }
});