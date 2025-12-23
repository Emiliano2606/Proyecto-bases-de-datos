<?php
// 1. INCLUIR LA CONEXIÓN
require_once '../includes/db_connection.php'; 

// Función para mostrar errores y redirigir
function display_error_and_exit($error_array) {
    // Aquí puedes implementar una forma de redirigir y mostrar los errores 
    // en el formulario, pero por ahora solo los imprimiremos
    echo "<h1>Errores de Validación</h1>";
    foreach ($error_array as $error) {
        echo "<p style='color:red;'>- $error</p>";
    }
    // Opcional: Redirigir al formulario para que el usuario corrija
    // header("Location: ../registro.html?errors=" . urlencode(implode('; ', $error_array)));
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. OBTENER Y SANITIZAR DATOS INICIALES
    
    // Autenticación (se obtienen antes para la validación)
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password']; 
    
    // Datos Personales y Domicilio (solo se obtienen y sanitizan)
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $apellido1 = htmlspecialchars(trim($_POST['apellido1']));
    $apellido2 = htmlspecialchars(trim($_POST['apellido2']));
    $sexo_dueno = $_POST['sexo_dueno'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $procedencia_mascota = $_POST['procedencia_mascota'];
    $telefono_principal = htmlspecialchars(trim($_POST['telefono_principal']));
    $telefono_emergencia = htmlspecialchars(trim($_POST['telefono_emergencia']));

    $cp = htmlspecialchars(trim($_POST['cp']));
    $estado = htmlspecialchars(trim($_POST['estado']));
    $municipio = htmlspecialchars(trim($_POST['municipio']));
    $colonia = htmlspecialchars(trim($_POST['colonia']));
    $calle = htmlspecialchars(trim($_POST['calle']));
    $numero_exterior = htmlspecialchars(trim($_POST['numero_exterior']));
    $numero_interior = htmlspecialchars(trim($_POST['numero_interior']));
    $referencias = htmlspecialchars(trim($_POST['referencias']));
    
    // --- 2. VALIDACIONES ADICIONALES DE LADO DEL SERVIDOR ---
    
    $errores = [];

    // A. Validación de Campos Requeridos Faltantes (Mecanismo para NOT NULL)
    if (empty($nombre) || empty($apellido1) || empty($email) || empty($password) || empty($fecha_nacimiento)) {
        $errores[] = "Todos los campos marcados con * son obligatorios.";
    }

    // B. Validación de Formato de Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido.";
    }
    
    // C. Validación de Longitud de Contraseña
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // D. Validación de Fechas (Fecha de Nacimiento no puede ser futura)
    try {
        $fecha_nac = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        if ($fecha_nac > $hoy) {
            $errores[] = "La fecha de nacimiento no puede ser una fecha futura.";
        }
    } catch (\Exception $e) {
        $errores[] = "El formato de la fecha de nacimiento no es válido.";
    }

    // --- FIN DE VALIDACIONES ADICIONALES ---

    // 3. PROCESAR SI NO HAY ERRORES
    if (!empty($errores)) {
        display_error_and_exit($errores);
    }
    
    // Si llegamos aquí, los datos son válidos y podemos hacer el hashing
    // La variable se sigue llamando $password_hash, pero el campo de la DB es 'password'
    $password_hash = password_hash($password, PASSWORD_DEFAULT); 

    // INICIAR TRANSACCIÓN (si los datos son válidos, empezamos la inserción)
    $pdo->beginTransaction(); 
    
    try {
        // === 4. INSERCIÓN 1: TABLA Usuarios ===
        // ¡CORRECCIÓN AQUÍ! Se usa 'password' en lugar de 'password_hash' en la DB
        $sql_usuarios = "INSERT INTO Usuarios (email, password) 
                         VALUES (:email, :password_hash) RETURNING idUsuario";
        $stmt_usuarios = $pdo->prepare($sql_usuarios);
        $stmt_usuarios->execute([
            ':email' => $email,
            ':password_hash' => $password_hash // Usamos el alias de variable
        ]);
        
        $idUsuario = $stmt_usuarios->fetchColumn(); 
        
        // === 5. INSERCIÓN 2: TABLA DatosUsuario ===
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
        
        // === 6. INSERCIÓN 3: TABLA Domicilio ===
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
        
        // 7. CONFIRMAR TRANSACCIÓN y Redirigir
        $pdo->commit(); 
        header("Location: ../es/registro_mascota.html?success=1");
        exit();
        
    } catch (PDOException $e) {
        // REVERTIR TRANSACCIÓN y Manejar Errores SQL (ej: email duplicado)
        $pdo->rollBack(); 
        
        if ($e->getCode() == '23505') { 
            $error_message = "El email ya está registrado. Por favor, inicie sesión o use otro correo.";
        } else {
            // Este es un error general de la DB, solo mostrarlo si display_errors está activado
            $error_message = "Error crítico de base de datos: " . $e->getMessage();
        }
        
        echo "<h1>Error de Registro</h1>";
        echo "<p style='color:red;'>" . $error_message . "</p>";
    }
} else {
    header("Location: ../registro.html"); 
    exit();
}
?>