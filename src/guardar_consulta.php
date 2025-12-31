<?php
session_start();
require_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger IDs
    $id_cita = $_POST['id_cita'] ?? null;
    $id_mascota = $_POST['id_mascota'] ?? null;
    $id_doctor = $_SESSION['id_doctor'] ?? null;
    
    // Validar datos esenciales
    if (!$id_cita || !$id_mascota) {
        die("Error: Datos de cita o mascota no recibidos.");
    }

    // Recoger datos básicos del formulario
    $sintomas = $_POST['sintomas'] ?? '';
    $prediagnostico = $_POST['prediagnostico'] ?? '';
    $diagnostico = $_POST['diagnostico'] ?? '';
    $tratamiento = $_POST['tratamiento'] ?? ''; 

    // --- NUEVOS CAMPOS DEL CARNET ---
    $peso = $_POST['peso'] ?? null;
    $temperatura = $_POST['temperatura'] ?? null;
    $frecuencia_cardiaca = $_POST['frecuencia_cardiaca'] ?? null;
    $frecuencia_respiratoria = $_POST['frecuencia_respiratoria'] ?? null;
    $proxima_cita = !empty($_POST['proxima_cita']) ? $_POST['proxima_cita'] : null;

    // Medicamentos (Arrays)
    $medicamentos = $_POST['id_medicamento'] ?? [];
    $dosis = $_POST['dosis'] ?? [];

    try {
        $pdo->beginTransaction();

        // 1. Insertar la consulta médica con los nuevos campos
        $sqlC = "INSERT INTO public.consultas_medicas 
                 (fk_id_cita, fk_id_mascota, fk_id_doctor, sintomas, prediagnostico, 
                  diagnostico, tratamiento_general, peso, temperatura, 
                  frecuencia_cardiaca, frecuencia_respiratoria, proxima_cita) 
                 VALUES 
                 (:id_c, :id_m, :id_d, :sint, :pre, :diag, :trat, :peso, :temp, :fc, :fr, :px_cita) 
                 RETURNING id_consulta";
        
        $stmtC = $pdo->prepare($sqlC);
        $stmtC->execute([
            'id_c'  => $id_cita,
            'id_m'  => $id_mascota,
            'id_d'  => $id_doctor,
            'sint'  => $sintomas,
            'pre'   => $prediagnostico,
            'diag'  => $diagnostico,
            'trat'  => $tratamiento,
            'peso'  => $peso,
            'temp'  => $temperatura,
            'fc'    => $frecuencia_cardiaca,
            'fr'    => $frecuencia_respiratoria,
            'px_cita' => $proxima_cita
        ]);
        $id_consulta_generada = $stmtC->fetchColumn();

        // 2. Insertar los medicamentos
        if (!empty($medicamentos)) {
            $sqlR = "INSERT INTO public.recetas_medicamentos (fk_id_consulta, fk_id_producto, dosis_instrucciones) VALUES (?, ?, ?)";
            $stmtR = $pdo->prepare($sqlR);

            foreach ($medicamentos as $index => $id_prod) {
                if (!empty($id_prod)) {
                    $stmtR->execute([
                        $id_consulta_generada, 
                        $id_prod, 
                        $dosis[$index] ?? 'Sin instrucciones'
                    ]);
                }
            }
        }
        // ... después de insertar en consultas_medicas ...
// Dentro de guardar_consulta.php
if (isset($_POST['id_vacuna']) && is_array($_POST['id_vacuna'])) {
    $stmtVac = $pdo->prepare("INSERT INTO public.historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) VALUES (?, ?, ?)");
    
    foreach ($_POST['id_vacuna'] as $index => $id_vacuna) {
        if (!empty($id_vacuna)) { // Solo si seleccionó una vacuna
            $fecha = $_POST['fecha_vacuna'][$index];
            $stmtVac->execute([$id_mascota, $id_vacuna, $fecha]);
        }
    }
}
        // 3. ACTUALIZAR EL ESTATUS DE LA CITA
        $sqlEstatus = "UPDATE public.citas SET estatus_cita = 'Finalizada' WHERE id_cita = :id_c";
        $stmtE = $pdo->prepare($sqlEstatus);
        $stmtE->execute(['id_c' => $id_cita]);

        $pdo->commit();
        
        header("Location: dashboard_doctor.php?msg=exito");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error crítico al guardar: " . $e->getMessage());
    }
} else {
    header("Location: dashboard_doctor.php");
    exit();
}