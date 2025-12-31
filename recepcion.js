document.addEventListener('DOMContentLoaded', () => {
    cargarCitasMaestras();

    // Evento para el buscador
    const inputBusqueda = document.getElementById('busquedaGlobal');
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', (e) => {
            const texto = e.target.value;
            if (texto.length > 2) buscarMascotas(texto);
        });
    }
});

// 1. CARGAR TODAS LAS CITAS DEL SISTEMA
function cargarCitasMaestras() {
    fetch('obtener_todo_recepcion.php?accion=listar_citas')
        .then(res => res.json())
        .then(data => {
            const tabla = document.getElementById('tablaMaestraCitas');
            tabla.innerHTML = '';
            data.forEach(c => {
                const color = c.estatus_cita === 'Finalizada' ? '#2ecc71' :
                    (c.estatus_cita === 'Cancelada' ? '#e74c3c' : '#f1c40f');

                tabla.innerHTML += `
                    <tr>
                        <td><b>${c.fecha_cita}</b><br>${c.hora_cita}</td>
                        <td>${c.mascota}</td>
                        <td>${c.dueno}</td>
                        <td>${c.nombre_doctor}<br><small>${c.nombre_especialidad}</small></td>
                        <td><span class="status-badge" style="background:${color}">${c.estatus_cita}</span></td>
                        <td>
                            <button class="btn-accion" onclick="verFicha(${c.id_cita})" style="background:#34495e; color:white;">Ver</button>
                        </td>
                    </tr>
                `;
            });
        });
}

// 2. BUSCADOR EN TIEMPO REAL
function buscarMascotas(query) {
    fetch(`obtener_todo_recepcion.php?accion=buscar_mascotas&q=${query}`)
        .then(res => res.json())
        .then(data => {
            const contenedor = document.getElementById('resultadosBusqueda');
            contenedor.innerHTML = '';
            data.forEach(m => {
                contenedor.innerHTML += `
                    <div style="padding:10px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <strong>${m.mascota}</strong> (${m.tipo_mascota})<br>
                            <small>Dueño: ${m.dueno} ${m.apellido}</small>
                        </div>
                        <button onclick="agendarARecepción(${m.idmascota})" style="background:#00b894; color:white; border:none; padding:5px; border-radius:3px; cursor:pointer;">
                            + Cita
                        </button>
                    </div>
                `;
            });
        });
}