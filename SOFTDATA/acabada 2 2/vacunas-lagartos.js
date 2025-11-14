// Lista de chequeos preventivos y tratamientos comunes para lagartos.
// En reptiles, el enfoque es en profilaxis y chequeos de salud, más que en vacunas virales.
const vacunasLagarto = [
    { nombre: "Chequeo Parasitario (Coprológico)", valor: "chequeo_parasitario" },
    { nombre: "Desparasitación Interna (última dosis)", valor: "desparasitacion_int" },
    { nombre: "Suplemento de Calcio y D3 (Inicio de uso)", valor: "suplemento_calcio" },
    { nombre: "Prueba de Herpesvirus (Ophidian/Iguanid)", valor: "prueba_herpes" },
    { nombre: "Chequeo Anual General (Fecha)", valor: "chequeo_anual" },
    { nombre: "Gota/Enfermedad Renal (Inicio de Tratamiento)", valor: "gota_tratamiento" },
    { nombre: "Vitaminas del Complejo B (Inicio de Terapia)", valor: "vitamina_b" },
    { nombre: "Examen de Piel (Enfermedad Mular)", valor: "examen_piel" },
    { nombre: "Manejo de Estomatitis (Inicio de Terapia)", valor: "estomatitis" },
    { nombre: "Control de Ácaros (Fecha de Desinfección)", valor: "control_acaros" },
    { nombre: "Examen de Hemoparásitos", valor: "hemoparasitos" },
    { nombre: "Antibiótico de amplio espectro (Fecha inicio)", valor: "antibiotico_espectro" }
];

// Función para cargar los chequeos/tratamientos como checkboxes en el contenedor 4 (Lagartos)
function cargarVacunasLagarto() {
    // ID único para lagartos
    const containerCheckboxes = document.getElementById('lista-vacunas-checkbox4');

    if (!containerCheckboxes) {
        // console.log('No se encontró el elemento lista-vacunas-checkbox4');
        return;
    }

    // Limpiar contenedor
    containerCheckboxes.innerHTML = '';

    // Agregar cada chequeo/tratamiento como checkbox
    vacunasLagarto.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        // Usamos vacunas_lagarto[] como name
        div.innerHTML = `
            <input type="checkbox" id="vacuna_lagarto_${vacuna.valor}" name="vacunas_lagarto[]" value="${vacuna.valor}">
            <label for="vacuna_lagarto_${vacuna.valor}" style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        containerCheckboxes.appendChild(div);

        // Agregar evento a cada checkbox
        const checkbox = div.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', mostrarCamposFechaVacunasLagarto);
    });
}

// Función para mostrar campos de fecha según chequeos/tratamientos seleccionados
function mostrarCamposFechaVacunasLagarto() {
    // ID único para lagartos
    const containerFechas = document.getElementById('fechas-vacunas-container4');
    const checkboxes = document.querySelectorAll('input[name="vacunas_lagarto[]"]:checked');

    if (!containerFechas) return;

    const vacunasSeleccionadas = Array.from(checkboxes).map(checkbox => checkbox.value);

    // Limpiar el contenedor
    containerFechas.innerHTML = '';

    // Crear campo de fecha para cada chequeo seleccionado
    vacunasSeleccionadas.forEach(vacunaValor => {
        const vacunaInfo = vacunasLagarto.find(v => v.valor === vacunaValor);

        if (vacunaInfo) {
            const label = document.createElement('label');
            label.className = 'label-select';
            // Adaptar texto de la etiqueta para que sea más genérico
            label.textContent = `Fecha de ${vacunaInfo.nombre} *`;

            label.htmlFor = `fecha_lagarto_${vacunaValor}`;

            const input = document.createElement('input');
            input.className = 'namee';
            input.type = 'date';

            input.id = `fecha_lagarto_${vacunaValor}`;
            input.name = `fecha_lagarto_${vacunaValor}`; // Nombre único para el envío
            input.required = true;

            containerFechas.appendChild(label);
            containerFechas.appendChild(input);
        }
    });
}

// Ejecutar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarVacunasLagarto();
    // console.log('Sistema de chequeos preventivos para lagartos listo.');
});