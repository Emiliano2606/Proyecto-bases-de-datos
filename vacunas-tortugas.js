const vacunasTortuga = [
    { nombre: "Chequeo Parasitario (Coprológico)", valor: 62 },
    { nombre: "Desparasitación Interna", valor: 63 },
    { nombre: "Examen de Caparazón y Plastrón", valor: 64 },
    { nombre: "Prueba de Herpesvirus (Quelonios)", valor: 65 },
    { nombre: "Suplemento de Calcio y D3", valor: 66 },
    { nombre: "Revisión Anual Veterinaria", valor: 67 },
    { nombre: "Tratamiento por Rinovirus/Neumonía", valor: 68 },
    { nombre: "Tratamiento por Deficiencia de Vitamina A", valor: 69 },
    { nombre: "Manejo de Gota/Enfermedad Renal", valor: 70 },
    { nombre: "Análisis de Hemoparásitos", valor: 71 }
];

function cargarVacunasTortuga(contenedor) {
    if (!contenedor) return;
    contenedor.innerHTML = '';

    const seccionPadre = contenedor.closest('.section');
    const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

    vacunasTortuga.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox" name="vacunas_tortuga${sufijo}[]" value="${vacuna.valor}" class="vacuna-tortuga-checkbox-input">
            <label style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        contenedor.appendChild(div);
    });
}

// Delegación de eventos para Tortugas
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('vacuna-tortuga-checkbox-input')) {
        const checkbox = e.target;
        const seccionPadre = checkbox.closest('.section');
        const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas') || seccionPadre.querySelector('[id^="fechas-vacunas-container6"]');
        const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

        if (containerFechas) {
            actualizarFechasTortuga(seccionPadre, containerFechas, sufijo);
        }
    }
});

function actualizarFechasTortuga(seccion, contenedor, sufijo) {
    // 1. Selector corregido para la clase específica de tortuga
    const seleccionados = seccion.querySelectorAll('.vacuna-tortuga-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        // 2. CORRECCIÓN: Convertir checkbox.value a Number para comparar con los valores 62-71
        const vacunaInfo = vacunasTortuga.find(v => v.valor === Number(checkbox.value));

        if (vacunaInfo) {
            const div = document.createElement('div');
            div.className = 'mt-2';
            div.innerHTML = `
                <label class="label-select d-block">Fecha de ${vacunaInfo.nombre} *</label>
                <input type="date" class="namee" name="fecha_${vacunaInfo.valor}${sufijo}" required>
            `;
            // name queda estandarizado como fecha_ID_SUFIJO (ej. fecha_62_1)
            contenedor.appendChild(div);
        }
    });
}

// Carga inicial
document.addEventListener('DOMContentLoaded', function() {
    const originalContainer = document.getElementById('lista-vacunas-checkbox6');
    if (originalContainer) {
        cargarVacunasTortuga(originalContainer);
    }
});