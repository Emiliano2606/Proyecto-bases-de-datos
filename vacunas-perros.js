const vacunasPerro = [
    { nombre: "Rabia", valor: "rabia" },
    { nombre: "Moquillo (Distemper)", valor: "moquillo" },
    { nombre: "Parvovirus", valor: "parvovirus" },
    { nombre: "Hepatitis canina (Adenovirus-2)", valor: "hepatitis" },
    { nombre: "Leptospirosis", valor: "leptospirosis" },
    { nombre: "Traqueobronquitis (Bordetella)", valor: "traqueobronquitis" },
    { nombre: "Influenza canina H3N2", valor: "influenza_h3n2" },
    { nombre: "Influenza canina H3N8", valor: "influenza_h3n8" },
    { nombre: "Coronavirus canino", valor: "coronavirus" },
    { nombre: "Giardia", valor: "giardia" },
    { nombre: "Leishmaniosis", valor: "leishmaniosis" },
    { nombre: "Polivalente (Moquillo + Hepatitis + Parvovirus)", valor: "polivalente_basica" },
    { nombre: "Polivalente Completa (Moquillo + Hepatitis + Parvovirus + Leptospirosis)", valor: "polivalente_completa" },
    { nombre: "Triple (Bordetella + Parainfluenza)", valor: "triple_respiratoria" },
    { nombre: "Vacuna contra hongos (Microsporum)", valor: "hongos" }
];

// Función para cargar las vacunas como checkboxes
function cargarVacunasPerro() {
    const containerCheckboxes = document.getElementById('lista-vacunas-checkbox');
    
    if (!containerCheckboxes) {
        console.log('No se encontró el elemento lista-vacunas-checkbox');
        return;
    }
    
    // Limpiar contenedor
    containerCheckboxes.innerHTML = '';
    
    // Agregar cada vacuna como checkbox
    vacunasPerro.forEach(vacuna => {
        const div = document.createElement('div');
        div.className = 'checkbox-vacuna';
        div.innerHTML = `
            <input type="checkbox" id="vacuna_${vacuna.valor}" name="vacunas_perro[]" value="${vacuna.valor}">
            <label for="vacuna_${vacuna.valor}" style="margin-left: 8px;">${vacuna.nombre}</label>
        `;
        containerCheckboxes.appendChild(div);
        
        // Agregar evento a cada checkbox
        const checkbox = div.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', mostrarCamposFechaVacunas);
    });
}

// Función para mostrar campos de fecha (modificada para checkboxes)
function mostrarCamposFechaVacunas() {
    const containerFechas = document.getElementById('fechas-vacunas-container');
    const checkboxes = document.querySelectorAll('input[name="vacunas_perro[]"]:checked');
    
    if (!containerFechas) return;
    
    const vacunasSeleccionadas = Array.from(checkboxes).map(checkbox => checkbox.value);
    
    // Limpiar el contenedor
    containerFechas.innerHTML = '';
    
    // Crear campo de fecha para cada vacuna seleccionada
    vacunasSeleccionadas.forEach(vacunaValor => {
        const vacunaInfo = vacunasPerro.find(v => v.valor === vacunaValor);
        
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
    cargarVacunasPerro();
    console.log('Sistema de vacunas para perros cargado correctamente');
});
