<?php
session_start();
if (!isset($_SESSION['recep_id'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Recepción - Control Total</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; margin: 0; display: flex; background: #f0f2f5; }
        .sidebar { width: 250px; background: #2c3e50; color: white; height: 100vh; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 20px; width: calc(100% - 330px); }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .grid-recepcion { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.9rem; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn-accion { padding: 5px 10px; border-radius: 4px; cursor: pointer; border: none; font-size: 0.7rem; }
        .status-badge { padding: 3px 8px; border-radius: 12px; font-size: 0.7rem; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🐾 Clínica Vet</h2>
    <p>Bienvenida, <?php echo $_SESSION['recep_nombre']; ?></p>
    <hr>
    <nav>
        <p><i class="fas fa-calendar"></i> Control de Citas</p>
        <p><i class="fas fa-paw"></i> Todas las Mascotas</p>
        <p><i class="fas fa-users"></i> Dueños registrados</p>
        <a href="logout.php" style="color: #ff7675; text-decoration: none;">Cerrar Sesión</a>
    </nav>
</div>

<div class="main-content">
    <h1>Centro de Control de Recepción</h1>

    <div class="grid-recepcion">
        <div class="card">
            <h3><i class="fas fa-search"></i> Buscador de Mascotas</h3>
            <input type="text" id="busquedaGlobal" placeholder="Busca por nombre de mascota o dueño..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            <div id="resultadosBusqueda" style="max-height: 300px; overflow-y: auto;">
                </div>
        </div>

        <div class="card">
            <h3><i class="fas fa-plus-circle"></i> Acciones Rápidas</h3>
            <button onclick="abrirModalCita()" style="background:#0984e3; color:white; padding:15px; border:none; border-radius:5px; cursor:pointer; width:100%; font-weight:bold;">
                AGENDAR NUEVA CITA
            </button>
        </div>
    </div>

    <div class="card">
        <h3><i class="fas fa-list"></i> Todas las Citas del Sistema</h3>
        <div style="margin-bottom: 10px;">
            <select id="filtroEstatus">
                <option value="Todos">Todos los estatus</option>
                <option value="Pendiente">Pendientes</option>
                <option value="Finalizada">Finalizadas</option>
            </select>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Mascota</th>
                    <th>Dueño</th>
                    <th>Doctor</th>
                    <th>Estatus</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="tablaMaestraCitas">
                </tbody>
        </table>
    </div>
</div>

<script src="js/recepcion.js"></script>
</body>
</html>