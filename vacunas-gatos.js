// =======================
// VACUNAS GATO
// =======================
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

// =======================
// OBTENER SUFIJO CORRECTO
// =======================
function obtenerSufijoGato(seccion) {
    const partes = seccion.id.split('_');
    return partes.length > 1 ? '_' + partes.pop() : '';
}

// =======================
// CARGAR VACUNAS
// =======================
function cargarVacunasGato(contenedor) {
    if (!contenedor) return;

    contenedor.innerHTML = '';
    const seccion = contenedor.closest('.section');
    const sufijo = obtenerSufijoGato(seccion);

    vacunasGato.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox"
                class="vacuna-gato-checkbox-input"
                name="vacunas_gato${sufijo}[]"
                id="vacuna_gato_${vacuna.valor}${sufijo}"
                value="${vacuna.valor}">
            <label for="vacuna_gato_${vacuna.valor}${sufijo}">
                ${vacuna.nombre}
            </label>
        `;
        contenedor.appendChild(div);
    });
}

// =======================
// FECHAS DINÁMICAS
// =======================
function actualizarFechasGato(seccion, contenedor) {
    contenedor.innerHTML = '';
    const sufijo = obtenerSufijoGato(seccion);

    seccion.querySelectorAll('.vacuna-gato-checkbox-input:checked')
        .forEach(check => {
            const vacuna = vacunasGato.find(v => v.valor === Number(check.value));
            if (!vacuna) return;

            const div = document.createElement('div');
            div.innerHTML = `
                <label>Fecha de ${vacuna.nombre}</label>
                <input type="date" name="fecha_${vacuna.valor}${sufijo}">
            `;
            contenedor.appendChild(div);
        });
}

// =======================
// PROCESAR VACUNAS ANTES DE ENVIAR
// =======================
function procesarVacunasGatoAntesDeEnviar() {
    const form = document.querySelector('form');
    if (!form) return true;

    // Eliminar hidden previos
    form.querySelectorAll('[data-gato-hidden]').forEach(e => e.remove());

    document.querySelectorAll('div[name="seccion_gato"]').forEach(seccion => {

        const sufijo = obtenerSufijoGato(seccion);

        seccion.querySelectorAll('.vacuna-gato-checkbox-input:checked')
            .forEach(check => {

                // Hidden vacuna
                const hVacuna = document.createElement('input');
                hVacuna.type = 'hidden';
                hVacuna.name = `vacunas_gato${sufijo}[]`;
                hVacuna.value = check.value;
                hVacuna.dataset.gatoHidden = '1';
                form.appendChild(hVacuna);

                // Hidden fecha
                const fecha = seccion.querySelector(
                    `input[name="fecha_${check.value}${sufijo}"]`
                );

                if (fecha && fecha.value) {
                    const hFecha = document.createElement('input');
                    hFecha.type = 'hidden';
                    hFecha.name = fecha.name;
                    hFecha.value = fecha.value;
                    hFecha.dataset.gatoHidden = '1';
                    form.appendChild(hFecha);
                }
            });
    });

    return true;
}

// =======================
// INIT
// =======================
document.addEventListener('DOMContentLoaded', () => {

    // Cargar vacunas en todos los contenedores
    document.querySelectorAll('[id^="lista-vacunas-checkbox"]')
        .forEach(c => cargarVacunasGato(c));

    // Actualizar fechas al marcar / desmarcar
    document.addEventListener('change', e => {
        if (e.target.classList.contains('vacuna-gato-checkbox-input')) {
            const seccion = e.target.closest('.section');
            const fechas = seccion.querySelector('.fechas-vacunas-dinamicas');
            if (fechas) {
                actualizarFechasGato(seccion, fechas);
            }
        }
    });

});

// =======================
// FORZAR NOMBRES DE GATOS ANTES DE ENVIAR
// =======================
function forzarNombresGatosAntesDeEnviar() {
    console.log('😺 Forzando nombres de gatos...');

    const form = document.querySelector('form');
    if (!form) return true;

    // Eliminar hidden previos de nombres forzados
    form.querySelectorAll('input[name^="nombre_gato_forzado_"]').forEach(e => e.remove());

    // Buscar TODAS las secciones de gatos
    const seccionesGatos = document.querySelectorAll('div[name="seccion_gato"]');

    seccionesGatos.forEach((seccion, index) => {
        const numeroGato = index + 1; // 1, 2, 3...

        console.log(`🔍 Procesando gato ${numeroGato}...`);

        // Buscar el campo de nombre en esta sección
        // IMPORTANTE: El primer gato usa nombre_gato[], el segundo usa nombre_gato[] pero es el mismo name
        // Necesitamos una forma diferente de identificarlos

        // Método 1: Buscar por ID de sección
        let nombreInput;
        if (numeroGato === 1) {
            // Primer gato: #sectionGato
            nombreInput = document.querySelector('#sectionGato [name="nombre_gato[]"]');
        } else {
            // Segundo gato: #sectionGato_2
            nombreInput = document.querySelector(`#sectionGato_${numeroGato} [name="nombre_gato[]"]`);
        }

        if (nombreInput && nombreInput.value.trim()) {
            // Crear campo forzado para este gato
            const hiddenNombre = document.createElement('input');
            hiddenNombre.type = 'hidden';
            hiddenNombre.name = 'nombre_gato_forzado_' + numeroGato;
            hiddenNombre.value = nombreInput.value.trim();
            form.appendChild(hiddenNombre);

            console.log(`✅ Gato ${numeroGato} forzado: "${hiddenNombre.value}"`);

            // También forzar otros campos importantes
            // Fecha de nacimiento
            let fechaInput;
            if (numeroGato === 1) {
                fechaInput = document.querySelector('#sectionGato [name="fecha_nacimiento_gato"]');
            } else {
                fechaInput = document.querySelector(`#sectionGato_${numeroGato} [name="fecha_nacimiento_gato_${numeroGato}"]`);
            }

            if (fechaInput && fechaInput.value) {
                const hiddenFecha = document.createElement('input');
                hiddenFecha.type = 'hidden';
                hiddenFecha.name = `fecha_nacimiento_gato_${numeroGato}_forzado`;
                hiddenFecha.value = fechaInput.value;
                form.appendChild(hiddenFecha);
                console.log(`   📅 Fecha forzada: ${fechaInput.value}`);
            }

            // Sexo
            let sexoInput;
            if (numeroGato === 1) {
                sexoInput = document.querySelector('#sectionGato [name="sexo_gato"]');
            } else {
                sexoInput = document.querySelector(`#sectionGato_${numeroGato} [name="sexo_gato_${numeroGato}"]`);
            }

            if (sexoInput && sexoInput.value) {
                const hiddenSexo = document.createElement('input');
                hiddenSexo.type = 'hidden';
                hiddenSexo.name = `sexo_gato_${numeroGato}_forzado`;
                hiddenSexo.value = sexoInput.value;
                form.appendChild(hiddenSexo);
                console.log(`   ⚥ Sexo forzado: ${sexoInput.value}`);
            }
        } else {
            console.log(`⚠️ Gato ${numeroGato}: Nombre no encontrado o vacío`);
        }
    });

    return true;
}

// =======================
// INTERCEPTAR ENVÍO DEL FORMULARIO - VERSIÓN CORREGIDA
// =======================
function interceptarEnvioGatos() {
    const form = document.querySelector('form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        console.log('📤 Interceptando envío para gatos...');

        // 1. Forzar nombres de gatos
        forzarNombresGatosAntesDeEnviar();

        // 2. Procesar vacunas de gatos
        procesarVacunasGatoAntesDeEnviar();

        console.log('✅ Gatos procesados correctamente');
        return true;
    });
}

// =======================
// ACTUALIZAR INIT
// =======================
document.addEventListener('DOMContentLoaded', () => {
    console.log('🐈 Sistema de gatos cargado');

    // 1. Cargar vacunas en todos los contenedores
    document.querySelectorAll('[id^="lista-vacunas-checkbox"]')
        .forEach(c => cargarVacunasGato(c));

    // 2. Actualizar fechas al marcar / desmarcar
    document.addEventListener('change', e => {
        if (e.target.classList.contains('vacuna-gato-checkbox-input')) {
            const seccion = e.target.closest('.section');
            const fechas = seccion.querySelector('.fechas-vacunas-dinamicas');
            if (fechas) {
                actualizarFechasGato(seccion, fechas);
            }
        }
    });

    // 3. Interceptar envío del formulario
    interceptarEnvioGatos();

    console.log(`🔍 Secciones de gatos encontradas: ${document.querySelectorAll('div[name="seccion_gato"]').length}`);
});