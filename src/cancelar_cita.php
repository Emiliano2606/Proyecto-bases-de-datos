<?php
require_once '../includes/db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'Sesión no válida']);
    exit;
}

$id_cita = $_POST['id_cita'] ?? null;

if (!$id_cita) {
    echo json_encode(['success' => false, 'error' => 'ID de cita no proporcionado']);
    exit;
}

try {
    // 1. Consultar la fecha y hora de la cita
    $stmt = $pdo->prepare("SELECT fecha_cita, hora_cita FROM public.citas WHERE id_cita = :id");
    $stmt->execute(['id' => $id_cita]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        echo json_encode(['success' => false, 'error' => 'Cita no encontrada']);
        exit;
    }

    // 2. Validar 24 horas (Comparar fecha actual vs fecha cita)
    $fechaCita = new DateTime($cita['fecha_cita'] . ' ' . $cita['hora_cita']);
    $ahora = new DateTime();
    $diferencia = $ahora->diff($fechaCita);
    
    // Si la cita es en el pasado o falta menos de 1 día (24h)
    if ($fechaCita <= $ahora || ($diferencia->days == 0 && $diferencia->invert == 0)) {
        echo json_encode(['success' => false, 'error' => 'Solo puedes cancelar con un mínimo de 24 horas de antelación.']);
        exit;
    }

    // 3. Eliminar la cita (Esto libera el bloque de tiempo automáticamente)
    $stmtDel = $pdo->prepare("DELETE FROM public.citas WHERE id_cita = :id");
    $stmtDel->execute(['id' => $id_cita]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}