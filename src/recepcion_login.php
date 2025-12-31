<?php
session_start();
require_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM public.recepcionistas WHERE correo = :correo AND estatus = TRUE");
    $stmt->execute(['correo' => $correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['recep_id'] = $user['id_recepcionista'];
        $_SESSION['recep_nombre'] = $user['nombre'];
        $_SESSION['rol'] = 'recepcionista';
        header("Location: recepcion_dashboard.php");
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Recepción</title>
    <link rel="stylesheet" href="../css/style.css"> </head>
<body style="display:flex; justify-content:center; align-items:center; height:100vh; background:#f4f7f6;">
    <form method="POST" style="background:white; padding:30px; border-radius:10px; box-shadow:0 4px 6px rgba(0,0,0,0.1); width:300px;">
        <h2 style="text-align:center; color:#5d4037;">Recepción</h2>
        <?php if(isset($error)) echo "<p style='color:red; font-size:0.8rem;'>$error</p>"; ?>
        <input type="email" name="correo" placeholder="Correo" required style="width:100%; margin-bottom:15px; padding:10px;">
        <input type="password" name="password" placeholder="Contraseña" required style="width:100%; margin-bottom:15px; padding:10px;">
        <button type="submit" style="width:100%; padding:10px; background:#5d4037; color:white; border:none; cursor:pointer;">Entrar</button>
    </form>
</body>
</html>