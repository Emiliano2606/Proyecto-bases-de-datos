<?php
session_start();
require_once '../includes/db_connection.php';

// Verificación de sesión
if (!isset($_SESSION['id_doctor']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: login_doctor.html");
    exit();
}

$id_cita = $_GET['id_cita'] ?? null;
if (!$id_cita) die("ID de cita no proporcionado.");

try {
    // 1. Obtener datos de la cita, mascota y dueño
    $sqlCita = "SELECT c.*, m.*, du.*, u.email 
                FROM public.citas c
                JOIN public.mascotas m ON c.fk_id_mascota = m.idmascota
                JOIN public.usuarios u ON m.fk_id_dueno = u.idusuario
                JOIN public.datosusuario du ON u.idusuario = du.fk_id_usuario
                WHERE c.id_cita = :id_cita";
    $stmt = $pdo->prepare($sqlCita);
    $stmt->execute(['id_cita' => $id_cita]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) die("Cita no encontrada.");

    // 2. Mapeo de Especie Individual para el filtro de medicamentos
    $tipo_texto = strtolower($cita['tipo_mascota']);
    $id_especie_busqueda = 17; // Por defecto "Todas las especies"

    if (strpos($tipo_texto, 'perro') !== false) $id_especie_busqueda = 5;
    elseif (strpos($tipo_texto, 'gato') !== false) $id_especie_busqueda = 6;
    elseif (strpos($tipo_texto, 'ave') !== false) $id_especie_busqueda = 150;
    elseif (strpos($tipo_texto, 'tortuga') !== false || strpos($tipo_texto, 'serpiente') !== false || strpos($tipo_texto, 'lagarto') !== false) {
        $id_especie_busqueda = 9; // Peces/Reptiles según tu lógica
    }

    // 3. CONSULTA DE MEDICAMENTOS FILTRADOS POR ESPECIE
    $sqlMed = "SELECT DISTINCT p.id_producto, p.nombre_producto 
               FROM public.producto p
               JOIN public.medicamento m ON p.id_producto = m.fk_producto
               JOIN public.medicamento_especie me ON m.id_medicamento = me.fk_medicamento
               WHERE me.fk_especie_individual = :esp OR me.fk_especie_individual = 17
               ORDER BY p.nombre_producto ASC";
    
    $stmtMed = $pdo->prepare($sqlMed);
    $stmtMed->execute(['esp' => $id_especie_busqueda]);
    $medicamentos = $stmtMed->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error en el servidor: " . $e->getMessage());
}

$tipo_mascota_db = $cita['tipo_mascota']; 
    $especie_para_vacuna = 'Todas'; 

    $tipo_check = strtolower($tipo_mascota_db);

    if (strpos($tipo_check, 'perro') !== false) {
        $especie_para_vacuna = 'Perro';
    } elseif (strpos($tipo_check, 'gato') !== false) {
        $especie_para_vacuna = 'Gato';
    } elseif (strpos($tipo_check, 'ave') !== false) {
        $especie_para_vacuna = 'Ave';
    } elseif (strpos($tipo_check, 'reptil') !== false) {
        $especie_para_vacuna = 'Reptil';
    } elseif (strpos($tipo_check, 'serpiente') !== false) {
        $especie_para_vacuna = 'Serpiente';
    } elseif (strpos($tipo_check, 'tortuga') !== false) {
        $especie_para_vacuna = 'Tortuga';
    }

    $sqlCatVac = "SELECT id_vacuna, nombre_vacuna 
                  FROM public.catalogo_vacunas 
                  WHERE especie = :esp OR especie = 'Todas'
                  ORDER BY nombre_vacuna ASC";

    $stmtVac = $pdo->prepare($sqlCatVac);
    $stmtVac->execute(['esp' => $especie_para_vacuna]);
    $catalogo_vacunas = $stmtVac->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Atendiendo a <?php echo htmlspecialchars($cita['nombre']); ?></title>
    <link rel="stylesheet" href="../es/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .grid-atencion { display: grid; grid-template-columns: 350px 1fr; gap: 20px; padding: 20px; }
        .sidebar-expediente { background: #f4ece6; padding: 20px; border-radius: 15px; border: 1px solid #d4c8bf; height: fit-content; }
        .area-consulta { background: white; padding: 25px; border-radius: 15px; border: 1px solid #d4c8bf; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .detalle-item { display: flex; justify-content: space-between; border-bottom: 1px solid #d4c8bf; padding: 8px 0; font-size: 0.85rem; }
        .detalle-label { font-weight: bold; color: #5d4037; text-transform: uppercase; }
        .input-consulta { width: 100%; padding: 12px; margin-top: 8px; border: 1px solid #d4c8bf; border-radius: 8px; margin-bottom: 15px; }
        .btn-finalizar { background: #27ae60; color: white; border: none; padding: 15px; border-radius: 8px; width: 100%; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 20px; }
        .btn-finalizar:hover { background: #219150; }
        .fila-med { display: grid; grid-template-columns: 1fr 1fr 50px; gap: 10px; margin-bottom: 15px; background: #fdfaf8; padding: 15px; border-radius: 8px; border: 1px solid #d4c8bf; }
    </style>
</head>
<body style="background: #fdfaf8;">

<div style="max-width: 1300px; margin: auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #5d4037;"><i class="fa-solid fa-stethoscope"></i> Consulta en Progreso</h1>
        <a href="dashboard_doctor.php" style="color: #5d4037; text-decoration: none; font-weight: bold;">← Regresar al Panel</a>
    </div>

    <div class="grid-atencion">
        <aside class="sidebar-expediente">
            <center>
                <?php 
                    $carpetaMap = ['Perro' => 'perro.jfif', 'Gato' => 'gato.jfif', 'Ave' => 'ave.jfif', 'Lagarto' => 'lagarto.webp', 'Serpiente' => 'serpiente.jfif', 'Tortuga' => 'tortuga.jfif'];
                    $foto = $carpetaMap[$cita['tipo_mascota']] ?? 'enproceso.jpg';
                ?>
                <img src="uploads/<?php echo $foto; ?>" style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid #5d4037; object-fit: cover;">
                <h2 style="margin: 10px 0;"><?php echo htmlspecialchars($cita['nombre']); ?></h2>
                <span style="background: #5d4037; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem;">
                    <?php echo htmlspecialchars($cita['tipo_mascota']); ?>
                </span>
            </center>

            <h4 style="border-bottom: 2px solid #5d4037; margin-top: 25px; padding-bottom: 5px;">Tutor</h4>
            <p style="font-size: 0.9rem;"><strong>Nombre:</strong> <?php echo htmlspecialchars($cita['nombre'] . " " . $cita['apellido1']); ?></p>
            <p style="font-size: 0.9rem;"><strong>Tel:</strong> <?php echo htmlspecialchars($cita['telefono_principal']); ?></p>
            
            <h4 style="border-bottom: 2px solid #5d4037; margin-top: 25px; padding-bottom: 5px;">Detalles Clínicos</h4>
            <div id="datos-especificos">
                <p style="text-align: center; color: #888;">Cargando detalles...</p>
            </div>

            <h4 style="border-bottom: 2px solid #5d4037; margin-top: 25px; padding-bottom: 5px;">
                <i class="fa-solid fa-syringe"></i> Carnet Vacunación
            </h4>
            <div id="lista-vacunas-doctor">
                <p style="text-align: center; color: #888;">Cargando vacunas...</p>
            </div>
        </aside>

        <main class="area-consulta">
            <div style="background: #fff5e6; padding: 15px; border-radius: 10px; border-left: 5px solid #ff9800; margin-bottom: 25px;">
                <strong>Motivo reportado por el cliente:</strong><br>
                <?php echo htmlspecialchars($cita['motivo_cliente']); ?>
            </div>

            <form action="guardar_consulta.php" method="POST">
                <input type="hidden" name="id_cita" value="<?php echo $id_cita; ?>">
                <input type="hidden" name="id_mascota" value="<?php echo $cita['idmascota']; ?>">

                <h3><i class="fa-solid fa-file-medical"></i> Registro Clínico</h3>
          <div>
                <br>

        <label>Peso (kg):</label>
        <input type="number" step="0.01" name="peso" class="form-control" required>
    </div>
    <div>
        <label>Temperatura (°C):</label>
        <input type="number" step="0.1" name="temperatura" class="form-control">
    </div>
    <div>
        <label>Frecuencia Cardíaca (LPM):</label>
        <input type="number" name="frecuencia_cardiaca" class="form-control">
    </div>

        <div>
        <label>Frecuencia Respiratoria:</label>
        <input type="number" name="frecuencia_respiratoria" class="form-control">
    </div>
 
    
    <br>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label class="detalle-label">Signos y Síntomas:</label>
                        <textarea name="sintomas" class="input-consulta" style="height: 100px;" placeholder="Describa lo observado..." required></textarea>
                    </div>
                    
                    <div>
                        <label class="detalle-label">Prediagnóstico:</label>
                        <textarea name="prediagnostico" class="input-consulta" style="height: 100px;" placeholder="Impresión inicial..."></textarea>
                    </div>
                </div>

                <label class="detalle-label">Diagnóstico Final:</label>
                <textarea name="diagnostico" class="input-consulta" style="height: 80px;" required></textarea>

                <div style="margin-top: 25px; border-top: 2px solid #5d4037; padding-top: 20px;">
                    <h3 style="color: #5d4037;"><i class="fa-solid fa-pills"></i> Receta Médica (Filtrada para <?php echo $cita['tipo_mascota']; ?>)</h3>
                    
                    <div id="contenedor-receta">
                        <div class="fila-med">
                            <div>
                                <label class="detalle-label">Medicamento:</label>
                                <select name="id_medicamento[]" class="input-consulta" style="margin-bottom: 0;">
                                    <option value="">-- Seleccionar --</option>
                                    <?php foreach ($medicamentos as $med): ?>
                                        <option value="<?php echo $med['id_producto']; ?>">
                                            <?php echo htmlspecialchars($med['nombre_producto']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="detalle-label">Dosis e Instrucciones:</label>
                                <input type="text" name="dosis[]" class="input-consulta" placeholder="Ej: 1 tableta c/12h" style="margin-bottom: 0;">
                            </div>
                            <div style="display: flex; align-items: flex-end;">
                                <button type="button" onclick="eliminarFila(this)" style="background: #e74c3c; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
<div>
        <label>Proxima Cita</label>
        <input type="date" name="proxima_cita" class="form-control">
    </div>
                    <button type="button" id="btn-agregar" style="background: #5d4037; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 10px;">
                        <i class="fa-solid fa-plus"></i> Agregar otro medicamento
                    </button>
                </div>
<div style="margin-top: 25px; border-top: 2px solid #5d4037; padding-top: 20px;">
    <h3 style="color: #5d4037;"><i class="fa-solid fa-syringe"></i> Aplicación de Vacunas (Hoy)</h3>
    <p style="font-size: 0.8rem; color: #7f8c8d; margin-bottom: 10px;">Seleccione las vacunas aplicadas en esta sesión para que se sumen al historial.</p>
    
<div style="margin-top: 25px; border-top: 2px solid #5d4037; padding-top: 20px;">
    <h3 style="color: #5d4037;"><i class="fa-solid fa-syringe"></i> Aplicación de Vacunas (Hoy)</h3>
    
    <div id="contenedor-vacunas-dinamico">
        <div class="fila-vacuna" style="display: grid; grid-template-columns: 1fr 1fr 50px; gap: 10px; margin-bottom: 15px; background: #f0f9ff; padding: 15px; border-radius: 8px; border: 1px solid #bae6fd;">
            <div>
                <label class="detalle-label">Vacuna a aplicar:</label>
                <select name="id_vacuna[]" class="input-consulta" style="margin-bottom: 0;">
                    <option value="">-- Seleccionar Vacuna --</option>
                    <?php foreach ($catalogo_vacunas as $v): ?>
                        <option value="<?php echo $v['id_vacuna']; ?>">
                            <?php echo htmlspecialchars($v['nombre_vacuna']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="detalle-label">Fecha:</label>
                <input type="date" name="fecha_vacuna[]" class="input-consulta" value="<?php echo date('Y-m-d'); ?>" style="margin-bottom: 0;">
            </div>
            <div style="display: flex; align-items: flex-end;">
                <button type="button" onclick="eliminarFilaVacuna(this)" style="background: #e74c3c; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%;">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    </div>

    <button type="button" id="btn-nueva-vacuna" style="background: #0369a1; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; font-weight: bold;">
        <i class="fa-solid fa-plus"></i> Aplicar otra vacuna
    </button>
</div>


</div>
                <button type="submit" class="btn-finalizar">
                    <i class="fa-solid fa-check-double"></i> Finalizar Consulta y Guardar Historial
                </button>
            </form>
        </main>
    </div>
</div>

<script>
// 1. CARGA DE DETALLES Y VACUNAS (AJAX)
async function cargarInformacionMascota() {
    const idMascota = "<?php echo $cita['idmascota']; ?>";
    const tipoMascota = "<?php echo $cita['tipo_mascota']; ?>";
    const contenedorDetalles = document.getElementById('datos-especificos');
    const contenedorVacunas = document.getElementById('lista-vacunas-doctor');

    try {
        const response = await fetch(`obtener_detalles.php?id=${idMascota}&tipo=${tipoMascota}`);
        const data = await response.json();

        // Renderizar Detalles
        contenedorDetalles.innerHTML = "";
        if (data.detalles && Object.keys(data.detalles).length > 0) {
            for (const [key, value] of Object.entries(data.detalles)) {
                if (value && value !== "" && !key.includes('id')) {
                    let label = key.replace(/_/g, ' ').toUpperCase();
                    contenedorDetalles.innerHTML += `
                        <div class="detalle-item">
                            <span class="detalle-label">${label}:</span>
                            <span class="detalle-valor">${value}</span>
                        </div>`;
                }
            }
        } else {
            contenedorDetalles.innerHTML = "<p>Sin detalles específicos.</p>";
        }

        // Renderizar Vacunas
        contenedorVacunas.innerHTML = "";
        if (data.vacunas && data.vacunas.length > 0) {
            data.vacunas.forEach(v => {
                contenedorVacunas.innerHTML += `
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #d4c8bf; padding: 8px 0;">
                        <strong style="color: #5d4037;">${v.nombre_vacuna}</strong>
                        <span style="font-size: 0.8rem; color: #777;">${v.fecha_aplicacion}</span>
                    </div>`;
            });
        } else {
            contenedorVacunas.innerHTML = "<p style='color: #888; padding: 10px 0;'>No hay vacunas registradas.</p>";
        }

    } catch (e) {
        console.error("Error al cargar datos:", e);
        contenedorDetalles.innerHTML = "<p>Error al cargar.</p>";
    }
}

// 2. FUNCIÓN PARA AGREGAR FILAS DE MEDICAMENTOS
document.getElementById('btn-agregar').addEventListener('click', function() {
    const contenedor = document.getElementById('contenedor-receta');
    const filas = document.querySelectorAll('.fila-med');
    
    if (filas.length > 0) {
        const nuevaFila = filas[0].cloneNode(true);
        // Limpiar inputs
        nuevaFila.querySelector('select').value = "";
        nuevaFila.querySelector('input').value = "";
        contenedor.appendChild(nuevaFila);
    }
});

// 3. FUNCIÓN PARA ELIMINAR FILA
function eliminarFila(btn) {
    const filas = document.querySelectorAll('.fila-med');
    if (filas.length > 1) {
        btn.closest('.fila-med').remove();
    } else {
        alert("Debe haber al menos un espacio para medicamento.");
    }
}

// Ejecutar al cargar
window.onload = cargarInformacionMascota;
</script>
<script>
    // Agregar nueva fila de vacuna
document.getElementById('btn-nueva-vacuna').addEventListener('click', function() {
    const contenedor = document.getElementById('contenedor-vacunas-dinamico');
    const filas = document.querySelectorAll('.fila-vacuna');
    
    if (filas.length > 0) {
        const nuevaFila = filas[0].cloneNode(true);
        // Limpiar la selección previa
        nuevaFila.querySelector('select').value = "";
        // Asegurar que la fecha sea la de hoy
        nuevaFila.querySelector('input[type="date"]').value = "<?php echo date('Y-m-d'); ?>";
        contenedor.appendChild(nuevaFila);
    }
});

// Eliminar fila de vacuna
function eliminarFilaVacuna(btn) {
    const filas = document.querySelectorAll('.fila-vacuna');
    if (filas.length > 1) {
        btn.closest('.fila-vacuna').remove();
    } else {
        // Si solo queda una, solo la vaciamos
        btn.closest('.fila-vacuna').querySelector('select').value = "";
    }
}
</script>
</body>
</html>