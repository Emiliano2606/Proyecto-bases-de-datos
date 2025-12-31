<?php
require_once '../includes/db_connection.php'; 

try {
    $query = "SELECT id_especialidad, nombre_especialidad FROM public.especialidades ORDER BY nombre_especialidad ASC";
    $stmt = $pdo->query($query);
    $especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($especialidades);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>