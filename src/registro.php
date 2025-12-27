
<?php
// 1. INICIAR SESIÓN (Solo una vez al principio)
session_start();
require_once '../includes/db_connection.php'; 

// Función para mostrar errores
function display_error_and_exit($error_array) {
    echo "<h1>Errores de Validación</h1>";
    foreach ($error_array as $error) {
        echo "<p style='color:red;'>- $error</p>";
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. OBTENER Y SANITIZAR DATOS
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? ''; 
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $apellido1 = htmlspecialchars(trim($_POST['apellido1'] ?? ''));
    $apellido2 = htmlspecialchars(trim($_POST['apellido2'] ?? ''));
    $sexo_dueno = $_POST['sexo_dueno'] ?? null;
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $procedencia_mascota = $_POST['procedencia_mascota'] ?? null;
    $telefono_principal = htmlspecialchars(trim($_POST['telefono_principal'] ?? ''));
    $telefono_emergencia = htmlspecialchars(trim($_POST['telefono_emergencia'] ?? ''));

    $cp = htmlspecialchars(trim($_POST['cp'] ?? ''));
    $estado = htmlspecialchars(trim($_POST['estado'] ?? ''));
    $municipio = htmlspecialchars(trim($_POST['municipio'] ?? ''));
    $colonia = htmlspecialchars(trim($_POST['colonia'] ?? ''));
    $calle = htmlspecialchars(trim($_POST['calle'] ?? ''));
    $numero_exterior = htmlspecialchars(trim($_POST['numero_exterior'] ?? ''));
    $numero_interior = htmlspecialchars(trim($_POST['numero_interior'] ?? ''));
    $referencias = htmlspecialchars(trim($_POST['referencias'] ?? ''));
    
    // 3. VALIDACIONES
    $errores = [];

    if (empty($nombre) || empty($apellido1) || empty($email) || empty($password) || empty($fecha_nacimiento)) {
        $errores[] = "Todos los campos marcados con * son obligatorios.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido.";
    }
    
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    if (!empty($errores)) {
        display_error_and_exit($errores);
    }
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT); 

    // 4. INICIAR TRANSACCIÓN
    $pdo->beginTransaction(); 
    
    try {
        // === INSERCIÓN 1: Usuarios ===
        $sql_usuarios = "INSERT INTO Usuarios (email, password) VALUES (:email, :password_hash) RETURNING idUsuario";
        $stmt_usuarios = $pdo->prepare($sql_usuarios);
        $stmt_usuarios->execute([
            ':email' => $email,
            ':password_hash' => $password_hash
        ]);
        
        $idUsuario = $stmt_usuarios->fetchColumn(); 
        
        // === INSERCIÓN 2: DatosUsuario ===
        $sql_datos = "INSERT INTO DatosUsuario (fk_id_usuario, nombre, apellido1, apellido2, sexo_dueno, fecha_nacimiento, procedencia_mascota, telefono_principal, telefono_emergencia) 
                      VALUES (:id, :nombre, :apellido1, :apellido2, :sexo, :fecha_nac, :procedencia, :tel_princ, :tel_emerg)";
        $stmt_datos = $pdo->prepare($sql_datos);
        $stmt_datos->execute([
            ':id' => $idUsuario, 
            ':nombre' => $nombre,
            ':apellido1' => $apellido1,
            ':apellido2' => $apellido2,
            ':sexo' => $sexo_dueno,
            ':fecha_nac' => $fecha_nacimiento,
            ':procedencia' => $procedencia_mascota,
            ':tel_princ' => $telefono_principal,
            ':tel_emerg' => $telefono_emergencia
        ]);
        
        // === INSERCIÓN 3: Domicilio ===
        $sql_domicilio = "INSERT INTO Domicilio (fk_usuario_id, calle, numero_exterior, numero_interior, colonia, municipio, estado, cp, referencias)
                          VALUES (:id, :calle, :num_ext, :num_int, :colonia, :municipio, :estado, :cp, :referencias)";
        $stmt_domicilio = $pdo->prepare($sql_domicilio);
        $stmt_domicilio->execute([
            ':id' => $idUsuario, 
            ':calle' => $calle,
            ':num_ext' => $numero_exterior,
            ':num_int' => $numero_interior,
            ':colonia' => $colonia,
            ':municipio' => $municipio,
            ':estado' => $estado,
            ':cp' => $cp,
            ':referencias' => $referencias
        ]);
        
        // 5. CONFIRMAR Y GUARDAR EN SESIÓN
        $pdo->commit(); 
        
        $_SESSION['idUsuario'] = $idUsuario; // Importante para el siguiente formulario

        // Usamos la ruta absoluta que confirmamos antes para evitar el error 404
        header("Location: ../es/registro_mascota.html?success=1");
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack(); 
        if ($e->getCode() == '23505') { 
            $error_message = "El email ya está registrado.";
        } else {
            $error_message = "Error de base de datos: " . $e->getMessage();
        }
        echo "<h1>Error de Registro</h1><p style='color:red;'>$error_message</p>";
    }
} else {
    header("Location: ../registro.html"); 
        exit();
}
?>