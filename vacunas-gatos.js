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

// ==================== FUNCIONES PRINCIPALES ====================

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

// ==================== PROCESAMIENTO DE VACUNAS AL ENVIAR ====================

function procesarVacunasAntesDeEnviar() {
    const form = document.querySelector('form');
    if (!form) return;

    // 1. Eliminar inputs hidden anteriores
    const hiddensAnteriores = form.querySelectorAll('input[name^="vacunas_gato"], input[name^="fecha_"]');
    hiddensAnteriores.forEach(hidden => hidden.remove());

    // 2. Agregar vacunas seleccionadas como hidden
    const checksMarcados = document.querySelectorAll('.vacuna-gato-checkbox-input:checked');

    checksMarcados.forEach(checkbox => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'vacunas_gato[]';
        hiddenInput.value = checkbox.value;
        form.appendChild(hiddenInput);
    });

    // 3. Agregar fechas como hidden
    checksMarcados.forEach(checkbox => {
        const idVacuna = checkbox.value;
        const seccion = checkbox.closest('.section');
        const sufijo = seccion.id.includes('_') ? '_' + seccion.id.split('_').pop() : '';

        const nombreCampoFecha = `fecha_${idVacuna}${sufijo}`;
        const inputFecha = document.querySelector(`input[name="${nombreCampoFecha}"]`);

        if (inputFecha && inputFecha.value) {
            const hiddenFecha = document.createElement('input');
            hiddenFecha.type = 'hidden';
            hiddenFecha.name = nombreCampoFecha;
            hiddenFecha.value = inputFecha.value;
            form.appendChild(hiddenFecha);
        }
    });
}

// ==================== INICIALIZACIÓN ====================

document.addEventListener('DOMContentLoaded', function() {
    // Cargar vacunas en el contenedor
    const contGato = document.getElementById('lista-vacunas-checkbox2');
    if (contGato) {
        cargarVacunasGato(contGato);

        // Si ya hay checkboxes marcados (al recargar), generar fechas
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

    // Configurar listener para cambios en checkboxes
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

    // Interceptar envío del formulario
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            procesarVacunasAntesDeEnviar();
        });
    }
});

// ==================== LISTENER PARA BOTONES DE ENVÍO ====================

document.addEventListener('click', function(e) {
    const target = e.target;
    const isSubmitButton =
        target.type === 'submit' ||
        (target.tagName === 'BUTTON' && (
            target.innerText.toLowerCase().includes('enviar') ||
            target.innerText.toLowerCase().includes('guardar') ||
            target.innerText.toLowerCase().includes('registrar')
        ));

    if (isSubmitButton) {
        setTimeout(procesarVacunasAntesDeEnviar, 50);
    }
});

// Mensaje simple de confirmación
console.log('✅ Sistema de vacunas para gatos cargado correctamente');
// Debug en tiempo real - agregar en registro_mascota.html
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Solo para debug

            console.log('=== DEBUG: CAMPOS DE GATO ===');

            // Buscar TODOS los campos nombre_gato
            const camposNombreGato = document.querySelectorAll('input[name="nombre_gato[]"]');
            console.log(`Total campos nombre_gato[] encontrados: ${camposNombreGato.length}`);

            // Mostrar valores de cada uno
            camposNombreGato.forEach((campo, index) => {
                console.log(`Gato ${index + 1}: name="${campo.name}", value="${campo.value}"`);
            });

            // Verificar si el segundo tiene valor
            if (camposNombreGato.length > 1 && !camposNombreGato[1].value.trim()) {
                console.warn('⚠️ El segundo gato está VACÍO o tiene solo espacios');
                alert('¡ATENCIÓN! El segundo gato está vacío. Por favor, llena el nombre.');
            }

            // Si todo está bien, enviar el formulario
            if (confirm('¿Enviar formulario?')) {
                form.submit();
            }
        });
    }
});