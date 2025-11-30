<?php
$host = "localhost";
$port = "5432";
$dbname = "sistema_mascotas"; // ¡CAMBIA ESTO!
$user = "postgres"; // ¡CAMBIA ESTO!
$password = "potros26"; // ¡CAMBIA ESTO!

try {
    // Cadena de conexión DSN
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    // Crear una instancia de PDO para la conexión
    $pdo = new PDO($dsn, $user, $password);
    
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "¡Conexión exitosa a la base de datos '$dbname'!";
} catch (PDOException $e) {
    // Si la conexión falla, detiene la ejecución y muestra el error
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>