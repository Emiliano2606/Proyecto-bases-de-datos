<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../es/registro_mascota.html"); 
    exit();
}

$nombreUsuario = $_SESSION['nombre'];
$apellidoUsuario = $_SESSION['apellido'];
$emailUsuario = $_SESSION['email'];
$telefonoUsuario = $_SESSION['telefono_principal'];
$direccionUsuario = $_SESSION['direccion'] ?? 'Dirección no disponible';
$mis_mascotas = isset($_SESSION['mis_mascotas']) ? $_SESSION['mis_mascotas'] : [];
$mascotas_js = json_encode($mis_mascotas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Historial Clínico</title>
    <link rel="stylesheet" href="../es/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .selector-mascota { padding: 12px; border-radius: 10px; border: 2px solid #5d4037; margin-bottom: 20px; width: 100%; font-size: 16px; background-color: white; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .detalle-item { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #d4c8bf; padding-bottom: 5px; }
        .detalle-label { font-weight: bold; color: #5d4037; font-size: 0.85rem; text-transform: uppercase; }
        .detalle-valor { color: #333; text-align: right; font-size: 0.9rem; }
        .vacuna-fecha { font-size: 0.8rem; color: #777; font-style: italic; }

        /* MODALES */
        .modal-personalizado { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(3px); }
        .modal-contenido { background-color: white; margin: 5% auto; padding: 25px; border-radius: 15px; width: 90%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative; }
        .cerrar-modal { position: absolute; right: 20px; top: 15px; font-size: 28px; font-weight: bold; color: #5d4037; cursor: pointer; }
        
        .btn-agendar { background: #5d4037; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; width: 100%; margin-top: 10px; font-weight: bold; transition: 0.3s; }
        .btn-agendar:hover { background: #795548; }
        
        .grid-horarios { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 15px 0; max-height: 180px; overflow-y: auto; padding: 5px; }
        .bloque-tiempo { padding: 10px; border: 1px solid #d4c8bf; text-align: center; border-radius: 8px; cursor: pointer; font-size: 0.9rem; background: #fdfaf8; transition: 0.2s; }
        .bloque-tiempo.ocupado { background: #eee; color: #bbb; cursor: not-allowed; border-color: #ddd; }
        .bloque-tiempo.seleccionado { background: #5d4037; color: white; border-color: #5d4037; }
        
        .btn-confirmar { background: #2ecc71; color: white; border: none; padding: 12px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer; margin-top: 15px; }
        textarea { width: 100%; border-radius: 8px; border: 1px solid #d4c8bf; padding: 10px; resize: none; margin-top: 5px; }

        /* Estilo para items de la lista de citas */
        .cita-card-mini { background: #fdfaf8; border: 1px solid #d4c8bf; padding: 12px; border-radius: 10px; margin-bottom: 10px; transition: 0.3s; }
        .cita-card-mini:hover { border-color: #5d4037; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    </style>
</head>

<body>
    <div class="titutifuti">
        <div class="pepino">
            <div class="uti">
                <i class="fas fa-circle browser-red"></i>
                <i class="fas fa-circle browser-yellow"></i>
                <i class="fas fa-circle browser-green"></i>
            </div>
        </div>

        <div style="padding: 20px;">
            <label style="font-weight: bold;">Mascota en pantalla:</label>
            <select id="selectorMascota" class="selector-mascota" onchange="actualizarDashboard(this.value)">
                <?php foreach ($mis_mascotas as $index => $m): ?>
                    <option value="<?php echo $index; ?>"><?php echo htmlspecialchars($m['nombre']); ?> (<?php echo htmlspecialchars($m['tipo_mascota']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="emilianiiiii">
            <main class="tralalero-tralala">
                <div class="sape card">
                    <div class="sape2">
                        <img src="" class="tun-tun" id="profile-image">
                        <div class="sahur">
                            <h2 id="display-nombre-mascota"></h2>
                            <p id="display-especie-top"></p>
                        </div>
                    </div>
                    <div class="picoro">
                        <h4><i class="fa-solid fa-user-tie"></i> Información del Tutor</h4>
                        <p><strong>Dueño:</strong> <?php echo htmlspecialchars($nombreUsuario . " " . $apellidoUsuario); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($emailUsuario); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefonoUsuario); ?></p>
                        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($direccionUsuario); ?></p>
                    </div>
                </div>

                <div class="cabezon">
                    <div class="card trunk">
                        <h4><i class="fa-solid fa-dna"></i> Detalles Clínicos</h4>
                        <div id="lista-especifica"></div>
                    </div>

                    <div class="card cabezoni">
                        <h4><i class="fa-solid fa-syringe"></i> Carnet de Vacunación</h4>
                        <ul id="lista-vacunas" style="list-style: none; padding: 0;"></ul>
                    </div>
                </div>
            </main>

            <aside class="jbalvin">
                <div class="cahcitrula card">
                    <h3><i class="fa-solid fa-calendar-check"></i> Acciones</h3>
                    <p>Mascota: <span id="nombre-agendar" style="font-weight: bold;"></span></p>
                    <button onclick="abrirModalCita()" class="btn-agendar">
                        <i class="fa-solid fa-plus"></i> Agendar Nueva Cita
                    </button>
                </div>

                <div class="card" style="margin-top: 20px;">
                    <h3><i class="fa-solid fa-clock-rotate-left"></i> Mis Citas</h3>
                    <div id="lista-citas-mini" style="max-height: 300px; overflow-y: auto; margin-top: 10px;">
                        </div>
                </div>

                <div class="card" style="text-align: center; margin-top: 20px;">
                    <a href="logout.php" style="color: #ff5f56; text-decoration: none; font-weight: bold;"><i class="fa-solid fa-power-off"></i> Cerrar Sesión</a>
                </div>


                <div class="card" style="margin-top: 20px; border-top: 4px solid #2ecc71;">
    <h3><i class="fa-solid fa-file-medical"></i> Consultas y Recetas</h3>
    <div id="lista-consultas-recetas" style="max-height: 400px; overflow-y: auto; margin-top: 10px;">
        
        </div>
        
</div>
            </aside>
        </div>
    </div>

    <div id="modalCita" class="modal-personalizado">
        <div class="modal-contenido">
            <span class="cerrar-modal" onclick="cerrarModal()">&times;</span>
            <h2><i class="fa-solid fa-paw"></i> Nueva Cita para <span id="modal-nombre-mascota"></span></h2>
            
            <form id="formAgendar">
                <input type="hidden" id="hidden_id_mascota" name="id_mascota">
                <input type="hidden" id="hidden_id_asignacion" name="id_asignacion">
                <input type="hidden" id="hidden_hora" name="hora">
                <input type="hidden" id="hidden_monto" name="monto">

                <label>1. Selecciona Servicio:</label>
                <select id="selectEspecialidad" class="selector-mascota" required onchange="cargarHorariosDisponibles()">
                    <option value="">-- Elige una especialidad --</option>
                </select>

                <div id="info-servicio" style="display:none; background: #fff5e6; border: 1px solid #ff9800; padding: 15px; border-radius: 10px; margin: 10px 0;">
                    <p style="margin: 0; color: #5d4037; font-size: 0.85rem;">
                        <strong>Doctor:</strong> <span id="info-doctor"></span><br>
                        <strong>Sucursal:</strong> <span id="info-sucursal"></span><br>
                        <strong>Atiende:</strong> <span id="info-dias"></span>
                    </p>
                </div>

                <label>2. Selecciona la fecha:</label>
                <input type="date" id="fechaCita" name="fecha" class="selector-mascota" 
                       min="<?php echo date('Y-m-d'); ?>" 
                       onchange="cargarHorariosDisponibles()">

                <div id="areaCalendario">
                    <label>3. Horas disponibles:</label>
                    <div id="contenedorBloques" class="grid-horarios"></div>
                </div>

                <label>4. Motivo:</label>
                <textarea id="motivoCita" name="motivo" placeholder="¿Por qué viene a consulta?" required></textarea>

                <div id="precioFinal" style="margin-top:15px; font-size: 1.1rem; color: #5d4037;">Total: <strong>$0.00</strong></div>
                <button type="submit" class="btn-confirmar">Confirmar Cita</button>
            </form>
        </div>
    </div>

    <div id="modalDetalleCita" class="modal-personalizado">
        <div class="modal-contenido">
            <span class="cerrar-modal" onclick="cerrarModalDetalle()">&times;</span>
            <h2 id="det-servicio" style="color: #5d4037; margin-bottom: 5px;"></h2>
            <span id="det-estado-badge" style="background: #eee; padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase;"></span>
            <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
            
            <div id="cuerpo-detalle" style="line-height: 1.8; font-size: 0.95rem;">
                <p><strong><i class="fa-solid fa-calendar"></i> Fecha:</strong> <span id="det-fecha"></span></p>
                <p><strong><i class="fa-solid fa-clock"></i> Hora:</strong> <span id="det-hora"></span></p>
                <p><strong><i class="fa-solid fa-user-doctor"></i> Doctor:</strong> <span id="det-doctor"></span></p>
                <p><strong><i class="fa-solid fa-location-dot"></i> Sucursal:</strong> <span id="det-sucursal"></span></p>
                <p><strong><i class="fa-solid fa-receipt"></i> Monto:</strong> $<span id="det-monto"></span></p>
                <p><strong><i class="fa-solid fa-note-sticky"></i> Motivo:</strong><br>
                   <span id="det-motivo" style="background: #f9f9f9; display: block; padding: 10px; border-radius: 5px; border-left: 3px solid #d4c8bf;"></span>
                </p>
            </div>
            <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
    <button id="btn-cancelar-cita" class="btn-agendar" style="background: #e74c3c; margin: 0;">
        <i class="fa-solid fa-trash-can"></i> Cancelar Cita
    </button>
    <p id="msg-restriccion" style="font-size: 0.75rem; color: #888; margin-top: 5px; text-align: center;">
        * Solo cancelaciones con 24h de antelación.
    </p>
</div>
        </div>
    </div>

    <script>
        const mascotas = <?php echo $mascotas_js; ?>;
        let horaSeleccionada = null;

        // --- 1. LÓGICA PRINCIPAL DEL DASHBOARD ---
        async function actualizarDashboard(index) {
            if (mascotas.length === 0) return;
            const m = mascotas[index];

            // Datos básicos
            document.getElementById('display-nombre-mascota').innerText = m.nombre;
            document.getElementById('display-especie-top').innerText = m.tipo_mascota;
            document.getElementById('nombre-agendar').innerText = m.nombre;

            // Foto perfil
            const carpetaMap = { 'Perro': 'perro.jfif', 'Gato': 'gato.jfif', 'Ave': 'ave.jfif', 'Lagarto': 'lagarto.webp', 'Serpiente': 'serpiente.jfif', 'Tortuga': 'tortuga.jfif' };
            document.getElementById('profile-image').src = 'uploads/' + (carpetaMap[m.tipo_mascota] || 'enproceso.jpg');

            // Cargar Detalles
            try {
                const response = await fetch(`obtener_detalles.php?id=${m.idmascota}&tipo=${m.tipo_mascota}`);
                const data = await response.json();

                const listaDetalles = document.getElementById('lista-especifica');
                listaDetalles.innerHTML = "";
                if (data.detalles && Object.keys(data.detalles).length > 0) {
                    for (const [key, value] of Object.entries(data.detalles)) {
                        if (value && value !== "" && !key.includes('fk_id_mascota')) {
                            let label = key.replace(/_/g, ' ').replace(/(perro|gato|ave|lagarto|serpiente|tortuga)/gi, '').trim().toUpperCase();
                            listaDetalles.innerHTML += `<div class="detalle-item"><span class="detalle-label">${label}:</span><span class="detalle-valor">${value}</span></div>`;
                        }
                    }
                } else {
                    listaDetalles.innerHTML = "<p>Sin detalles específicos.</p>";
                }

            } catch (e) { console.error("Error cargando detalles:", e); }

            // IMPORTANTE: Cargar las citas, consultas y el NUEVO CARNET DE VACUNAS
            cargarCitasMascota();
            cargarConsultasRecetas(m.idmascota);
            cargarVacunasMascota(m.idmascota); // Nueva función integrada
        }

        // --- FUNCIÓN PARA CARGAR EL CARNET DE VACUNACIÓN ---
       async function cargarVacunasMascota(idMascota) {
    const lista = document.getElementById('lista-vacunas');
    if(!lista) return;

    try {
        const res = await fetch(`obtener_vacunas_mascota.php?id_mascota=${idMascota}`);
        
        // Verificamos si la respuesta es OK (200)
        if (!res.ok) throw new Error('Error en el servidor');

        const vacunas = await res.json();
        
        lista.innerHTML = "";

        // Si el JSON trae un error del PHP
        if (vacunas.error) {
            console.error("Error desde PHP:", vacunas.error);
            lista.innerHTML = "<li>Error al obtener vacunas.</li>";
            return;
        }

        if (vacunas.length === 0) {
            lista.innerHTML = "<li style='color:#888; font-size:0.8rem; padding:10px;'>Sin vacunas registradas.</li>";
            return;
        }

        vacunas.forEach(v => {
            const item = document.createElement('li');
            item.style = `background: #f8f9fa; border-left: 4px solid #2ecc71; padding: 10px; margin-bottom: 8px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);`;
            item.innerHTML = `
                <div>
                    <div style="font-weight:bold; font-size:0.85rem; color:#2c3e50;">${v.nombre_vacuna}</div>
                    <div style="font-size:0.7rem; color:#7f8c8d;">Estatus: Aplicada</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.75rem; font-weight:bold; color:#27ae60;">${v.fecha_aplicacion}</div>
                    <i class="fa-solid fa-check-circle" style="color:#27ae60; font-size:0.8rem;"></i>
                </div>`;
            lista.appendChild(item);
        });
    } catch (e) {
        console.error("Error detallado:", e);
        lista.innerHTML = "<li>No se pudo cargar el carnet.</li>";
    }
}
        // --- 2. LÓGICA DE AGENDAR CITA ---
        async function abrirModalCita() {
            const index = document.getElementById('selectorMascota').value;
            const m = mascotas[index];
            document.getElementById('modal-nombre-mascota').innerText = m.nombre;
            document.getElementById('hidden_id_mascota').value = m.idmascota;
            document.getElementById('modalCita').style.display = "block";

            const selectEsp = document.getElementById('selectEspecialidad');
            if (selectEsp.options.length <= 1) {
                try {
                    const res = await fetch('obtener_especialidades_lista.php');
                    const data = await res.json();
                    selectEsp.innerHTML = '<option value="">-- Elige una especialidad --</option>';
                    data.forEach(esp => {
                        let opt = document.createElement('option');
                        opt.value = esp.id_especialidad;
                        opt.text = esp.nombre_especialidad;
                        selectEsp.add(opt);
                    });
                } catch (e) { console.error(e); }
            }
        }

        async function cargarHorariosDisponibles() {
            const idEsp = document.getElementById('selectEspecialidad').value;
            const fecha = document.getElementById('fechaCita').value;
            const contenedor = document.getElementById('contenedorBloques');
            const infoS = document.getElementById('info-servicio');

            if (!idEsp) { infoS.style.display="none"; return; }

            try {
                const res = await fetch(`obtener_disponibilidad.php?id_especialidad=${idEsp}&fecha=${fecha}`);
                const data = await res.json();

                infoS.style.display = "block";
                document.getElementById('info-doctor').innerText = data.doctor || "Asignando...";
                document.getElementById('info-sucursal').innerText = data.sucursal || "Sede Principal";
                if (data.dias_permitidos) document.getElementById('info-dias').innerText = data.dias_permitidos.join(', ');
                
                document.getElementById('precioFinal').innerHTML = `Total: <strong>$${data.precio || '0.00'}</strong>`;
                document.getElementById('hidden_monto').value = data.precio || 0;

                if (!fecha) {
                    contenedor.innerHTML = '<p style="font-size:0.8rem; color:#888;">Selecciona un día de atención.</p>';
                    return;
                }

                if (data.error || !data.bloques) {
                    contenedor.innerHTML = `<p style="color:red; font-size:0.8rem;">${data.error || 'Sin horarios'}</p>`;
                    return;
                }

                contenedor.innerHTML = "";
                data.bloques.forEach(b => {
                    const div = document.createElement('div');
                    div.className = `bloque-tiempo ${b.estado}`;
                    div.innerText = b.hora;
                    if (b.estado === 'disponible') {
                        div.onclick = () => {
                            document.querySelectorAll('.bloque-tiempo').forEach(el => el.classList.remove('seleccionado'));
                            div.classList.add('seleccionado');
                            horaSeleccionada = b.hora;
                            document.getElementById('hidden_hora').value = b.hora;
                            document.getElementById('hidden_id_asignacion').value = data.id_asignacion;
                        };
                    }
                    contenedor.appendChild(div);
                });
            } catch (e) { console.error(e); }
        }

        document.getElementById('formAgendar').onsubmit = async (e) => {
            e.preventDefault();
            if (!horaSeleccionada) { alert("Elige una hora"); return; }
            const formData = new FormData(e.target);
            try {
                const res = await fetch('agendar_cita.php', { method: 'POST', body: formData });
                const r = await res.json();
                if (r.success) {
                    alert("¡Cita registrada!");
                    cerrarModal();
                    cargarCitasMascota(); // Refrescar lista lateral
                } else alert("Error: " + r.error);
            } catch (e) { console.error(e); }
        };

        // --- 3. LÓGICA DE VER CITAS ---
        async function cargarCitasMascota() {
            const index = document.getElementById('selectorMascota').value;
            const m = mascotas[index];
            const listaMini = document.getElementById('lista-citas-mini');

            try {
                const res = await fetch(`obtener_citas.php?id_mascota=${m.idmascota}`);
                const citas = await res.json();
                listaMini.innerHTML = "";

                if (citas.length === 0) {
                    listaMini.innerHTML = "<p style='font-size: 0.8rem; color:#888;'>No tienes citas agendadas.</p>";
                    return;
                }

                citas.forEach(c => {
                    const div = document.createElement('div');
                    div.className = "cita-card-mini";
                    div.innerHTML = `
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <strong>${c.nombre_especialidad}</strong>
                            <span style="font-size:0.7rem; color:#888;">${c.estatus_cita}</span>
                        </div>
                        <div style="font-size:0.8rem; color:#555; margin: 4px 0;">
                            <i class="fa-regular fa-calendar"></i> ${c.fecha_cita} | ${c.hora_cita.substring(0,5)}
                        </div>
                        <button onclick='verDetalleCita(${JSON.stringify(c)})' 
                                style='background:none; border:none; color:#5d4037; text-decoration:underline; cursor:pointer; font-size:0.8rem; padding:0;'>
                                Ver detalles
                        </button>
                    `;
                    listaMini.appendChild(div);
                });
            } catch (e) { console.error(e); }
        }

        function verDetalleCita(c) {
            document.getElementById('det-servicio').innerText = c.nombre_especialidad;
            document.getElementById('det-fecha').innerText = c.fecha_cita;
            document.getElementById('det-hora').innerText = c.hora_cita;
            document.getElementById('det-doctor').innerText = c.nombre_doctor;
            document.getElementById('det-sucursal').innerText = c.nombre_sucursal;
            document.getElementById('det-motivo').innerText = c.motivo_cliente || "Sin motivo registrado.";
            document.getElementById('det-monto').innerText = c.monto_base_congelado;
            
            const badge = document.getElementById('det-estado-badge');
            badge.innerText = c.estatus_cita;
            badge.style.color = c.estatus_cita === 'Confirmada' ? '#27ae60' : '#e67e22';

            const btnCancel = document.getElementById('btn-cancelar-cita');
            const fechaCita = new Date(c.fecha_cita + 'T' + c.hora_cita);
            const ahora = new Date();
            const difHoras = (fechaCita - ahora) / (1000 * 60 * 60);

            if (difHoras < 24) {
                btnCancel.style.display = "none";
                document.getElementById('msg-restriccion').innerText = "Esta cita ya no puede cancelarse (menos de 24h).";
            } else {
                btnCancel.style.display = "block";
                btnCancel.onclick = () => cancelarCita(c.id_cita);
                document.getElementById('msg-restriccion').innerText = "* Puedes cancelar hasta 24h antes.";
            }

            document.getElementById('modalDetalleCita').style.display = "block";
        }

        async function cancelarCita(idCita) {
            if (!confirm("¿Estás seguro de que deseas cancelar esta cita?")) return;
            const formData = new FormData();
            formData.append('id_cita', idCita);
            try {
                const res = await fetch('cancelar_cita.php', { method: 'POST', body: formData });
                const r = await res.json();
                if (r.success) {
                    alert("Cita cancelada exitosamente.");
                    cerrarModalDetalle();
                    cargarCitasMascota();
                } else alert("Error: " + r.error);
            } catch (e) { console.error(e); }
        }

        // --- CERRAR MODALES ---
        function cerrarModal() { 
            document.getElementById('modalCita').style.display = "none"; 
            document.getElementById('formAgendar').reset();
            horaSeleccionada = null;
        }
        function cerrarModalDetalle() { document.getElementById('modalDetalleCita').style.display = "none"; }

        window.onclick = (event) => { 
            if (event.target == document.getElementById('modalCita')) cerrarModal(); 
            if (event.target == document.getElementById('modalDetalleCita')) cerrarModalDetalle(); 
        };

        window.onload = () => { if (mascotas.length > 0) actualizarDashboard(0); };
    </script>

    <script>
    async function cargarConsultasRecetas(idMascota) {
        const contenedor = document.getElementById('lista-consultas-recetas');
        if (!contenedor) return;

        try {
            const res = await fetch(`obtener_consultas.php?id_mascota=${idMascota}`);
            const datosRecibidos = await res.json();
            
            contenedor.innerHTML = "";

            if (datosRecibidos.error) {
                contenedor.innerHTML = "<p style='color:red; font-size:0.8rem;'>Error al obtener datos médicos.</p>";
                return;
            }

            if (!Array.isArray(datosRecibidos) || datosRecibidos.length === 0) {
                contenedor.innerHTML = "<p style='font-size: 0.8rem; color:#888; padding:10px;'>No hay historial de consultas disponible.</p>";
                return;
            }

            datosRecibidos.forEach(c => {
                let medsHTML = "";
                if (c.medicamentos && c.medicamentos.length > 0) {
                    medsHTML = `
                        <div style="margin-top:10px; padding:10px; background:#fdfaf8; border-radius:8px; border:1px solid #d4c8bf;">
                            <strong style="font-size:0.75rem; color:#5d4037;"><i class="fa-solid fa-pills"></i> RECETA:</strong>
                            <ul style="margin:5px 0 0 0; padding-left:18px; font-size:0.8rem; color:#333;">`;
                    c.medicamentos.forEach(m => {
                        medsHTML += `<li style="margin-bottom:4px;"><strong>${m.nombre_producto}</strong>: ${m.dosis_instrucciones}</li>`;
                    });
                    medsHTML += `</ul></div>`;
                }

                const card = document.createElement('div');
                card.className = "cita-card-mini";
                card.style.borderLeft = "4px solid #5d4037"; 
                card.innerHTML = `
                    <div style="border: 1px solid #d4c8bf; border-radius: 10px; overflow: hidden; margin-bottom: 20px;">
                        <div style="background: #5d4037; color: white; padding: 10px; display: flex; justify-content: space-between;">
                            <strong>RESUMEN DE CONSULTA</strong>
                            <span>${c.fecha_cita}</span>
                        </div>
                        
                        <div style="padding: 15px; background: white;">
                            <div style="display: flex; gap: 15px; margin-bottom: 15px; font-size: 0.8rem; background: #fdfaf8; padding: 10px; border-radius: 5px;">
                                <span>⚖️ <b>Peso:</b> ${c.peso || '--'} kg</span>
                                <span>🌡️ <b>Temp:</b> ${c.temperatura || '--'} °C</span>
                                <span>❤️ <b>F.C:</b> ${c.frecuencia_cardiaca || '--'} lpm</span>
                            </div>

                            <div style="font-size: 0.85rem;">
                                <p><strong>Sintomas:</strong> ${c.sintomas}</p>
                                <p><strong>Diagnóstico:</strong> ${c.diagnostico}</p>
                                <p><strong>Tratamiento:</strong> ${c.tratamiento_general || 'Ver receta adjunta'}</p>
                            </div>

                            ${medsHTML} 
                            ${c.proxima_cita ? `
                                <div style="margin-top: 10px; color: #e67e22; font-weight: bold; font-size: 0.8rem;">
                                    📅 PRÓXIMA CITA RECOMENDADA: ${c.proxima_cita}
                                </div>
                            ` : ''}
                        </div>
                        <div style="background: #f9f9f9; padding: 5px 15px; font-size: 0.7rem; color: #888; text-align: right;">
                            Médico Responsable: Dr. ${c.nombre_doctor}
                            
                        </div>
                        <div style="background: #f9f9f9; padding: 10px 15px; font-size: 0.7rem; color: #888; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee;">
    <span>Médico Responsable: Dr. ${c.nombre_doctor}</span>
    
    <a href="generar_receta.php?id_cita=${c.fk_id_cita}" 
       target="_blank" 
       style="background: #e74c3c; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 0.75rem; display: flex; align-items: center; gap: 5px; transition: 0.3s;">
       <i class="fa-solid fa-file-pdf"></i> DESCARGAR RECETA
    </a>
</div>
                    </div>
                    
                `;
                contenedor.appendChild(card);
                
            });
        } catch (e) {
            console.error("Error fatal:", e);
            contenedor.innerHTML = "<p style='color:red;'>Error de conexión con el historial.</p>";
        }
    }
    </script>
</body>
</html>