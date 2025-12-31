<?php
require_once '../includes/db_connection.php';
header('Content-Type: application/json');

$id_mascota = $_GET['id_mascota'] ?? null;

if (!$id_mascota) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT c.*, e.nombre_especialidad, d.nombre_doctor, s.nombre_sucursal 
            FROM public.citas c
            JOIN public.doctor_asignacion da ON c.fk_id_asignacion = da.id_asignacion
            JOIN public.especialidades e ON da.fk_id_especialidad = e.id_especialidad
            JOIN public.doctores d ON da.fk_id_doctor = d.id_doctor
            JOIN public.sucursales s ON da.fk_id_sucursal = s.id_sucursal
            WHERE c.fk_id_mascota = :id
            ORDER BY c.fecha_cita DESC, c.hora_cita DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_mascota]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($citas);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}