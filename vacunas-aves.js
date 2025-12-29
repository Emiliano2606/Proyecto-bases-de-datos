// ==============================================
// SISTEMA DE AVES - COMPLETO Y FUNCIONAL (FIX)
// ==============================================

// Array de vacunas de aves
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

// =============================
// 1. CARGAR VACUNAS
// =============================
function cargarVacunasAve(contenedor) {
    if (!contenedor) return;

    contenedor.innerHTML = '';

    const seccionPadre = contenedor.closest('.section');
    const sufijo = seccionPadre.id.includes('_') ?
        '_' + seccionPadre.id.split('_').pop() :
        '';

    vacunasAve.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox"
                   name="vacunas_ave${sufijo}[]"
                   value="${vacuna.valor}"
                   class="vacuna-ave-checkbox-input"
                   id="vacuna_ave_${vacuna.valor}${sufijo}">
            <label for="vacuna_ave_${vacuna.valor}${sufijo}" style="margin-left: 8px;">
                ${vacuna.nombre}
            </label>
        `;
        contenedor.appendChild(div);
    });
}

// =============================
// 2. ACTUALIZAR FECHAS DE VACUNAS
// =============================
function actualizarFechasAve(seccion, contenedor, sufijo) {
    const seleccionados = seccion.querySelectorAll('.vacuna-ave-checkbox-input:checked');
    contenedor.innerHTML = '';

    seleccionados.forEach(checkbox => {
        const vacunaInfo = vacunasAve.find(v => v.valor === Number(checkbox.value));

        if (vacunaInfo) {
            const div = document.createElement('div');
            div.className = 'mt-2';
            div.innerHTML = `
                <label class="label-select d-block">Fecha de ${vacunaInfo.nombre} *</label>
                <input type="date" class="namee"
                       name="fecha_${vacunaInfo.valor}${sufijo}"
                       required>
            `;
            contenedor.appendChild(div);
        }
    });
}

// =============================
// 3. DOM READY
// =============================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🦜 Sistema de aves cargado');

    const contAve = document.getElementById('lista-vacunas-checkbox3');
    if (contAve) {
        cargarVacunasAve(contAve);
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('vacuna-ave-checkbox-input')) {
            const seccionPadre = e.target.closest('.section');
            const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas');
            const sufijo = seccionPadre.id.includes('_') ?
                '_' + seccionPadre.id.split('_').pop() :
                '';

            if (containerFechas) {
                actualizarFechasAve(seccionPadre, containerFechas, sufijo);
            }
        }
    });

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            console.log('📤 Procesando aves antes de enviar...');
            procesarAvesAntesDeEnviar();
        });
    }
});

// =============================
// 4. PROCESAR AVES ANTES DE ENVIAR (FIX FINAL)
// =============================
function procesarAvesAntesDeEnviar() {
    const form = document.querySelector('form');
    if (!form) return true;

    // Limpiar hiddens previos
    form.querySelectorAll('input[data-ave-hidden]').forEach(e => e.remove());

    // TODAS las secciones de aves
    const sectionsAves = document.querySelectorAll('#sectionAve, [id^="sectionAve_"]');

    sectionsAves.forEach((section, index) => {
        const numeroAve = index + 1;
        const sufijo = numeroAve === 1 ? '' : `_${numeroAve}`;

        // =============================
        // NOMBRE DEL AVE
        // =============================
        const nombreInput = section.querySelector(`[name="nombre_mascota${sufijo}"], [name="nombre_mascota"]`);
        if (nombreInput && nombreInput.value.trim()) {
            crearHidden(form, `nombre_ave_forzado_${numeroAve}`, nombreInput.value.trim());
        }

        // =============================
        // CAMPOS GENERALES DEL AVE
        // =============================
        const camposAve = [
            'fecha_nacimiento_ave',
            'sexo_ave',
            'especie_ave',
            'grupo_taxonomico',
            'estatus_conservacion',
            'clasificacion_autoridades',
            'tamano_ave',
            'convive_ave',
            'alimento_ave',
            'marca_alimento_ave',
            'veces_comida_ave',
            'tratamientos_ave',
            'tipo_jaula_ave',
            'dimensiones_jaula_ave',
            'tipo_plumas_ave',
            'color_principal_ave',
            'color_secundario_ave',
            'chip_del_ave',
            'numero_chip_ave',
            'tipo_chip_ave'
        ];

        camposAve.forEach(campo => {
            const input = section.querySelector(`[name="${campo}${sufijo}"], [name="${campo}"]`);
            if (input && input.value !== '') {
                crearHidden(form, `${campo}${sufijo}`, input.value);
            }
        });

        // =============================
        // VACUNAS
        // =============================
        const vacunas = section.querySelectorAll('.vacuna-ave-checkbox-input:checked');

        vacunas.forEach(vacuna => {
            crearHidden(form, `vacunas_ave${sufijo}[]`, vacuna.value);

            const fecha = section.querySelector(`[name="fecha_${vacuna.value}${sufijo}"]`);
            if (fecha && fecha.value) {
                crearHidden(form, `fecha_${vacuna.value}${sufijo}`, fecha.value);
            }
        });
    });

    console.log('✅ Aves serializadas correctamente');
    return true;
}

// =============================
// UTILIDAD
// =============================
function crearHidden(form, name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.setAttribute('data-ave-hidden', '1');
    form.appendChild(input);
}