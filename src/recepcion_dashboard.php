<?php
session_start();
// Validación de sesión para recepción
if (!isset($_SESSION['recep_id'])) { header("Location: login_recepcion.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recepción - Panel de Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> 
    <style>
        /* Estilos base manteniendo tu diseño original */
        .header-recepcion {
            background: linear-gradient(0.25turn, #81e84e, #16f21d, #d5ff02, #00fbee, #3400ce);
            animation: fanimado 10s infinite;
            background-size: 500%;
            padding: 40px;
            color: white;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0% 100%);
        }
        .main-container { padding: 40px; max-width: 1300px; margin: auto; }
        .card-custom {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #eee;
        }
        .tabla-recepcion { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .tabla-recepcion th { color: #555; padding: 15px; font-weight: 600; text-align: left; }
        .tabla-recepcion td { 
            background: #fdfdfd; padding: 15px; border-top: 1px solid #eee; border-bottom: 1px solid #eee;
        }
        .btn-recep {
            background: #34495e; color: white; border: none; padding: 10px 20px;
            border-radius: 8px; cursor: pointer; transition: 0.3s; font-weight: bold;
        }
        .btn-recep:hover { background-color: #16f21d; transform: scale(1.05); }
        .busqueda-input {
            width: 100%; padding: 15px; border-radius: 10px; border: 2px solid #81e84e;
            font-size: 18px; margin-bottom: 20px; box-sizing: border-box;
        }
        #modalDetalle, #modalAgendar {
            display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
            background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;
        }
        .detalle-item { margin-bottom: 12px; font-size: 16px; text-align: left; }
        .detalle-label { font-weight: bold; color: #555; display: block; font-size: 12px; text-transform: uppercase; margin-bottom: 4px; }
        @keyframes slideIn { from { transform:translateY(-20px); opacity:0; } to { transform:translateY(0); opacity:1; } }
    </style>
</head>
<body>

<div id="modalDetalle">
    <div class="card-custom" style="width:500px; position:relative; animation: slideIn 0.3s ease;">
        <span onclick="cerrarModal()" style="position:absolute; top:15px; right:20px; cursor:pointer; font-size:28px; color:#888;">&times;</span>
        <h2 style="border-bottom: 2px solid #16f21d; padding-bottom:10px; margin-top:0;">Detalles de la Cita</h2>
        <div id="contenidoDetalle" style="margin-top:20px;"></div>
        <div style="margin-top:25px; text-align:right;">
            <button class="btn-recep" onclick="cerrarModal()" style="background:#747d8c;">Cerrar</button>
        </div>
    </div>
</div>

<div id="modalAgendar">
    <div class="card-custom" style="width:450px; position:relative; animation: slideIn 0.3s ease;">
        <span onclick="cerrarModalAgendar()" style="position:absolute; top:15px; right:20px; cursor:pointer; font-size:24px;">&times;</span>
        <h2>📅 Agendar Cita: <span id="nombreMascotaCita" style="color:#16f21d;"></span></h2>
        
        <form id="formNuevaCita">
            <input type="hidden" id="id_mascota_hidden" name="fk_id_mascota">
            <input type="hidden" id="monto_congelado_hidden" name="monto_base_congelado" value="0">
            <input type="hidden" name="accion" value="guardar_cita">
            
            <label class="detalle-label">Médico y Especialidad</label>
            <select name="fk_id_asignacion" id="selectAsignacion" class="busqueda-input" style="font-size:14px;" required onchange="actualizarMonto(this)">
                <option value="">Seleccione un médico...</option>
            </select>

            <div style="display:flex; gap:10px;">
                <div style="flex:1;">
                    <label class="detalle-label">Fecha</label>
                    <input type="date" name="fecha_cita" class="busqueda-input" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div style="flex:1;">
                    <label class="detalle-label">Hora</label>
                    <input type="time" name="hora_cita" class="busqueda-input" required>
                </div>
            </div>

            <label class="detalle-label">Motivo de consulta</label>
            <textarea name="motivo_cliente" class="busqueda-input" style="height:80px; font-size:14px;" placeholder="Ej. Revisión general..."></textarea>

            <div id="displayPrecio" style="margin-bottom: 15px; font-weight: bold; color: #2c3e50; text-align: center;">
                Precio Consulta: $0.00
            </div>

            <button type="submit" class="btn-recep" style="width:100%; background:#16f21d; font-size:16px;">
                <i class="fas fa-save"></i> CONFIRMAR CITA
            </button>
        </form>
    </div>
</div>

<header class="header-recepcion">
    <h1>PANEL DE RECEPCIÓN</h1>
    <p>Control de Citas y Pacientes</p>
    <div style="margin-top: 20px;">
        <span><i class="fas fa-user-tie"></i> Recepcionista: <?php echo $_SESSION['recep_nombre']; ?></span>
        <a href="logout.php" style="color: white; margin-left: 20px; text-decoration: none; font-weight: bold;">[ Salir ]</a>
    </div>
</header>

<div class="main-container">
    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <div class="card-custom" style="flex: 1;">
            <h2><i class="fas fa-search"></i> Buscador Global</h2>
            <input type="text" id="busquedaGlobal" class="busqueda-input" placeholder="Nombre de mascota o dueño...">
            <div id="resultadosBusqueda" style="max-height: 500px; overflow-y: auto;">
                <p>Escribe para buscar...</p>
            </div>
        </div>

        <div class="card-custom" style="flex: 1.5;">
            <h2><i class="fas fa-calendar-check"></i> Citas Recientes</h2>
            <div style="max-height: 550px; overflow-y: auto;">
                <table class="tabla-recepcion">
                    <thead>
                        <tr>
                            <th>HORARIO</th>
                            <th>PACIENTE</th>
                            <th>DOCTOR</th>
                            <th>ESTADO</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="tablaMaestraCitas"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    cargarCitasMaestras();

    // Buscador en tiempo real
    document.getElementById('busquedaGlobal').addEventListener('input', (e) => {
        const texto = e.target.value.trim();
        if (texto.length >= 2) buscarMascotas(texto);
        else document.getElementById('resultadosBusqueda').innerHTML = '<p>Escribe para buscar...</p>';
    });

    // Envío del formulario Agendar
    document.getElementById('formNuevaCita').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('obtener_todo_recepcion.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert("✅ Cita agendada correctamente");
                cerrarModalAgendar();
                this.reset();
                cargarCitasMaestras();
            } else {
                // Mejora: Ahora capturamos el mensaje de error personalizado enviado desde PHP
                // Esto mostrará "El médico ya tiene una cita agendada..." en lugar del error de SQL
                alert("⚠️ ATENCIÓN: " + (res.error || "No se pudo guardar la cita"));
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error de conexión al intentar guardar la cita.");
        });
    });
});

function actualizarMonto(select) {
    const option = select.options[select.selectedIndex];
    if (!option.value) {
        document.getElementById('displayPrecio').innerText = "Precio Consulta: $0.00";
        document.getElementById('monto_congelado_hidden').value = "0";
        return;
    }
    const match = option.text.match(/\$(\d+(\.\d+)?)/);
    const precio = match ? match[1] : "0";
    document.getElementById('monto_congelado_hidden').value = precio;
    document.getElementById('displayPrecio').innerText = "Precio Consulta: $" + precio;
}

function cargarCitasMaestras() {
    fetch('obtener_todo_recepcion.php?accion=listar_citas')
        .then(res => res.json())
        .then(data => {
            const tabla = document.getElementById('tablaMaestraCitas');
            tabla.innerHTML = '';
            data.forEach(c => {
                let badgeColor = '#f1c40f'; // Por defecto: Agendada/Pendiente
                if(c.estatus_cita === 'Finalizada') badgeColor = '#2ecc71';
                if(c.estatus_cita === 'Cancelada') badgeColor = '#e74c3c';

                tabla.innerHTML += `
                    <tr>
                        <td><b>${c.fecha_cita}</b><br><small>${c.hora_cita}</small></td>
                        <td><b>${c.mascota}</b><br><small>${c.dueno}</small></td>
                        <td>${c.nombre_doctor}</td>
                        <td><span style="background:${badgeColor}; color:white; padding:5px 10px; border-radius:15px; font-size:11px;">${c.estatus_cita}</span></td>
                        <td><button class="btn-recep" onclick="verDetalle(${c.id_cita})">Ver</button></td>
                    </tr>`;
            });
        });
}

function buscarMascotas(query) {
    fetch(`obtener_todo_recepcion.php?accion=buscar_mascotas&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            const contenedor = document.getElementById('resultadosBusqueda');
            contenedor.innerHTML = ''; 
            if (data.length === 0) {
                contenedor.innerHTML = '<p>❌ Sin coincidencias.</p>';
                return;
            }
            data.forEach(m => {
                contenedor.innerHTML += `
                    <div style="background:#fff; border:1px solid #81e84e; padding:15px; border-radius:10px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <b>🐾 ${m.mascota}</b> <small>(${m.tipo_mascota})</small><br>
                            <span>👤 Dueño: ${m.dueno} ${m.apellido}</span>
                        </div>
                        <button class="btn-recep" onclick="prepararCita(${m.idmascota}, '${m.mascota}')" style="background:#16f21d">AGENDAR</button>
                    </div>`;
            });
        });
}

function prepararCita(idMascota, nombreMascota) {
    document.getElementById('id_mascota_hidden').value = idMascota;
    document.getElementById('nombreMascotaCita').innerText = nombreMascota;
    document.getElementById('displayPrecio').innerText = "Precio Consulta: $0.00";
    
    fetch('obtener_todo_recepcion.php?accion=listar_doctores')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('selectAsignacion');
            select.innerHTML = '<option value="">Seleccione un médico...</option>';
            data.forEach(d => {
                select.innerHTML += `<option value="${d.id_asignacion}">${d.nombre_doctor} - ${d.nombre_especialidad} ($${d.precio_base})</option>`;
            });
            document.getElementById('modalAgendar').style.display = 'flex';
        });
}

function verDetalle(idCita) {
    fetch(`obtener_todo_recepcion.php?accion=detalle_cita&id=${idCita}`)
        .then(res => res.json())
        .then(c => {
            const contenedor = document.getElementById('contenidoDetalle');
            contenedor.innerHTML = `
                <div class="detalle-item">
                    <span class="detalle-label">Paciente y Dueño</span>
                    <p><b>${c.mascota}</b> (${c.tipo_mascota})<br>Cliente: ${c.dueno}</p>
                </div>
                <div class="detalle-item">
                    <span class="detalle-label">Médico</span>
                    <p>Dr. ${c.nombre_doctor} (${c.nombre_especialidad})</p>
                </div>
                <div class="detalle-item">
                    <span class="detalle-label">Fecha y Hora</span>
                    <p>📅 ${c.fecha_cita} | ⏰ ${c.hora_cita}</p>
                </div>
                <div class="detalle-item">
                    <span class="detalle-label">Monto Congelado</span>
                    <p><b>$${c.monto_base_congelado}</b></p>
                </div>
                <div class="detalle-item">
                    <span class="detalle-label">Motivo</span>
                    <p>${c.motivo_cliente || 'Sin motivo especificado'}</p>
                </div>`;
            document.getElementById('modalDetalle').style.display = 'flex';
        });
}

function cerrarModal() { document.getElementById('modalDetalle').style.display = 'none'; }
function cerrarModalAgendar() { document.getElementById('modalAgendar').style.display = 'none'; }
</script>
</body>
</html>