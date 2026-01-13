<?php
require_once '../includes/db_connection.php';
require_once 'libs/fpdf186/fpdf.php'; 

$id_cita = $_GET['id_cita'] ?? null;
if (!$id_cita) die("ID de cita no proporcionado.");
if (!$id_cita || $id_cita === 'undefined' || !is_numeric($id_cita)) {
    die("Error: El ID de la cita no es válido o no se recibió correctamente.");
}
try {
    // 1. Obtener los datos de la consulta uniendo con citas, mascotas y doctores
   
    $sql = "SELECT c.*, ci.fecha_cita, m.nombre as mascota, m.tipo_mascota, 
                   du.nombre as dueno_nombre, du.apellido1, d.nombre_doctor
            FROM public.consultas_medicas c
            JOIN public.citas ci ON c.fk_id_cita = ci.id_cita
            JOIN public.mascotas m ON c.fk_id_mascota = m.idmascota
            JOIN public.usuarios u ON m.fk_id_dueno = u.idusuario
            JOIN public.datosusuario du ON u.idusuario = du.fk_id_usuario
            JOIN public.doctores d ON c.fk_id_doctor = d.id_doctor
            WHERE ci.id_cita = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id_cita]);
    $con = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$con) die("No se encontró el registro clínico de esta cita.");

    // 2. Obtener Medicamentos (Recetas)
$sqlM = "SELECT p.nombre_producto, r.dosis_instrucciones 
         FROM public.recetas_medicamentos r
         JOIN public.producto p ON r.fk_id_producto = p.id_producto
         WHERE r.fk_id_consulta = :id_c"; 
$stmtM = $pdo->prepare($sqlM);
$stmtM->execute(['id_c' => $con['id_consulta']]);
$medicamentos = $stmtM->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título y Fecha
$pdf->Cell(0, 10, utf8_decode("RESUMEN MÉDICO DE CONSULTA"), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, utf8_decode("Fecha de atención: " . $con['fecha_cita']), 0, 1, 'R');
$pdf->Ln(5);

// Bloque de Información General
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode("DATOS DEL PACIENTE"), 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(95, 7, utf8_decode("Mascota: " . $con['mascota']), 0, 0);
$pdf->Cell(95, 7, utf8_decode("Especie: " . $con['tipo_mascota']), 0, 1);
$pdf->Cell(95, 7, utf8_decode("Dueño: " . $con['dueno_nombre'] . " " . $con['apellido1']), 0, 1);
$pdf->Ln(5);

// BLOQUE DE SIGNOS VITALES (Los que pediste)
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode("SIGNOS VITALES"), 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(47, 7, utf8_decode("Peso: " . ($con['peso'] ?: '--') . " kg"), 0, 0);
$pdf->Cell(47, 7, utf8_decode("Temp: " . ($con['temperatura'] ?: '--') . " C"), 0, 0);
$pdf->Cell(47, 7, utf8_decode("F.C: " . ($con['frecuencia_cardiaca'] ?: '--') . " lpm"), 0, 0);
$pdf->Cell(47, 7, utf8_decode("F.R: " . ($con['frecuencia_respiratoria'] ?: '--')), 0, 1);
$pdf->Ln(5);

// Diagnóstico y Síntomas
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode("REPORTE CLÍNICO"), 0, 1, 'L', true);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, utf8_decode("Síntomas:"), 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode($con['sintomas']));
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, utf8_decode("Diagnóstico:"), 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 5, utf8_decode($con['diagnostico']));
$pdf->Ln(5);

// Medicamentos
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode("TRATAMIENTO / RECETA"), 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 10);
if (count($medicamentos) > 0) {
    foreach ($medicamentos as $med) {
        $pdf->Cell(0, 6, utf8_decode("- " . $med['nombre_producto'] . ": " . $med['dosis_instrucciones']), 0, 1);
    }
} else {
    $pdf->Cell(0, 6, utf8_decode($con['tratamiento_general'] ?: "No se registraron medicamentos específicos."), 0, 1);
}

// Próxima cita
if ($con['proxima_cita']) {
    $pdf->Ln(5);
    $pdf->SetTextColor(200, 0, 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, utf8_decode("PRÓXIMA CITA RECOMENDADA: " . $con['proxima_cita']), 0, 1);
    $pdf->SetTextColor(0);
}

// Pie de página con el Doctor
$pdf->Ln(20);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 10, utf8_decode("___________________________"), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode("Dr. " . $con['nombre_doctor']), 0, 1, 'C');

$pdf->Output('I', 'Consulta_' . $con['mascota'] . '.pdf');