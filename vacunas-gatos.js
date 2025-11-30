// Lista de vacunas del gato
const vacunasGato = [
    { nombre: "Herpesvirus felino-1 (FHV)", valor: "fhv" },
    { nombre: "Calicivirus felino (FCV)", valor: "fcv" },
    { nombre: "Virus de la panleucopenia felina (FPV)", valor: "fpv" },
    { nombre: "FPV + FHV + FCV", valor: "fpv_fhv_fcv" },
    { nombre: "FHV + FCV", valor: "fhv_fcv" },
    { nombre: "Rabia", valor: "rabia" },
    { nombre: "FelV", valor: "felv" },
    { nombre: "Virus de la inmunodeficiencia felina", valor: "fiv" },
    { nombre: "Chlamydia felis", valor: "chlamydia" },
    { nombre: "Bordetella bronchiseptica", valor: "bordetella" },
    { nombre: "Virus de la peritonitis infecciosa felina (FIP)", valor: "fip" },
    { nombre: "Giardia spp.", valor: "giardia" },
    { nombre: "Microsporum canis", valor: "microsporum" },
    { nombre: "Enfermedad de Marek", valor: "marek" }
];

// Función para cargar las vacunas como checkboxes
function cargarVacunasGato() {
    const containerCheckboxes = document.getElementById('lista-vacunas-checkbox2');

    if (!containerCheckboxes) {
        console.log('No se encontró el elemento lista-vacunas-checkbox');
        return;
    }

    // Limpiar contenedor
    containerCheckboxes.innerHTML = '';

    // Agregar cada vacuna como checkbox
    vacunasGato.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox" id="vacuna_${vacuna.valor}" name="vacunas_gato[]" value="${vacuna.valor}">
            <label for="vacuna_${vacuna.valor}" style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        containerCheckboxes.appendChild(div);

        // Agregar evento a cada checkbox
        const checkbox = div.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', mostrarCamposFechaVacunasGato);
    });
}

// Función para mostrar campos de fecha según vacunas seleccionadas
function mostrarCamposFechaVacunasGato() {
    const containerFechas = document.getElementById('fechas-vacunas-container2');
    const checkboxes = document.querySelectorAll('input[name="vacunas_gato[]"]:checked');

    if (!containerFechas) return;

    const vacunasSeleccionadas = Array.from(checkboxes).map(checkbox => checkbox.value);

    // Limpiar el contenedor
    containerFechas.innerHTML = '';

    // Crear campo de fecha para cada vacuna seleccionada
    vacunasSeleccionadas.forEach(vacunaValor => {
        const vacunaInfo = vacunasGato.find(v => v.valor === vacunaValor);

        if (vacunaInfo) {
            const label = document.createElement('label');
            label.className = 'label-select';
            label.textContent = `Fecha de última colocación de ${vacunaInfo.nombre} *`;
            label.htmlFor = `fecha_${vacunaValor}`;

            const input = document.createElement('input');
            input.className = 'namee';
            input.type = 'date';
            input.id = `fecha_${vacunaValor}`;
            input.name = `fecha_${vacunaValor}`;
            input.required = true;

            containerFechas.appendChild(label);
            containerFechas.appendChild(input);
        }
    });
}

// Ejecutar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarVacunasGato();
    console.log('Sistema de vacunas para gatos cargado correctamente');
});