// Lista de vacunas de aves comunes, incluyendo psitácidas (loros, periquitos) y aves de corral clave
const vacunasAve = [
    { nombre: "Virus de la Enfermedad de Newcastle (NDV)", valor: "newcastle" },
    { nombre: "Viruela Aviar (Fowl Pox)", valor: "viruela_aviar" },
    { nombre: "Herpesvirus del Pato (Duck Virus Enteritis - DVE)", valor: "dve" },
    { nombre: "Gumboro (Enfermedad de la Bolsa de Fabricio)", valor: "gumboro" },
    { nombre: "Bronquitis Infecciosa Aviar (IBV)", valor: "bronquitis_infecciosa" },
    { nombre: "Cólera Aviar (Pasteurella multocida)", valor: "colera_aviar" },
    { nombre: "Micoplasmosis (Mycoplasma gallisepticum)", valor: "micoplasmosis" },
    { nombre: "Paramixovirus-1 (Para psitácidas)", valor: "paramixovirus_1" },
    { nombre: "Psitacosis (Chlamydophila psittaci)", valor: "psitacosis" },
    { nombre: "Pacheco's Disease (Herpesvirus)", valor: "pachecos_disease" },
];

// Función para cargar las vacunas como checkboxes en el contenedor 3
function cargarVacunasAve() {
    // Usamos el ID solicitado: lista-vacunas-checkbox3
    const containerCheckboxes = document.getElementById('lista-vacunas-checkbox3');

    if (!containerCheckboxes) {
        // console.log('No se encontró el elemento lista-vacunas-checkbox3');
        return;
    }

    // Limpiar contenedor
    containerCheckboxes.innerHTML = '';

    // Agregar cada vacuna como checkbox
    vacunasAve.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        // Aseguramos que el 'name' sea único para las aves: name="vacunas_ave[]"
        div.innerHTML = `
            <input type="checkbox" id="vacuna_ave_${vacuna.valor}" name="vacunas_ave[]" value="${vacuna.valor}">
            <label for="vacuna_ave_${vacuna.valor}" style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        containerCheckboxes.appendChild(div);

        // Agregar evento a cada checkbox
        const checkbox = div.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', mostrarCamposFechaVacunasAve);
    });
}

// Función para mostrar campos de fecha según vacunas seleccionadas
function mostrarCamposFechaVacunasAve() {
    // Usamos el ID solicitado: fechas-vacunas-container3
    const containerFechas = document.getElementById('fechas-vacunas-container3');
    // Buscamos los checkboxes de las aves
    const checkboxes = document.querySelectorAll('input[name="vacunas_ave[]"]:checked');

    if (!containerFechas) return;

    const vacunasSeleccionadas = Array.from(checkboxes).map(checkbox => checkbox.value);

    // Limpiar el contenedor
    containerFechas.innerHTML = '';

    // Crear campo de fecha para cada vacuna seleccionada
    vacunasSeleccionadas.forEach(vacunaValor => {
        const vacunaInfo = vacunasAve.find(v => v.valor === vacunaValor);

        if (vacunaInfo) {
            const label = document.createElement('label');
            label.className = 'label-select';
            label.textContent = `Fecha de última colocación de ${vacunaInfo.nombre} *`;
            // Aseguramos que el 'for' y el 'id' sean únicos
            label.htmlFor = `fecha_ave_${vacunaValor}`;

            const input = document.createElement('input');
            input.className = 'namee';
            input.type = 'date';
            // Aseguramos que el 'id' y el 'name' sean únicos
            input.id = `fecha_ave_${vacunaValor}`;
            input.name = `fecha_ave_${vacunaValor}`;
            input.required = true;

            containerFechas.appendChild(label);
            containerFechas.appendChild(input);
        }
    });
}

// Ejecutar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    cargarVacunasAve();
    // console.log('Sistema de vacunas para aves cargado correctamente');
});