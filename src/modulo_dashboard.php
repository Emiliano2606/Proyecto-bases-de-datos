<?php
session_start();

// 1. Verificamos que el usuario tenga sesión, si no, al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../es/registro_mascota.html"); // Cambia esto al nombre de tu archivo de login
    exit();
}

// 2. Variables de sesión para usar en el HTML
$nombreUsuario = $_SESSION['nombre'];
$apellidoUsuario = $_SESSION['apellido'];
$emailUsuario = $_SESSION['email'];
$telefono_principal = $_SESSION['telefono_principal'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chikis - Historial Clínico</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="../es/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <div class="titutifuti">
        <div class="emilianiiiii">
            <main class="tralalero-tralala">
                <div class="sape card">
                    <div class="sape2">
                        <img src="../css/img2/enproceso.jpg" alt="Foto de Chikis" class="tun-tun" id="profile-image">
                        <div class="sahur">
                            <h2>Chikis</h2>
                            <p>Chihuahua</p>
                            <p>5 años</p>
                            <p>Tutor: <?php echo htmlspecialchars($nombreUsuario); ?></p>
                        </div>
                    </div>
                    
                    <div class="picoro">
                        <h4>Información del Dueño</h4>
                        <p><strong>Dueño:</strong> <?php echo htmlspecialchars($nombreUsuario . " " . $apellidoUsuario); ?></p>
                        <p><strong>Correo:</strong> <?php echo htmlspecialchars($emailUsuario); ?></p>
                        
<p><strong>Teléfono:</strong> <?php echo htmlspecialchars($_SESSION['telefono_principal']); ?></p>                       
<p><strong>Dirección:</strong> <?php echo htmlspecialchars($_SESSION['direccion'] ?? 'Dirección no disponible'); ?></p>                    </div>

                    <div class="trunk">
                        <h4>Últimos signos vitales</h4>
                        <p><strong>Hembra</strong></p>
                        <p><strong>Alergias:</strong> N/A</p>
                        <p><strong>Peso:</strong> 5 kg</p>
                        <p><strong>Temperatura:</strong> 38°</p>
                        <p><strong>Frecuencia Cardíaca:</strong> 120 prmm</p>
                        <p><strong>Frecuencia Respiratoria:</strong> 20 rrmm</p>
                        <p><strong>Desparasitación:</strong> 10/01/25</p>
                    </div>
                </div>

                <div class="cabezon">
                    <div class="card cabezoni">
                        <h4>Vacunas</h4>
                        <ul>
                            <li>Rabia. <span>12/08/23</span></li>
                            <li>Moquillo canino. <span>20/09/24</span></li>
                        </ul>
                    </div>
                    <div class="card cabezoni">
                        <h4><a href="logout.php" style="color: red; text-decoration: none;">Cerrar Sesión</a></h4>
                    </div>
                </div>
            </main>

            <aside class="jbalvin">
                <div class="cahcitrula card">
                    <h3>Agendar consulta</h3>
                </div>
                </aside>
            <div class="page-number">12</div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileImage = document.getElementById('profile-image');
            profileImage.src = '../css/img2/enproceso.jpg'; 
        });
    </script>
</body>
</html>