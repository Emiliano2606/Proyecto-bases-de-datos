// ==============================================
// SISTEMA DE TORTUGAS - JS FINAL
// ==============================================

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
    const seleccionados = seccion.querySelectorAll('.vacuna-tortuga-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        const vacunaInfo = vacunasTortuga.find(v => v.valor === Number(checkbox.value));
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

// FUNCIÓN DE PRE-PROCESAMIENTO PARA EL ENVÍO
function procesarTortugasAntesDeEnviar() {
    const form = document.querySelector('form');
    if (!form) return;

    // Limpiar hiddens de tortugas previos
    form.querySelectorAll('[data-tortuga-hidden]').forEach(e => e.remove());

    const secciones = document.querySelectorAll('#sectionTortuga, [id^="sectionTortuga_"]');

    secciones.forEach((seccion, index) => {
        const num = index + 1;
        const sufijo = (num === 1) ? "" : "_" + num;
        const nombreInput = seccion.querySelector('[name="nombre_mascota"], [name^="nombre_mascota_"]');

        if (nombreInput && nombreInput.value.trim() !== "") {
            // Crear campo de nombre forzado
            crearHiddenTort(form, `nombre_tort_forzado_${num}`, nombreInput.value.trim());

            // Campos en orden de aparición del formulario
            const campos = [
                'fecha_nacimiento', 'sexo', 'especie_tortuga', 'tamano_tortuga',
                'clasificacion_tortuga', 'estatus_tortuga', 'fuente_calor_tortuga',
                'alimentos_tortuga', 'tratamientos_tortuga', 'veces_comida_tortuga',
                'tipo_terrario_tortuga', 'dimensiones_terrario_tortuga'
            ];

            campos.forEach(campo => {
                const input = seccion.querySelector(`[name="${campo}${sufijo}"], [name="${campo}"]`);
                if (input) {
                    crearHiddenTort(form, `${campo}_tort_forzado_${num}`, input.value);
                }
            });

            // Procesar Vacunas Seleccionadas
            seccion.querySelectorAll('.vacuna-tortuga-checkbox-input:checked').forEach(v => {
                crearHiddenTort(form, `vacunas_tort_forzado_${num}[]`, v.value);
                const fechaInp = seccion.querySelector(`[name="fecha_${v.value}${sufijo}"]`);
                if (fechaInp) {
                    crearHiddenTort(form, `fecha_v_tort_forzado_${v.value}_${num}`, fechaInp.value);
                }
            });
        }
    });
}

function crearHiddenTort(form, name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.setAttribute('data-tortuga-hidden', '1');
    form.appendChild(input);
}

document.addEventListener('DOMContentLoaded', function() {
    const originalContainer = document.getElementById('lista-vacunas-checkbox6');
    if (originalContainer) cargarVacunasTortuga(originalContainer);

    const form = document.querySelector('form');
    if (form) form.addEventListener('submit', procesarTortugasAntesDeEnviar);
});