// ==============================================
// SISTEMA DE GATOS - UNIFICADO (ESTILO AVES)
// ==============================================

const vacunasGato = [
    { nombre: "Herpesvirus felino-1 (FHV)", valor: 16 },
    { nombre: "Calicivirus felino (FCV)", valor: 17 },
    { nombre: "Virus de la panleucopenia felina (FPV)", valor: 18 },
    { nombre: "Triple Felina (FPV + FHV + FCV)", valor: 19 },
    { nombre: "Dual Felina (FHV + FCV)", valor: 20 },
    { nombre: "Rabia", valor: 21 },
    { nombre: "Leucemia Felina (FelV)", valor: 22 },
    { nombre: "Virus de la inmunodeficiencia felina", valor: 23 },
    { nombre: "Chlamydia felis", valor: 24 },
    { nombre: "Bordetella bronchiseptica", valor: 25 },
    { nombre: "Virus de la peritonitis infecciosa felina (FIP)", valor: 26 },
    { nombre: "Giardia spp.", valor: 27 },
    { nombre: "Microsporum canis (Hongos)", valor: 28 },
    { nombre: "Enfermedad de Marek", valor: 29 }
];

function cargarVacunasGato(contenedor) {
    if (!contenedor) return;
    contenedor.innerHTML = '';
    const seccionPadre = contenedor.closest('.section');
    const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

    vacunasGato.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox" name="vacunas_gato${sufijo}[]" value="${vacuna.valor}" 
                   class="vacuna-gato-checkbox-input" id="vacuna_gato_${vacuna.valor}${sufijo}">
            <label for="vacuna_gato_${vacuna.valor}${sufijo}" style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        contenedor.appendChild(div);
    });
}

function actualizarFechasGato(seccion, contenedor, sufijo) {
    const seleccionados = seccion.querySelectorAll('.vacuna-gato-checkbox-input:checked');
    contenedor.innerHTML = '';
    seleccionados.forEach(checkbox => {
        const vacunaInfo = vacunasGato.find(v => v.valor === Number(checkbox.value));
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

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar vacunas existentes
    document.querySelectorAll('[id^="lista-vacunas-checkbox"]').forEach(cont => {
        if (cont.closest('#sectionGato') || cont.closest('[id^="sectionGato_"]')) {
            cargarVacunasGato(cont);
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('vacuna-gato-checkbox-input')) {
            const seccionPadre = e.target.closest('.section');
            const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas');
            const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';
            if (containerFechas) actualizarFechasGato(seccionPadre, containerFechas, sufijo);
        }
    });

    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            procesarGatosAntesDeEnviar();
        });
    }
});

function procesarGatosAntesDeEnviar() {
    const form = document.querySelector('form');
    if (!form) return;

    form.querySelectorAll('input[data-gato-hidden]').forEach(e => e.remove());
    const sectionsGatos = document.querySelectorAll('#sectionGato, [id^="sectionGato_"]');

    sectionsGatos.forEach((section, index) => {
        const numeroGato = index + 1;
        const sufijo = numeroGato === 1 ? '' : `_${numeroGato}`;

        // Nombre
        const nombreInput = section.querySelector('[name="nombre_gato[]"], [name^="nombre_gato_"]');
        if (nombreInput && nombreInput.value.trim()) {
            crearHiddenGato(form, `nombre_gato_forzado_${numeroGato}`, nombreInput.value.trim());
        }

        // Campos generales
        const camposGato = [
            'fecha_nacimiento_gato', 'sexo_gato', 'raza_del_gato', 'grupo_gato',
            'registro_principal_gato', 'tamano_gato', 'peso_gato', 'tipopelaje_gato',
            'caracterfisicas_gato', 'color_principal_gato', 'color_secundario_gato',
            'tipo_pelo_gato', 'patron_pelo_gato', 'convive_gato', 'alimento_gato',
            'marca_alimento_gato', 'veces_comida_gato', 'tratamientos_gatito',
            'tiene_ruac_gato', 'ruac_gato', 'chip_del_gato', 'numero_chip_gato', 'tipo_chip_gato'
        ];

        camposGato.forEach(campo => {
            const input = section.querySelector(`[name="${campo}${sufijo}"], [name="${campo}"]`);
            if (input && input.value !== '') {
                crearHiddenGato(form, `${campo}${sufijo}`, input.value);
            }
        });

        // Vacunas
        const vacunas = section.querySelectorAll('.vacuna-gato-checkbox-input:checked');
        vacunas.forEach(vacuna => {
            crearHiddenGato(form, `vacunas_gato${sufijo}[]`, vacuna.value);
            const fecha = section.querySelector(`[name="fecha_${vacuna.value}${sufijo}"]`);
            if (fecha && fecha.value) {
                crearHiddenGato(form, `fecha_${vacuna.value}${sufijo}`, fecha.value);
            }
        });
    });
}

function crearHiddenGato(form, name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.setAttribute('data-gato-hidden', '1');
    form.appendChild(input);
}