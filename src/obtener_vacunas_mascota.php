<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once '../includes/db_connection.php'; 

$id_mascota = $_GET['id_mascota'] ?? null;

if (!$id_mascota) {
    echo json_encode(["error" => "ID de mascota no proporcionado"]);
    exit;
}

try {
    
    $sql = "SELECT h.fecha_aplicacion, v.nombre_vacuna 
            FROM public.historial_vacunacion h
            JOIN public.catalogo_vacunas v ON h.fk_id_vacuna = v.id_vacuna
            WHERE h.fk_id_mascota = :id
            ORDER BY h.fecha_aplicacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_mascota]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados ? $resultados : []);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}