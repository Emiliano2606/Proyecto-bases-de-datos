// ==============================================
// SISTEMA DE SERPIENTES - JS FINAL
// ==============================================

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
    const seleccionados = seccion.querySelectorAll('.vacuna-serpiente-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        const vacunaInfo = vacunasSerpiente.find(v => v.valor === Number(checkbox.value));
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

// FUNCIÓN DE PRE-PROCESAMIENTO (RECOLECCIÓN FORZADA)
function procesarSerpientesAntesDeEnviar() {
    const form = document.querySelector('form');
    if (!form) return;

    // Limpiar hiddens previos
    form.querySelectorAll('[data-serpiente-hidden]').forEach(e => e.remove());

    const secciones = document.querySelectorAll('#sectionSerpiente, [id^="sectionSerpiente_"]');

    secciones.forEach((seccion, index) => {
        const num = index + 1;
        const sufijo = (num === 1) ? "" : "_" + num;

        const nombreInput = seccion.querySelector('[name="nombre_mascota"], [name^="nombre_mascota_"]');

        if (nombreInput && nombreInput.value.trim() !== "") {
            // ID Forzado Principal
            crearHiddenSerp(form, `nombre_serp_forzado_${num}`, nombreInput.value.trim());

            // Campos según orden de tu tabla SQL
            const campos = [
                'fecha_nacimiento', 'sexo', 'especie_serpiente', 'tamano_serpiente',
                'clasificacion_serpiente', 'estatus_serpiente', 'fuente_calor_serpiente',
                'tipo_terrario_serpiente', 'alimentos_serpiente', 'marca_alimento_serpiente',
                'veces_comida_serpiente', 'tipo_tratamiento_serpiente', 'dimensiones_terrario_serpiente'
            ];

            campos.forEach(campo => {
                const input = seccion.querySelector(`[name="${campo}${sufijo}"], [name="${campo}"]`);
                if (input) {
                    crearHiddenSerp(form, `${campo}_serp_forzado_${num}`, input.value);
                }
            });

            // Vacunas Forzadas
            const vacunasChecked = seccion.querySelectorAll('.vacuna-serpiente-checkbox-input:checked');
            vacunasChecked.forEach(v => {
                crearHiddenSerp(form, `vacunas_serp_forzado_${num}[]`, v.value);
                const fechaInput = seccion.querySelector(`[name="fecha_${v.value}${sufijo}"]`);
                if (fechaInput && fechaInput.value) {
                    crearHiddenSerp(form, `fecha_v_serp_forzado_${v.value}_${num}`, fechaInput.value);
                }
            });
        }
    });
}

function crearHiddenSerp(form, name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.setAttribute('data-serpiente-hidden', '1');
    form.appendChild(input);
}

document.addEventListener('DOMContentLoaded', function() {
    const originalContainer = document.getElementById('lista-vacunas-checkbox5');
    if (originalContainer) cargarVacunasSerpiente(originalContainer);

    const form = document.querySelector('form');
    if (form) form.addEventListener('submit', procesarSerpientesAntesDeEnviar);
});