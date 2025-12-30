<?php
session_start();

// 1. Verificamos sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../es/registro_mascota.html"); 
    exit();
}

// 2. Variables de sesión del Usuario (Dueño)
$nombreUsuario = $_SESSION['nombre'];
$apellidoUsuario = $_SESSION['apellido'];
$emailUsuario = $_SESSION['email'];
$telefonoUsuario = $_SESSION['telefono_principal'];
$direccionUsuario = $_SESSION['direccion'] ?? 'Dirección no disponible';

// 3. Preparar datos de mascotas para JavaScript
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
        .selector-mascota {
            padding: 12px;
            border-radius: 10px;
            border: 2px solid #5d4037;
            margin-bottom: 20px;
            width: 100%;
            font-size: 16px;
            background-color: white;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        /* Ajuste para que los detalles no se vean amontonados */
        .detalle-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #d4c8bf;
            padding-bottom: 5px;
        }
        .detalle-label {
            font-weight: bold;
            color: #5d4037;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .detalle-valor {
            color: #333;
            text-align: right;
            font-size: 0.9rem;
        }
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
            <div class="goku">
                <i class="fas fa-search"></i>
                <input type="text" value="https://clinica-veterinaria.com/dashboard" readonly>
            </div>
        </div>

        <div style="padding: 20px 20px 0 20px;">
            <label style="font-weight: bold; display:block; margin-bottom: 5px;">Mascota en pantalla:</label>
            <select id="selectorMascota" class="selector-mascota" onchange="actualizarDashboard(this.value)">
                <?php if (count($mis_mascotas) > 0): ?>
                    <?php foreach ($mis_mascotas as $index => $m): ?>
                        <option value="<?php echo $index; ?>">
                            <?php echo htmlspecialchars($m['nombre']); ?> (<?php echo htmlspecialchars($m['tipo_mascota']); ?>)
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No tienes mascotas registradas</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="emilianiiiii">
            <main class="tralalero-tralala">
                
                <div class="sape card">
                    <div class="sape2">
                        <img src="" class="tun-tun" id="profile-image" alt="Mascota">
                        <div class="sahur">
                            <h2 id="display-nombre-mascota">Nombre</h2>
                            <p id="display-especie-top">Especie</p>
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
                    <div class="card trunk" id="contenedor-detalles">
                        <h4><i class="fa-solid fa-dna"></i> Detalles Clínicos</h4>
                        <div id="lista-especifica">
                            <p>Selecciona una mascota para ver su información detallada.</p>
                        </div>
                    </div>

                    <div class="card cabezoni">
                        <h4><i class="fa-solid fa-syringe"></i> Vacunas y Estatus</h4>
                        <ul id="lista-vacunas">
                            <li>Registro oficial en proceso de validación.</li>
                            <li>Última revisión: <?php echo date('d/m/Y'); ?></li>
                        </ul>
                    </div>
                </div>
            </main>

            <aside class="jbalvin">
                <div class="cahcitrula card">
                    <h3><i class="fa-solid fa-calendar-check"></i> Agendar Consulta</h3>
                    <p>Disponible para <span id="nombre-agendar">...</span></p>
                </div>
                
                <div class="card" style="text-align: center;">
                    <a href="logout.php" style="color: #ff5f56; text-decoration: none; font-weight: bold;">
                        <i class="fa-solid fa-power-off"></i> Cerrar Sesión
                    </a>
                </div>
            </aside>

            <div class="page-number">12</div>
        </div>
    </div>

    <script>
        const mascotas = <?php echo $mascotas_js; ?>;

       async function actualizarDashboard(index) {
    if (mascotas.length === 0) return;
    const m = mascotas[index];

    // 1. Datos Básicos y Fotos
    document.getElementById('display-nombre-mascota').innerText = m.nombre;
    document.getElementById('display-especie-top').innerText = m.tipo_mascota;
    document.getElementById('nombre-agendar').innerText = m.nombre;

    const carpetaMap = { 
        'Perro': 'perro.jfif', 'Gato': 'gato.jfif', 'Ave': 'ave.jfif', 
        'Lagarto': 'lagarto.webp', 'Serpiente': 'serpiente.jfif', 'Tortuga': 'tortuga.jfif' 
    };
    document.getElementById('profile-image').src = 'uploads/' + (carpetaMap[m.tipo_mascota] || 'enproceso.jpg');

    // 2. Carga de Detalles
    const listaDiv = document.getElementById('lista-especifica');
    listaDiv.innerHTML = '<p><i class="fas fa-spinner fa-spin"></i> Cargando detalles...</p>';

    try {
        const response = await fetch(`obtener_detalles.php?id=${m.idmascota}&tipo=${m.tipo_mascota}`);
        const textoSucio = await response.text();
        
        // Limpiamos cualquier mensaje de "Conexión exitosa" que se cuele
        const jsonInicio = textoSucio.indexOf('{');
        const jsonLimpio = textoSucio.substring(jsonInicio);
        const detalles = JSON.parse(jsonLimpio);

        listaDiv.innerHTML = ""; 

        if (detalles && Object.keys(detalles).length > 0) {
            for (const [key, value] of Object.entries(detalles)) {
                // Solo mostramos si tiene valor y no son IDs
                if (value && value !== "" && !key.includes('id_mascota')) {
                    
                    let label = key.replace(/_/g, ' ')
                                   .replace(/(perro|gato|ave|lagarto|serpiente|tortuga)/gi, '')
                                   .trim().toUpperCase();

                    listaDiv.innerHTML += `
                        <div class="detalle-item" style="display:flex; justify-content:space-between; border-bottom:1px solid #d4c8bf; margin-bottom:8px;">
                            <span class="detalle-label" style="font-weight:bold; font-size:0.8rem;">${label}:</span>
                            <span class="detalle-valor" style="color:#555;">${value}</span>
                        </div>`;
                }
            }
        } else {
            listaDiv.innerHTML = "<p>No hay detalles registrados para esta mascota.</p>";
        }
    } catch (error) {
        console.error("Error:", error);
        listaDiv.innerHTML = "<p>Error al procesar los datos clínicos.</p>";
    }
}

        // Carga inicial
        window.onload = () => {
            if (mascotas.length > 0) {
                actualizarDashboard(0);
            }
        };
    </script>
</body>
</html>