const vacunasLagarto = [
    { nombre: "Chequeo Parasitario (Coprológico)", valor: 40 },
    { nombre: "Desparasitación Interna", valor: 41 },
    { nombre: "Suplemento de Calcio y D3", valor: 42 },
    { nombre: "Prueba de Herpesvirus", valor: 43 },
    { nombre: "Chequeo Anual General", valor: 44 },
    { nombre: "Gota/Enfermedad Renal", valor: 45 },
    { nombre: "Vitaminas del Complejo B", valor: 46 },
    { nombre: "Examen de Piel", valor: 47 },
    { nombre: "Manejo de Estomatitis", valor: 48 },
    { nombre: "Control de Ácaros", valor: 49 },
    { nombre: "Examen de Hemoparásitos", valor: 50 },
    { nombre: "Antibiótico de amplio espectro", valor: 51 }
];

function cargarVacunasLagarto(contenedor) {
    if (!contenedor) return;
    contenedor.innerHTML = '';

    const seccionPadre = contenedor.closest('.section');
    // Esto extrae el _2, _3 etc.
    const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

    vacunasLagarto.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox" name="vacunas_lagarto${sufijo}[]" value="${vacuna.valor}" class="vacuna-lagarto-checkbox-input">
            <label style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        contenedor.appendChild(div);
    });
}

// Delegación de eventos para Lagartos
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('vacuna-lagarto-checkbox-input')) {
        const checkbox = e.target;
        const seccionPadre = checkbox.closest('.section');
        // Buscamos el contenedor por clase o ID que empiece por...
        const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas') || seccionPadre.querySelector('[id^="fechas-vacunas-container4"]');
        const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

        if (containerFechas) {
            actualizarFechasLagarto(seccionPadre, containerFechas, sufijo);
        }
    }
});

function actualizarFechasLagarto(seccion, contenedor, sufijo) {
    // 1. Aseguramos que el selector use la clase específica de lagarto
    const seleccionados = seccion.querySelectorAll('.vacuna-lagarto-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        // 2. CORRECCIÓN CLAVE: Convertimos a Number para que coincida con el catálogo (40, 41...)
        const vacunaInfo = vacunasLagarto.find(v => v.valor === Number(checkbox.value));

        if (vacunaInfo) {
            const div = document.createElement('div');
            div.className = 'mt-2';
            div.innerHTML = `
                <label class="label-select d-block">Fecha de ${vacunaInfo.nombre} *</label>
                <input type="date" class="namee" name="fecha_${vacunaInfo.valor}${sufijo}" required>
            `;
            // He simplificado el name a "fecha_ID_SUFIJO" para que tu PHP sea universal
            contenedor.appendChild(div);
        }
    });
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    const originalContainer = document.getElementById('lista-vacunas-checkbox4');
    if (originalContainer) {
        cargarVacunasLagarto(originalContainer);
    }
});