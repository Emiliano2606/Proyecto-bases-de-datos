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

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('vacuna-lagarto-checkbox-input')) {
        const checkbox = e.target;
        const seccionPadre = checkbox.closest('.section');
        const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas') || seccionPadre.querySelector('[id^="fechas-vacunas-container4"]');
        const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

        if (containerFechas) {
            actualizarFechasLagarto(seccionPadre, containerFechas, sufijo);
        }
    }
});

function actualizarFechasLagarto(seccion, contenedor, sufijo) {
    const seleccionados = seccion.querySelectorAll('.vacuna-lagarto-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        const vacunaInfo = vacunasLagarto.find(v => v.valor === Number(checkbox.value));
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


function procesarLagartosAntesDeEnviar() {
    const form = document.querySelector('form');
    if (!form) return;

    // Limpiar hiddens antiguos
    form.querySelectorAll('[data-lagarto-hidden]').forEach(e => e.remove());

    const secciones = document.querySelectorAll('#sectionLagarto, [id^="sectionLagarto_"]');

    secciones.forEach((seccion, index) => {
        const num = index + 1;
        const sufijo = (num === 1) ? "" : "_" + num;

        const nombreInput = seccion.querySelector('[name="nombre_mascota"], [name^="nombre_mascota_"]');

        if (nombreInput && nombreInput.value.trim() !== "") {
            // Nombre Forzado
            crearHiddenLagarto(form, `nombre_lagarto_forzado_${num}`, nombreInput.value.trim());

            // Mapeo de campos basado en tu tabla SQL NOT NULL
            const campos = [
                'fecha_nacimiento', 'sexo', 'especie_lagarto', 'tamano_lagarto',
                'clasificacion_lagarto', 'estatus_lagarto', 'requerimientos_ambientales_lagarto',
                'fuente_calor_lagarto', 'tipo_terrario_lagarto', 'dimensiones_terrario_lagarto',
                'dieta_lagarto', 'marca_alimento_lagarto', 'veces_comida_lagarto', 'tratamientos_lagarto'
            ];

            campos.forEach(campo => {
                const input = seccion.querySelector(`[name="${campo}${sufijo}"], [name="${campo}"]`);
                if (input) {
                    crearHiddenLagarto(form, `${campo}_lag_forzado_${num}`, input.value);
                }
            });

            // Vacunas Forzadas
            const vacunasChecked = seccion.querySelectorAll('.vacuna-lagarto-checkbox-input:checked');
            vacunasChecked.forEach(v => {
                crearHiddenLagarto(form, `vacunas_lag_forzado_${num}[]`, v.value);
                const fechaInput = seccion.querySelector(`[name="fecha_${v.value}${sufijo}"]`);
                if (fechaInput && fechaInput.value) {
                    crearHiddenLagarto(form, `fecha_vacuna_lag_forzado_${v.value}_${num}`, fechaInput.value);
                }
            });
        }
    });
}

function crearHiddenLagarto(form, name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.setAttribute('data-lagarto-hidden', '1');
    form.appendChild(input);
}

document.addEventListener('DOMContentLoaded', function() {
    const originalContainer = document.getElementById('lista-vacunas-checkbox4');
    if (originalContainer) cargarVacunasLagarto(originalContainer);

    const form = document.querySelector('form');
    if (form) form.addEventListener('submit', procesarLagartosAntesDeEnviar);
});