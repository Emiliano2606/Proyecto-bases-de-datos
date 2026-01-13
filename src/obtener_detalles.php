<?php
require_once '../includes/db_connection.php';
header('Content-Type: application/json');

ob_clean(); 

if (isset($_GET['id']) && isset($_GET['tipo'])) {
    $id = intval($_GET['id']);
    $tipo = $_GET['tipo'];
    
    $tablas = [
        'Perro'     => 'detalles_perros',
        'Gato'      => 'detalles_gatos',
        'Ave'       => 'detalles_aves',
        'Lagarto'   => 'detalles_lagartos',
        'Serpiente' => 'detalles_serpientes',
        'Tortuga'   => 'detalles_tortugas'
    ];

    $tabla = $tablas[$tipo] ?? null;
    $respuesta = [
        'detalles' => [],
        'vacunas'  => []
    ];

    try {
        if ($tabla) {
            $sql_d = "SELECT * FROM public.$tabla WHERE fk_id_mascota = :id";
            $stmt_d = $pdo->prepare($sql_d);
            $stmt_d->execute([':id' => $id]);
            $respuesta['detalles'] = $stmt_d->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $sql_v = "SELECT V.nombre_vacuna, H.fecha_aplicacion 
                  FROM public.historial_vacunacion H
                  INNER JOIN public.catalogo_vacunas V ON H.fk_id_vacuna = V.id_vacuna
                  WHERE H.fk_id_mascota = :id
                  ORDER BY H.fecha_aplicacion DESC";
        $stmt_v = $pdo->prepare($sql_v);
        $stmt_v->execute([':id' => $id]);
        $respuesta['vacunas'] = $stmt_v->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($respuesta);

    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Faltan parámetros']);
}