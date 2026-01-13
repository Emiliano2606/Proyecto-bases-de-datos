<?php
session_start();
require_once '../includes/db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_login = trim($_POST['usuario']); 
    $password_login = $_POST['contrasena'];
    
    try {
        $sql = "SELECT U.idusuario, U.email, U.password, D.nombre, D.apellido1, D.telefono_principal,
                       DOM.calle, DOM.numero_exterior, DOM.numero_interior, DOM.colonia, 
                       DOM.municipio, DOM.estado, DOM.cp
                FROM usuarios U
                INNER JOIN datosusuario D ON U.idusuario = D.fk_id_usuario
                LEFT JOIN domicilio DOM ON D.fk_id_usuario = DOM.fk_usuario_id
                WHERE U.email = :email
                LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email_login]);
        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario_db && password_verify($password_login, $usuario_db['password'])) {
            
            $_SESSION['id_usuario'] = $usuario_db['idusuario'];
            $_SESSION['nombre'] = $usuario_db['nombre'];
            $_SESSION['apellido'] = $usuario_db['apellido1'];
            $_SESSION['email'] = $usuario_db['email'];
            $_SESSION['telefono_principal'] = $usuario_db['telefono_principal'];
            
            $dir = $usuario_db['calle'] . " #" . $usuario_db['numero_exterior'];
            if (!empty($usuario_db['numero_interior'])) {
                $dir .= " Int. " . $usuario_db['numero_interior'];
            }
            $dir .= ", Col. " . $usuario_db['colonia'] . ", " . $usuario_db['municipio'];
            $_SESSION['direccion'] = $dir;


            $sql_m = "SELECT idmascota, nombre, tipo_mascota, fecha_nacimiento, sexo, foto_url 
                      FROM mascotas 
                      WHERE fk_id_dueno = :id_usuario";
            
            $stmt_m = $pdo->prepare($sql_m);
            $stmt_m->execute([':id_usuario' => $usuario_db['idusuario']]);
            $mascotas = $stmt_m->fetchAll(PDO::FETCH_ASSOC);

      
            $_SESSION['mis_mascotas'] = $mascotas;

            header("Location: modulo_dashboard.php"); 
            exit();
            
        } else {
            echo "<script>alert('Usuario o contraseña incorrectos.'); window.location.href='../index.html';</script>";
        }
    } catch (Exception $e) {
        echo "Error en el sistema: " . $e->getMessage();
    }
} else {
    header("Location: ../index.html");
    exit();
}
?>