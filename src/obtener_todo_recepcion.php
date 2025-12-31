<?php
// obtener_todo_recepcion.php
error_reporting(E_ALL);
ini_set('display_errors', 0); 

header('Content-Type: application/json');
require_once '../includes/db_connection.php'; 

$accion = $_REQUEST['accion'] ?? '';
$busqueda = $_GET['q'] ?? '';

try {
    if ($accion === 'buscar_mascotas') {
        $sql = "SELECT m.idmascota, m.nombre as mascota, m.tipo_mascota, 
                       du.nombre as dueno, du.apellido1 as apellido, du.telefono_principal as telefono
                FROM public.mascotas m
                JOIN public.usuarios u ON m.fk_id_dueno = u.idusuario
                JOIN public.datosusuario du ON u.idusuario = du.fk_id_usuario
                WHERE m.nombre ILIKE :q 
                   OR du.nombre ILIKE :q 
                   OR du.apellido1 ILIKE :q
                LIMIT 15";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['q' => "%$busqueda%"]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
        exit;
    }
    
    if ($accion === 'listar_citas') {
        $sql = "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.estatus_cita,
                       m.nombre as mascota, 
                       du.nombre || ' ' || du.apellido1 as dueno,
                       d.nombre_doctor
                FROM public.citas c
                JOIN public.mascotas m ON c.fk_id_mascota = m.idmascota
                JOIN public.datosusuario du ON m.fk_id_dueno = du.fk_id_usuario
                JOIN public.doctor_asignacion da ON c.fk_id_asignacion = da.id_asignacion
                JOIN public.doctores d ON da.fk_id_doctor = d.id_doctor
                ORDER BY c.fecha_cita DESC, c.hora_cita DESC LIMIT 30";
                
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
        exit;
    }

    if ($accion === 'detalle_cita') {
        $id = $_GET['id'] ?? 0;
        $sql = "SELECT c.*, 
                       m.nombre as mascota, m.tipo_mascota,
                       du.nombre || ' ' || du.apellido1 as dueno,
                       d.nombre_doctor, 
                       e.nombre_especialidad,
                       e.precio_base
                FROM public.citas c
                LEFT JOIN public.mascotas m ON c.fk_id_mascota = m.idmascota
                LEFT JOIN public.datosusuario du ON m.fk_id_dueno = du.fk_id_usuario
                LEFT JOIN public.doctor_asignacion da ON c.fk_id_asignacion = da.id_asignacion
                LEFT JOIN public.doctores d ON da.fk_id_doctor = d.id_doctor
                LEFT JOIN public.especialidades e ON da.fk_id_especialidad = e.id_especialidad
                WHERE c.id_cita = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: ["error" => "Cita no encontrada"]);
        exit;
    }

    if ($accion === 'listar_doctores') {
        $sql = "SELECT da.id_asignacion, d.nombre_doctor, e.nombre_especialidad, e.precio_base
                FROM public.doctor_asignacion da
                JOIN public.doctores d ON da.fk_id_doctor = d.id_doctor
                JOIN public.especialidades e ON da.fk_id_especialidad = e.id_especialidad
                ORDER BY d.nombre_doctor ASC";
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []);
        exit;
    }

    // --- ACCIÓN DE GUARDADO CON VALIDACIÓN DE DISPONIBILIDAD ---
    if ($accion === 'guardar_cita') {
        try {
            $id_asig = $_POST['fk_id_asignacion'];
            $fecha   = $_POST['fecha_cita'];
            $hora    = $_POST['hora_cita'];

            // 1. Verificar si hay una cita ACTIVA (no finalizada ni cancelada) en ese horario
            $sql_val = "SELECT COUNT(*) FROM public.citas 
                        WHERE fk_id_asignacion = :asig 
                        AND fecha_cita = :fecha 
                        AND hora_cita = :hora 
                        AND estatus_cita NOT IN ('Finalizada', 'Cancelada')";
            
            $stmt_val = $pdo->prepare($sql_val);
            $stmt_val->execute([
                'asig'  => $id_asig,
                'fecha' => $fecha,
                'hora'  => $hora
            ]);

            if ($stmt_val->fetchColumn() > 0) {
                echo json_encode([
                    "success" => false, 
                    "error" => "El médico ya tiene una cita agendada en este horario. Elija otra hora."
                ]);
                exit;
            }

            // 2. Si el horario está libre (o la cita anterior ya terminó), procedemos al INSERT
            $sql = "INSERT INTO public.citas 
                    (fk_id_mascota, fk_id_asignacion, fecha_cita, hora_cita, motivo_cliente, monto_base_congelado, estatus_cita) 
                    VALUES 
                    (:fk_id_mascota, :fk_id_asignacion, :fecha_cita, :hora_cita, :motivo_cliente, :monto, 'Agendada')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'fk_id_mascota' => $_POST['fk_id_mascota'],
                'fk_id_asignacion' => $id_asig,
                'fecha_cita' => $fecha,
                'hora_cita' => $hora,
                'motivo_cliente' => $_POST['motivo_cliente'],
                'monto' => $_POST['monto_base_congelado']
            ]);
            
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            // Si el error es por el UNIQUE de la DB (por si acaso), enviamos mensaje amigable
            if (strpos($e->getMessage(), 'cita_unica_bloque') !== false) {
                echo json_encode(["success" => false, "error" => "El horario seleccionado ya no está disponible."]);
            } else {
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
            }
        }
        exit;
    }

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}