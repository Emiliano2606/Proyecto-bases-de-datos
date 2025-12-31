<?php
error_reporting(0); 
header('Content-Type: application/json');
require_once '../includes/db_connection.php';

$id_mascota = $_GET['id_mascota'] ?? null;

if (!$id_mascota) {
    echo json_encode([]);
    exit;
}

try {
    // Agregamos los nuevos campos a la consulta SQL
 // Cambia tu línea de SELECT por esta:
$sql = "SELECT c.id_consulta, c.fk_id_cita, c.sintomas, c.prediagnostico, c.diagnostico, -- Agregamos fk_id_cita
               c.tratamiento_general, c.peso, c.temperatura, 
               c.frecuencia_cardiaca, c.frecuencia_respiratoria, c.proxima_cita,
               d.nombre_doctor, ci.fecha_cita
        FROM public.consultas_medicas c
        JOIN public.citas ci ON c.fk_id_cita = ci.id_cita
        JOIN public.doctores d ON c.fk_id_doctor = d.id_doctor
        WHERE c.fk_id_mascota = :id
        ORDER BY ci.fecha_cita DESC";



    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_mascota]);
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($consultas as &$con) {
        $sqlM = "SELECT p.nombre_producto, r.dosis_instrucciones 
                 FROM public.recetas_medicamentos r
                 JOIN public.producto p ON r.fk_id_producto = p.id_producto
                 WHERE r.fk_id_consulta = :id_c";
        $stmtM = $pdo->prepare($sqlM);
        $stmtM->execute(['id_c' => $con['id_consulta']]);
        $con['medicamentos'] = $stmtM->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($consultas);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}