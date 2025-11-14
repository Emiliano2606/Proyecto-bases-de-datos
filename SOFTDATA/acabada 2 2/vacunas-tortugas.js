// Lista de chequeos preventivos y tratamientos comunes para Tortugas.
// El enfoque está en el control de parásitos, salud de caparazón y manejo de enfermedades comunes de quelonios.
const vacunasTortuga = [
    { nombre: "Chequeo Parasitario (Coprológico)", valor: "chequeo_parasitario_tortuga" },
    { nombre: "Desparasitación Interna (última dosis)", valor: "desparasitacion_int_tortuga" },
    { nombre: "Examen de Caparazón y Plastrón", valor: "examen_caparazon" },
    { nombre: "Prueba de Herpesvirus (Quelonios)", valor: "prueba_herpes_tortuga" },
    { nombre: "Suplemento de Calcio y D3 (Inicio de uso)", valor: "suplemento_calcio_tortuga" },
    { nombre: "Revisión Anual Veterinaria (Fecha)", valor: "chequeo_anual_tortuga" },
    { nombre: "Tratamiento por Rinovirus/Neumonía", valor: "tratamiento_neumonia_tortuga" },
    { nombre: "Tratamiento por Deficiencia de Vitamina A (Ojos)", valor: "tratamiento_vitamina_a" },
    { nombre: "Manejo de Gota/Enfermedad Renal", valor: "manejo_gota_tortuga" },
    { nombre: "Análisis de Hemoparásitos", valor: "hemoparasitos_tortuga" }
];

// Función para cargar los chequeos/tratamientos como checkboxes en el contenedor 6 (Tortugas)
function cargarVacunasTortuga() {
    // ID único para tortugas
    const containerCheckboxes = document.getElementById('lista-vacunas-checkbox6');

    if (!containerCheckboxes) {
        // console.log('No se encontró el elemento lista-vacunas-checkbox6');
        return;
    }

    // Limpiar contenedor
    containerCheckboxes.innerHTML = '';

    // Agregar cada chequeo/tratamiento como checkbox
    vacunasTortuga.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        // Usamos vacunas_tortuga[] como name
        div.innerHTML = `
            <input type="checkbox" id="vacuna_tortuga_${vacuna.valor}" name="vacunas_tortuga[]" value="${vacuna.valor}">
            <label for="vacuna_tortuga_${vacuna.valor}" style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        containerCheckboxes.appendChild(div);

        // Agregar evento a cada checkbox
        const checkbox = div.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', mostrarCamposFechaVacunasTortuga);
    });
}

// Función para mostrar campos de fecha según chequeos/tratamientos seleccionados
function mostrarCamposFechaVacunasTortuga() {
    // ID único para tortugas
    const containerFechas = document.getElementById('fechas-vacunas-container6');
    const checkboxes = document.querySelectorAll('input[name="vacunas_tortuga[]"]:checked');

    if (!containerFechas) return;

    const vacunasSeleccionadas = Array.from(checkboxes).map(checkbox => checkbox.value);

    // Limpiar el contenedor
    containerFechas.innerHTML = '';

    // Crear campo de fecha para cada chequeo seleccionado
    vacunasSeleccionadas.forEach(vacunaValor => {
        const vacunaInfo = vacunasTortuga.find(v => v.valor === vacunaValor);

        if (vacunaInfo) {
            const label = document.createElement('label');
            label.className = 'label-select';
            // Adaptar texto de la etiqueta para que sea más genérico
            label.textContent = `Fecha de ${vacunaInfo.nombre} *`;

            label.htmlFor = `fecha_tortuga_${vacunaValor}`;

            const input = document.createElement('input');
            input.className = 'namee';
            input.type = 'date';

            input.id = `fecha_tortuga_${vacunaValor}`;
            input.name = `fecha_tortuga_${vacunaValor}`; // Nombre único para el envío
            input.required = true;

            containerFechas.appendChild(label);
            containerFechas.appendChild(input);
        }
    });
}

// Ejecutar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarVacunasTortuga();
    // console.log('Sistema de chequeos preventivos para tortugas listo.');
});