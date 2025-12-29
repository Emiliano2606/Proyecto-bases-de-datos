<?php
session_start();
require_once '../includes/db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_login = trim($_POST['usuario']); 
    $password_login = $_POST['contrasena'];

    try {
        // Hacemos el JOIN de las 3 tablas
        $sql = "SELECT U.idusuario, U.email, U.password, D.nombre, D.apellido1, D.telefono_principal,
                       DOM.calle, DOM.numero_exterior, DOM.numero_interior, DOM.colonia, 
                       DOM.municipio, DOM.estado, DOM.cp
                FROM usuarios U
                INNER JOIN datosusuario D ON U.idusuario = D.fk_id_usuario
                LEFT JOIN domicilio DOM ON D.fk_id_usuario = DOM.fk_usuario_id
                WHERE U.email = :email";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email_login]);
        $usuario_db = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario_db) {
            if (password_verify($password_login, $usuario_db['password'])) {
                
                // GUARDAMOS TODO EN LA SESIÓN
                $_SESSION['id_usuario'] = $usuario_db['idusuario'];
                $_SESSION['nombre'] = $usuario_db['nombre'];
                $_SESSION['apellido'] = $usuario_db['apellido1'];
                $_SESSION['email'] = $usuario_db['email'];
                $_SESSION['telefono_principal'] = $usuario_db['telefono_principal'];
                
                // Guardamos la dirección formateada
                $direccion_completa = $usuario_db['calle'] . " #" . $usuario_db['numero_exterior'];
                if (!empty($usuario_db['numero_interior'])) {
                    $direccion_completa .= " Int. " . $usuario_db['numero_interior'];
                }
                $direccion_completa .= ", Col. " . $usuario_db['colonia'] . ", " . $usuario_db['municipio'] . ", " . $usuario_db['estado'] . ". CP: " . $usuario_db['cp'];
                
                $_SESSION['direccion'] = $direccion_completa;

                header("Location: modulo_dashboard.php"); 
                exit();
            } else {
                echo "La contraseña es incorrecta.";
            }
        } else {
            echo "El correo electrónico no está registrado.";
        }
    } catch (Exception $e) {
        echo "Error en el servidor: " . $e->getMessage();
    }
}