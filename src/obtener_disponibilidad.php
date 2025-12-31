<?php
require_once '../includes/db_connection.php';

// Capturamos variables
$id_especialidad = isset($_GET['id_especialidad']) ? $_GET['id_especialidad'] : null;
$fecha_elegida = isset($_GET['fecha']) ? $_GET['fecha'] : null;

if (!$id_especialidad) {
    echo json_encode(['error' => 'No se recibió la especialidad.']);
    exit;
}

try {
    // 1. Buscamos TODOS los horarios para esa especialidad (sin importar la fecha aún)
    $query = "SELECT hd.dia_semana, hd.hora_entrada, hd.hora_salida, 
                     s.nombre_sucursal, e.precio_base, e.duracion_minutos,
                     d.nombre_doctor, da.id_asignacion
              FROM public.doctor_asignacion da
              JOIN public.especialidades e ON da.fk_id_especialidad = e.id_especialidad
              JOIN public.horarios_doctor hd ON hd.fk_id_asignacion = da.id_asignacion
              JOIN public.sucursales s ON da.fk_id_sucursal = s.id_sucursal
              JOIN public.doctores d ON da.fk_id_doctor = d.id_doctor
              WHERE da.fk_id_especialidad = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id_especialidad]);
    $horariosDisponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$horariosDisponibles) {
        echo json_encode(['error' => 'Esta especialidad no tiene horarios asignados en la base de datos.']);
        exit;
    }

    // Traducir el día de la fecha seleccionada a Español (porque tu tabla probablemente está en español)
    $dias_traduccion = [
        'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado', 'Sunday' => 'Domingo'
    ];
    
    $bloques = [];
    $infoFinal = null;

    if ($fecha_elegida) {
        $nombre_dia_en = date('l', strtotime($fecha_elegida)); // Ej: Monday
        $nombre_dia_es = $dias_traduccion[$nombre_dia_en];    // Ej: Lunes

        foreach ($horariosDisponibles as $h) {
            // Comparamos ignorando mayúsculas/minúsculas y espacios
            if (trim(strtolower($h['dia_semana'])) == trim(strtolower($nombre_dia_es)) || 
                trim(strtolower($h['dia_semana'])) == trim(strtolower($nombre_dia_en))) {
                
                $infoFinal = $h;
                
                // Buscar citas ya ocupadas
               $qCitas = "SELECT hora_cita FROM public.citas 
           WHERE fk_id_asignacion = :id 
           AND fecha_cita = :f 
           AND estatus_cita NOT IN ('Cancelada', 'Finalizada')";
                $stmtCitas = $pdo->prepare($qCitas);
                $stmtCitas->execute(['id' => $h['id_asignacion'], 'f' => $fecha_elegida]);
                $ocupados = $stmtCitas->fetchAll(PDO::FETCH_COLUMN);

                // Generar los bloques
                $inicio = strtotime($h['hora_entrada']);
                $fin = strtotime($h['hora_salida']);
                $duracion = ($h['duracion_minutos'] > 0 ? $h['duracion_minutos'] : 30) * 60;

                for ($i = $inicio; $i < $fin; $i += $duracion) {
                    $hora = date("H:i", $i);
                    $bloques[] = [
                        'hora' => $hora,
                        'estado' => in_array($hora.":00", $ocupados) ? 'ocupado' : 'disponible'
                    ];
                }
                break;
            }
        }
    }

    // Extraer qué días atiende el doctor para avisar al usuario
    $dias_que_atiende = array_unique(array_column($horariosDisponibles, 'dia_semana'));

  // Al final de obtener_disponibilidad.php, en el echo json_encode:
// Al final de obtener_disponibilidad.php
echo json_encode([
    'dias_permitidos' => $dias_que_atiende, // Asegúrate que sea un array ['Lunes', 'Martes']
    'precio' => $horariosDisponibles[0]['precio_base'],
    'sucursal' => $horariosDisponibles[0]['nombre_sucursal'],
    'doctor' => $horariosDisponibles[0]['nombre_doctor'],
    'id_asignacion' => $infoFinal ? $infoFinal['id_asignacion'] : null,
    'bloques' => $bloques
]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de BD: ' . $e->getMessage()]);
}