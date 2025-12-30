<?php
// 1. Conexión a la base de datos
require_once '../includes/db_connection.php';

// 2. Establecer el encabezado para que el navegador sepa que enviamos un JSON
header('Content-Type: application/json');

if (isset($_GET['id']) && isset($_GET['tipo'])) {
    $id = intval($_GET['id']);
    $tipo = $_GET['tipo'];
    
    // 3. Mapeo de tablas según el tipo de mascota registrado en la tabla general
    $tablas = [
        'Perro'     => 'detalles_perros',
        'Gato'      => 'detalles_gatos',
        'Ave'       => 'detalles_aves',
        'Lagarto'   => 'detalles_lagartos',
        'Serpiente' => 'detalles_serpientes',
        'Tortuga'   => 'detalles_tortugas'
    ];

    $tabla = $tablas[$tipo] ?? null;

    if ($tabla) {
        try {
            // 4. Consulta a la tabla específica (polimorfismo)
            $sql = "SELECT * FROM public.$tabla WHERE fk_id_mascota = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $detalles = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si existe la fila, la devuelve; si no, devuelve un objeto vacío
            echo json_encode($detalles ?: []);
        } catch (PDOException $e) {
            // En caso de error de SQL, enviamos el error en formato JSON para debug
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        // Si el tipo no está en nuestro mapa, enviamos vacío
        echo json_encode([]);
    }
} else {
    // Si faltan parámetros en la URL
    echo json_encode(['error' => 'Faltan parámetros id o tipo']);
}