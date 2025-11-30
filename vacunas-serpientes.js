// Lista de chequeos preventivos y tratamientos comunes para Serpientes.
// En serpientes, el enfoque es en chequeos parasitarios, salud respiratoria y control ambiental.
const vacunasSerpiente = [
    { nombre: "Chequeo Parasitario (Coprológico)", valor: "chequeo_parasitario_serpiente" },
    { nombre: "Desparasitación Interna (última dosis)", valor: "desparasitacion_int_serpiente" },
    { nombre: "Tratamiento por Ácaros (última aplicación)", valor: "tratamiento_acaros_serpiente" },
    { nombre: "Examen de Estomatitis Infecciosa (Inicio de Terapia)", valor: "estomatitis_serpiente" },
    { nombre: "Tratamiento por Neumonía (Enfermedad Respiratoria)", valor: "neumonia_tratamiento" },
    { nombre: "Suplemento Vitamínico A (Fecha de inicio)", valor: "suplemento_vitamina_a" },
    { nombre: "Chequeo de Ecdisis (Muda) completada sin problemas", valor: "chequeo_ecdisis" },
    { nombre: "Prueba de Inclusión Corporal (IBD)", valor: "prueba_ibd" },
    { nombre: "Revisión Anual General (Fecha)", valor: "chequeo_anual_serpiente" },
    { nombre: "Antibiótico de amplio espectro (Fecha inicio)", valor: "antibiotico_espectro_serpiente" }
];

// Función para cargar los chequeos/tratamientos como checkboxes en el contenedor 5 (Serpientes)
function cargarVacunasSerpiente() {
    // ID único para serpientes
    const containerCheckboxes = document.getElementById('lista-vacunas-checkbox5');

    if (!containerCheckboxes) {
        // console.log('No se encontró el elemento lista-vacunas-checkbox5');
        return;
    }

    // Limpiar contenedor
    containerCheckboxes.innerHTML = '';

    // Agregar cada chequeo/tratamiento como checkbox
    vacunasSerpiente.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        // Usamos vacunas_serpiente[] como name
        div.innerHTML = `
            <input type="checkbox" id="vacuna_serpiente_${vacuna.valor}" name="vacunas_serpiente[]" value="${vacuna.valor}">
            <label for="vacuna_serpiente_${vacuna.valor}" style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        containerCheckboxes.appendChild(div);

        // Agregar evento a cada checkbox
        const checkbox = div.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', mostrarCamposFechaVacunasSerpiente);
    });
}

// Función para mostrar campos de fecha según chequeos/tratamientos seleccionados
function mostrarCamposFechaVacunasSerpiente() {
    // ID único para serpientes
    const containerFechas = document.getElementById('fechas-vacunas-container5');
    const checkboxes = document.querySelectorAll('input[name="vacunas_serpiente[]"]:checked');

    if (!containerFechas) return;

    const vacunasSeleccionadas = Array.from(checkboxes).map(checkbox => checkbox.value);

    // Limpiar el contenedor
    containerFechas.innerHTML = '';

    // Crear campo de fecha para cada chequeo seleccionado
    vacunasSeleccionadas.forEach(vacunaValor => {
        const vacunaInfo = vacunasSerpiente.find(v => v.valor === vacunaValor);

        if (vacunaInfo) {
            const label = document.createElement('label');
            label.className = 'label-select';
            // Adaptar texto de la etiqueta para que sea más genérico
            label.textContent = `Fecha de ${vacunaInfo.nombre} *`;

            label.htmlFor = `fecha_serpiente_${vacunaValor}`;

            const input = document.createElement('input');
            input.className = 'namee';
            input.type = 'date';

            input.id = `fecha_serpiente_${vacunaValor}`;
            input.name = `fecha_serpiente_${vacunaValor}`; // Nombre único para el envío
            input.required = true;

            containerFechas.appendChild(label);
            containerFechas.appendChild(input);
        }
    });
}

// Ejecutar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarVacunasSerpiente();
    // console.log('Sistema de chequeos preventivos para serpientes listo.');
});