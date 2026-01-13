const vacunasPerro = [
    { nombre: "Rabia", valor: 1 },
    { nombre: "Moquillo (Distemper)", valor: 2 },
    { nombre: "Parvovirus", valor: 3 },
    { nombre: "Hepatitis canina (Adenovirus-2)", valor: 4 },
    { nombre: "Leptospirosis", valor: 5 },
    { nombre: "Traqueobronquitis (Bordetella)", valor: 6 },
    { nombre: "Influenza canina H3N2", valor: 7 },
    { nombre: "Influenza canina H3N8", valor: 8 },
    { nombre: "Coronavirus canino", valor: 9 },
    { nombre: "Giardia", valor: 10 },
    { nombre: "Leishmaniosis", valor: 11 },
    { nombre: "Polivalente (Básica)", valor: 12 },
    { nombre: "Polivalente Completa", valor: 13 },
    { nombre: "Triple (Respiratoria)", valor: 14 },
    { nombre: "Vacuna contra hongos (Microsporum)", valor: 15 }
];

function cargarVacunasEnContenedor(contenedor) {
    if (!contenedor) return;
    contenedor.innerHTML = '';

    const seccionPadre = contenedor.closest('.section');
    const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

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

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('vacuna-checkbox-input')) {
        const checkbox = e.target;
        const seccionPadre = checkbox.closest('.section');

        const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas');

        const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

        if (containerFechas) {
            actualizarFechasDeEstaMascota(seccionPadre, containerFechas, sufijo);
        }
    }
});

function actualizarFechasDeEstaMascota(seccion, contenedor, sufijo) {
    const seleccionados = seccion.querySelectorAll('.vacuna-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        const vacunaInfo = vacunasPerro.find(v => v.valor === Number(checkbox.value));

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