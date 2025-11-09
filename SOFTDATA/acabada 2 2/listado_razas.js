let razasData = [];

// Cargar JSON
fetch('razas.json')
    .then(response => response.json())
    .then(data => {
        razasData = data;
        const select = document.getElementById('raza_perro');
        data.forEach(r => {
            const option = document.createElement('option');
            option.value = r.nombre; // valor será el nombre de la raza
            option.textContent = r.nombre;
            select.appendChild(option);
        });
    })
    .catch(error => console.error('Error cargando JSON:', error));