<?php
session_start();
require_once '../includes/db_connection.php';

// 1. Inicializar la variable siempre para evitar el error de "Undefined variable"
$citas = []; 

// Seguridad: Solo doctores
if (!isset($_SESSION['id_doctor']) || $_SESSION['rol'] !== 'doctor') {
    header("Location: login_doctor.html");
    exit();
}

$idDoctor = $_SESSION['id_doctor'];
$nombreDoctor = $_SESSION['nombre_doctor'];

// 2. Ejecutar la consulta
try {
    $sql = "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.motivo_cliente, c.estatus_cita,
                   m.nombre AS nombre_mascota, m.tipo_mascota,
                   e.nombre_especialidad,
                   du.nombre AS nombre_tutor, du.apellido1 AS apellido_tutor
            FROM public.citas c
            JOIN public.mascotas m ON c.fk_id_mascota = m.idmascota
            /* Unimos mascotas con usuarios y luego con sus datos personales */
            JOIN public.usuarios u ON m.fk_id_dueno = u.idusuario 
            JOIN public.datosusuario du ON u.idusuario = du.fk_id_usuario
            JOIN public.doctor_asignacion da ON c.fk_id_asignacion = da.id_asignacion
            JOIN public.especialidades e ON da.fk_id_especialidad = e.id_especialidad
            WHERE da.fk_id_doctor = :id_doc 
            AND (c.estatus_cita = 'Programada' OR c.estatus_cita = 'En Proceso') 
            ORDER BY c.fecha_cita ASC, c.hora_cita ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id_doc' => $idDoctor]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas Programadas - Dr. <?php echo explode(' ', $nombreDoctor)[0]; ?></title>
    <link rel="stylesheet" href="../es/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .badge-programada {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #bee5eb;
        }
        .btn-atender {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        .btn-atender:hover {
            background-color: #219150;
            transform: translateY(-2px);
        }
    </style>
</head>
<body style="background-color: #fdfaf8;">

    <div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
            <div>
                <h1 style="color: #5d4037; margin-bottom: 5px;">Panel de Citas</h1>
                <p style="color: #888;">Doctor: <strong><?php echo htmlspecialchars($nombreDoctor); ?></strong></p>
            </div>
            <a href="logout_doctor.php" style="color: #e74c3c; text-decoration: none;"><i class="fa-solid fa-power-off"></i> Salir</a>
        </div>
        

        <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h2 style="margin-bottom: 20px; color: #5d4037;"><i class="fa-solid fa-clock-rotate-left"></i> Citas Programadas (Pendientes)</h2>
            
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #5d4037; text-align: left;">
                        <th style="padding: 12px;">Horario</th>
                        <th style="padding: 12px;">Mascota</th>
                        <th style="padding: 12px;">Tutor</th>
                        <th style="padding: 12px;">Estatus</th>
                        <th style="padding: 12px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($citas) > 0): ?>
                        <?php foreach ($citas as $c): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px;">
                                    <strong><?php echo $c['fecha_cita']; ?></strong><br>
                                    <small><?php echo substr($c['hora_cita'], 0, 5); ?> hrs</small>
                                </td>
                                <td style="padding: 15px;">
                                    <span style="font-weight: bold;"><?php echo htmlspecialchars($c['nombre_mascota']); ?></span><br>
                                    <small style="color: #888;"><?php echo $c['tipo_mascota']; ?></small>
                                </td>
                                <td style="padding: 15px; color: #555;">
                                    <?php echo htmlspecialchars($c['nombre_tutor'] . " " . $c['apellido_tutor']); ?>
                                </td>
                                <td style="padding: 15px;">
                                    <span class="badge-programada"><?php echo $c['estatus_cita']; ?></span>
                                </td>
                                <td style="padding: 15px;">
                                    <a href="atender_mascota.php?id_cita=<?php echo $c['id_cita']; ?>" class="btn-atender">
                                        <i class="fa-solid fa-notes-medical"></i> Iniciar Consulta
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 50px; color: #999;">
                                <i class="fa-solid fa-calendar-check fa-3x" style="margin-bottom: 15px;"></i><br>
                                No hay nuevas citas <strong>Programadas</strong>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>