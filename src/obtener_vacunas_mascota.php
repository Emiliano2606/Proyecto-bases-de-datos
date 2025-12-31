<?php
// Desactivar visualización de errores para que no rompan el JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once '../includes/db_connection.php'; // Ajusta esta ruta a tu conexión

$id_mascota = $_GET['id_mascota'] ?? null;

if (!$id_mascota) {
    echo json_encode(["error" => "ID de mascota no proporcionado"]);
    exit;
}

try {
    // IMPORTANTE: Usamos los nombres exactos de tu tabla historial_vacunacion
    // y catalogo_vacunas que me pasaste antes.
    $sql = "SELECT h.fecha_aplicacion, v.nombre_vacuna 
            FROM public.historial_vacunacion h
            JOIN public.catalogo_vacunas v ON h.fk_id_vacuna = v.id_vacuna
            WHERE h.fk_id_mascota = :id
            ORDER BY h.fecha_aplicacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_mascota]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay nada, devolvemos un array vacío pero VÁLIDO
    echo json_encode($resultados ? $resultados : []);

} catch (Exception $e) {
    // Si falla la base de datos, devolvemos el error en formato JSON
    echo json_encode(["error" => $e->getMessage()]);
}