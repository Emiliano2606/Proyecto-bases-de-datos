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
            <input type="checkbox" 
                   name="vacunas_gato${sufijo}[]" 
                   value="${vacuna.valor}" 
                   class="vacuna-gato-checkbox-input"
                   id="vacuna_gato_${vacuna.valor}${sufijo}">
            <label for="vacuna_gato_${vacuna.valor}${sufijo}" style="margin-left: 8px;">
                ${vacuna.nombre}
            </label>
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
                <input type="date" class="namee" 
                       name="fecha_${vacunaInfo.valor}${sufijo}" 
                       required>
            `;
            contenedor.appendChild(div);
        }
    });
}

// Función específica para procesar vacunas de GATOS
function procesarVacunasGatoAntesDeEnviar() {
    console.log('😺 Procesando vacunas de GATOS...');

    const form = document.querySelector('form');
    if (!form) return false;

    // Eliminar inputs hidden anteriores de GATOS
    const hiddensAnterioresGatos = form.querySelectorAll('input[name^="vacunas_gato"]');
    hiddensAnterioresGatos.forEach(hidden => hidden.remove());

    // Procesar TODOS los checkboxes marcados de GATOS
    const checksMarcados = document.querySelectorAll('.vacuna-gato-checkbox-input:checked');

    checksMarcados.forEach(checkbox => {
        const seccion = checkbox.closest('.section');
        const sufijo = seccion.id.includes('_') ? '_' + seccion.id.split('_').pop() : '';

        // Agregar vacuna como hidden
        const hiddenVacuna = document.createElement('input');
        hiddenVacuna.type = 'hidden';
        hiddenVacuna.name = `vacunas_gato${sufijo}[]`;
        hiddenVacuna.value = checkbox.value;
        form.appendChild(hiddenVacuna);

        // Buscar y agregar la fecha correspondiente
        const idVacuna = checkbox.value;
        const nombreCampoFecha = `fecha_${idVacuna}${sufijo}`;
        const inputFecha = seccion.querySelector(`input[name="${nombreCampoFecha}"]`);

        if (inputFecha && inputFecha.value) {
            const hiddenFecha = document.createElement('input');
            hiddenFecha.type = 'hidden';
            hiddenFecha.name = nombreCampoFecha;
            hiddenFecha.value = inputFecha.value;
            form.appendChild(hiddenFecha);
        }
    });

    return true;
}

// Función modificada para forzar envío
function forzarEnvioCamposGatos() {
    const form = document.querySelector('form');
    if (!form) return;

    const forzadosAnteriores = form.querySelectorAll('[name^="nombre_gato_forzado_"]');
    forzadosAnteriores.forEach(el => el.remove());

    const todosCampos = document.querySelectorAll('[name*="nombre_gato"]');

    todosCampos.forEach((campo, index) => {
        const valor = campo.value.trim();

        if (valor !== '') {
            const hiddenForzado = document.createElement('input');
            hiddenForzado.type = 'hidden';
            hiddenForzado.name = 'nombre_gato_forzado_' + (index + 1);
            hiddenForzado.value = valor;
            form.appendChild(hiddenForzado);
        }
    });
}

// Inicialización específica para GATOS
document.addEventListener('DOMContentLoaded', function() {
    console.log('🐈 Sistema de vacunas para GATOS cargado');

    // Cargar vacunas en el contenedor de GATOS
    const contGato = document.getElementById('lista-vacunas-checkbox2');
    if (contGato) {
        cargarVacunasGato(contGato);

        setTimeout(() => {
            const checksIniciales = contGato.querySelectorAll('.vacuna-gato-checkbox-input:checked');
            if (checksIniciales.length > 0) {
                const seccionPadre = contGato.closest('.section');
                const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas');
                const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

                if (containerFechas) {
                    actualizarFechasGato(seccionPadre, containerFechas, sufijo);
                }
            }
        }, 500);
    }

    // Configurar listener para cambios en checkboxes de GATOS
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('vacuna-gato-checkbox-input')) {
            const checkbox = e.target;
            const seccionPadre = checkbox.closest('.section');
            const containerFechas = seccionPadre.querySelector('.fechas-vacunas-dinamicas');
            const sufijo = seccionPadre.id.includes('_') ? '_' + seccionPadre.id.split('_').pop() : '';

            if (containerFechas) {
                actualizarFechasGato(seccionPadre, containerFechas, sufijo);
            }
        }
    });
});