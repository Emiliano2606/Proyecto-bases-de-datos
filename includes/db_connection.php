<?php
$host = "localhost";
$port = "5432";
$dbname = "sistema_mascotas"; // ¡Modifiquen este ya que es le nombre de la base de datos!
$user = "postgres"; 
$password = "potros26"; // ¡contrasena de postresql!

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    $pdo = new PDO($dsn, $user, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>