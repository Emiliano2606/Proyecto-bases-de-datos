<?php
session_start();
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id_mascota = $_POST['id_mascota'];
        $id_asignacion = $_POST['id_asignacion'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $motivo = $_POST['motivo'];
        $monto = $_POST['monto'];

        // Validar que no falten datos
        if (!$id_mascota || !$id_asignacion || !$fecha || !$hora) {
            echo json_encode(['success' => false, 'error' => 'Faltan datos para agendar la cita.']);
            exit;
        }

        $sql = "INSERT INTO public.citas (fk_id_mascota, fk_id_asignacion, fecha_cita, hora_cita, motivo_cliente, monto_base_congelado, estatus_cita) 
                VALUES (:mascota, :asig, :fecha, :hora, :motivo, :monto, 'Programada')";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'mascota' => $id_mascota,
            'asig'    => $id_asignacion,
            'fecha'   => $fecha,
            'hora'    => $hora,
            'motivo'  => $motivo,
            'monto'   => $monto
        ]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo insertar la cita.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>