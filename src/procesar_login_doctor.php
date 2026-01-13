<?php
require_once '../includes/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM public.doctores WHERE email_doctor = :email");
        $stmt->execute(['email' => $email]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doctor && $password === $doctor['password_doctor']) {
            $_SESSION['id_doctor'] = $doctor['id_doctor'];
            $_SESSION['nombre_doctor'] = $doctor['nombre_doctor'];
            $_SESSION['rol'] = 'doctor';

            header("Location: dashboard_doctor.php");
            exit();
        } else {
            echo "<script>alert('Correo o contraseña incorrectos'); window.location.href='login_doctor.html';</script>";
        }
    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
}