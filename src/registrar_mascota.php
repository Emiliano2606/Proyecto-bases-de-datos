<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
session_start();

// ==============================================
// DEBUG COMPLETO - QUÉ DATOS ESTÁN LLEGANDO
// ==============================================

error_log("\n\n");
error_log("==========================================");
error_log("=== DEBUG DE DATOS RECIBIDOS - GATOS ===");
error_log("==========================================");

// 1. Mostrar TODOS los datos POST recibidos relacionados con gatos
error_log("=== TODOS LOS CAMPOS POST CON 'gato' o 'vacuna' ===");
foreach ($_POST as $key => $value) {
    if (strpos($key, 'gato') !== false || strpos($key, 'vacuna') !== false) {
        if (is_array($value)) {
            error_log("ARRAY: $key = [" . implode(', ', $value) . "]");
        } else {
            error_log("$key = " . ($value ?: '(vacío)'));
        }
    }
}

// 2. Contador específico para ver cuántos gatos vienen
error_log("\n=== CONTEO DE GATOS ===");

// Método 1: Buscar por nombres de campo
$gatosEncontrados = 0;

// Primero el gato 1 (sin sufijo)
if (isset($_POST['nombre_gato[]']) && !empty(trim($_POST['nombre_gato[]']))) {
    $gatosEncontrados++;
    error_log("✅ Gato 1 encontrado (nombre_gato[]): " . $_POST['nombre_gato[]']);
}

// Luego buscar gatos 2, 3, 4, etc.
for ($i = 2; $i <= 10; $i++) {
    $campoNombre = "nombre_gato_$i";
    if (isset($_POST[$campoNombre]) && !empty(trim($_POST[$campoNombre]))) {
        $gatosEncontrados++;
        error_log("✅ Gato $i encontrado ($campoNombre): " . $_POST[$campoNombre]);
    }
}

// Método 2: Buscar por patrones
error_log("\n=== BUSQUEDA POR PATRONES ===");
foreach ($_POST as $key => $value) {
    if (preg_match('/nombre_gato/', $key)) {
        $valor = is_array($value) ? implode(',', $value) : $value;
        error_log("Patrón encontrado: $key = $valor");
    }
}

// 3. Mostrar resumen final
error_log("\n=== RESUMEN FINAL ===");
error_log("Total de gatos detectados: $gatosEncontrados");

if ($gatosEncontrados === 0) {
    error_log("🚨 ALERTA: ¡NO SE ENCONTRARON GATOS EN LOS DATOS!");
    
    // Mostrar TODOS los datos POST para debug completo
    error_log("\n=== TODOS LOS DATOS POST (completo) ===");
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            error_log("$key = [" . implode(', ', $value) . "]");
        } else {
            error_log("$key = $value");
        }
    }
}

error_log("==========================================");

require_once '../includes/db_connection.php'; 

try {
    // 2. VERIFICAR QUE EXISTA UN DUEÑO EN SESIÓN
    $fk_id_dueno = $_SESSION['idUsuario'] ?? null;

    if (!$fk_id_dueno) {
        die("Error: No se encontró una sesión de usuario activa. Por favor, regístrese primero.");
    }

    // 3. INICIAR TRANSACCIÓN (PostgreSQL)
    $pdo->beginTransaction(); 


    // 4. PROCESAR PERROS
    if (isset($_POST['nombre_perro'])) {
        // Convertimos a array para manejar tanto uno solo como múltiples (clones)
        $nombres = (array)$_POST['nombre_perro'];

        foreach ($nombres as $index => $nombre) {
            if (empty(trim($nombre))) continue; // Saltamos si el nombre viene vacío

            // Determinamos el sufijo basado en la posición (_2, _3, etc. El primero es vacío)
            $sufijo = ($index === 0) ? "" : "_" . ($index + 1);

            // --- A. INSERTAR EN TABLA MASCOTAS ---
            $sqlMascota = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota) 
               VALUES (:dueno, :nom, :fecha, :sexo, :tipo) RETURNING idmascota";

            $stmtMascota = $pdo->prepare($sqlMascota);

        // 1. Validamos si vienen vacíos para enviar NULL real a la DB
        // Nota: Quitamos el $sufijo porque en tu HTML el name es directo
       // En la sección A, cambia a esto:
$fecha_valor = !empty($_POST["fecha_nacimiento_perro$sufijo"]) ? $_POST["fecha_nacimiento_perro$sufijo"] : null;
$sexo_valor  = !empty($_POST["sexo_perro$sufijo"]) ? $_POST["sexo_perro$sufijo"] : null;

$stmtMascota->execute([
    ':dueno' => $fk_id_dueno,
    ':nom'   => $nombre,
    ':fecha' => $fecha_valor,
    ':sexo'  => $sexo_valor,
    ':tipo'  => 'Perro'
]);
            
            // Obtenemos el ID generado para esta mascota específica
            $idNuevoMascota = $stmtMascota->fetchColumn(); 

            // --- B. INSERTAR EN DETALLES_PERROS ---
        // --- B. INSERTAR EN DETALLES_PERROS ---
$sqlDetalle = "INSERT INTO detalles_perros (
    fk_id_mascota, 
    raza_perro, 
    grupo_perro, 
    seccion_perro, 
    pais_perro, 
    color_principal, 
    color_secundario, 
    tipo_pelo, 
    patron_pelo, 
    senas_particulares, 
    tamano_perro, 
    convive_animales, 
    tipo_alimento, 
    marca_alimento, 
    veces_come_dia, 
    tratamientos_recibidos, 
    tiene_ruac, 
    ruac_valor, 
    tiene_chip, 
    numero_chip_perro, 
    tipo_chip_perro
) VALUES (
    :id, :raza, :grupo, :seccion, :pais, 
    :c1, :c2, :pelo, :patron, 
    :senas, :tam, :convive, 
    :alimento, :marca, :veces_come, :tratamientos, 
    :t_ruac, :r_val, :t_chip, :n_chip, :tipo_chip
)";

$stmtDetalle = $pdo->prepare($sqlDetalle);

$stmtDetalle->execute([
    ':id'           => $idNuevoMascota,
    ':raza'         => $_POST["raza_perro$sufijo"] ?? null,
    ':grupo'        => $_POST["grupo_perro$sufijo"] ?? null,
    ':seccion'      => $_POST["seccion_perro$sufijo"] ?? null,
    ':pais'         => $_POST["pais_perro$sufijo"] ?? null,
    ':c1'           => $_POST["color_principal_del_perro$sufijo"] ?? null,
    ':c2'           => $_POST["color_secundario_del_perro$sufijo"] ?? null,
    ':pelo'         => $_POST["tipo_de_pelo_del_perro$sufijo"] ?? null,
    ':patron'       => $_POST["patron_de_pelo$sufijo"] ?? null,
    ':senas'        => $_POST["senas_particulares$sufijo"] ?? null,
    ':tam'          => $_POST["tamano_perro$sufijo"] ?? null,
    ':convive'      => $_POST["convive_perro$sufijo"] ?? null,
    ':alimento'     => $_POST["alimento_perro$sufijo"] ?? null,
    ':marca'        => $_POST["marca_alimento_perro$sufijo"] ?? null,
    ':veces_come'   => $_POST["veces_comida_perro$sufijo"] ?? null,
    ':tratamientos' => $_POST["tratamientos_del_perro$sufijo"] ?? null,
    ':t_ruac'       => $_POST["tieneRUAC$sufijo"] ?? null,
    ':r_val'        => $_POST["ruac_perro$sufijo"] ?? null,
    ':t_chip'       => $_POST["chip_del_perro$sufijo"] ?? null,
    ':n_chip'       => $_POST["numero_chip_perro$sufijo"] ?? null,
    ':tipo_chip'    => $_POST["tipo_chip_perro$sufijo"] ?? null
]);

            // --- C. INSERTAR VACUNAS DINÁMICAS ---
            // Verificamos si hay vacunas marcadas para este perro
            $nombrePostVacunas = "vacunas_perro$sufijo";
            if (isset($_POST[$nombrePostVacunas])) {
                $vacunasSeleccionadas = (array)$_POST[$nombrePostVacunas];

                foreach ($vacunasSeleccionadas as $idVacuna) {
                    // El JS genera el nombre: fecha_VALORVACUNA_SUFIJO (ej: fecha_1_2)
                    $nombreCampoFecha = "fecha_" . $idVacuna . $sufijo;
                    $fechaAplicacion = $_POST[$nombreCampoFecha] ?? null;

                    if ($fechaAplicacion) {
                        $sqlVacuna = "INSERT INTO historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) 
            VALUES (:idm, :idv, :fec)";
                        $stmtVacuna = $pdo->prepare($sqlVacuna);
                        $stmtVacuna->execute([
  ':idm' => $idNuevoMascota,
  ':idv' => $idVacuna,
  ':fec' => $fechaAplicacion
                        ]);
                    }
                }
            }
        }
    }

 // 5. PROCESAR GATOS
// 5. PROCESAR GATOS
if (isset($_POST['nombre_gato'])) {
    $nombresGatos = (array)$_POST['nombre_gato'];

    foreach ($nombresGatos as $index => $nombre) {
        $nombreLimpio = trim($nombre);
        if (empty($nombreLimpio)) continue;

        $sufijo = ($index === 0) ? "" : "_" . ($index + 1);

        // A. INSERTAR EN TABLA MASCOTAS
        $sqlMascotaG = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota, foto_url)
                        VALUES (:dueno, :nom, :fecha, :sexo, :tipo, :foto) RETURNING idmascota";
        $stmtMascotaG = $pdo->prepare($sqlMascotaG);
        
        $fecha_v_g = !empty($_POST["fecha_nacimiento_gato$sufijo"]) ? $_POST["fecha_nacimiento_gato$sufijo"] : null;
        $sexo_v_g  = !empty($_POST["sexo_gato$sufijo"]) ? $_POST["sexo_gato$sufijo"] : null;

        $stmtMascotaG->execute([
            ':dueno' => $fk_id_dueno,
            ':nom'   => $nombreLimpio,
            ':fecha' => $fecha_v_g,
            ':sexo'  => $sexo_v_g,
            ':tipo'  => 'Gato',
            ':foto'  => NULL
        ]);
        
        $idGato = $stmtMascotaG->fetchColumn();

        // B. INSERTAR EN DETALLES_GATOS
        $sqlDetGato = "INSERT INTO detalles_gatos (fk_id_mascota, raza_gato, grupo_gato, registro_principal_gato, tamano_gato, peso_gato, tipo_pelaje_especifico, caracteristicas_fisicas, color_principal, color_secundario, tipo_pelo, patron_pelo, convive_animales, tipo_alimento, marca_alimento, veces_come_dia, tratamientos_recibidos, tiene_ruac, ruac_valor, tiene_chip, numero_chip_gato, tipo_chip_gato) 
                       VALUES (:id, :raza, :grupo, :reg, :tam, :peso, :t_pelaje, :fisic, :c1, :c2, :tpelo, :patron, :convive, :alim, :marca, :veces, :trat, :t_ruac, :r_val, :t_chip, :n_chip, :tipo_chip)";

        $stmtDetG = $pdo->prepare($sqlDetGato);
        $stmtDetG->execute([
            ':id'       => $idGato,
            ':raza'     => $_POST["raza_del_gato$sufijo"] ?? null,
            ':grupo'    => $_POST["grupo_gato$sufijo"] ?? null,
            ':reg'      => $_POST["registro_principal_gato$sufijo"] ?? null,
            ':tam'      => $_POST["tamano_gato$sufijo"] ?? null,
            ':peso'     => $_POST["peso_gato$sufijo"] ?? null,
            ':t_pelaje' => $_POST["tipopelaje_gato$sufijo"] ?? null,
            ':fisic'    => $_POST["caracterfisicas_gato$sufijo"] ?? null,
            ':c1'       => $_POST["color_principal_gato$sufijo"] ?? null,
            ':c2'       => $_POST["color_secundario_gato$sufijo"] ?? null,
            ':tpelo'    => $_POST["tipo_pelo_gato$sufijo"] ?? null,
            ':patron'   => $_POST["patron_pelo_gato$sufijo"] ?? null,
            ':convive'  => $_POST["convive_gato$sufijo"] ?? null,
            ':alim'     => $_POST["alimento_gato$sufijo"] ?? null,
            ':marca'    => $_POST["marca_alimento_gato$sufijo"] ?? null,
            ':veces'    => $_POST["veces_comida_gato$sufijo"] ?? null,
            ':trat'     => $_POST["tratamientos_gatito$sufijo"] ?? null,
            ':t_ruac'   => $_POST["tiene_ruac_gato$sufijo"] ?? null,
            ':r_val'    => $_POST["ruac_gato$sufijo"] ?? null,
            ':t_chip'   => $_POST["chip_del_gato$sufijo"] ?? null,
            ':n_chip'   => $_POST["numero_chip_gato$sufijo"] ?? null,
            ':tipo_chip'=> $_POST["tipo_chip_gato$sufijo"] ?? null
        ]);

        // ============================================
        // C. PROCESAR VACUNAS DE GATO (CORREGIDO PARA MÚLTIPLES GATOS)
        // ============================================
        
        // El nombre de la clave de vacunas cambia según el gato (vacunas_gato, vacunas_gato_2, etc.)
        $nombreClaveVacunas = "vacunas_gato" . $sufijo;

        if (isset($_POST[$nombreClaveVacunas]) && is_array($_POST[$nombreClaveVacunas])) {
            $vacunasSeleccionadas = $_POST[$nombreClaveVacunas];
            
            foreach ($vacunasSeleccionadas as $idVacuna) {
                // Buscamos la fecha con el sufijo (ej: fecha_16_2)
                $campoFecha = "fecha_{$idVacuna}{$sufijo}";
                $fechaEncontrada = $_POST[$campoFecha] ?? null;
                
                if (!empty($fechaEncontrada)) {
                    try {
                        $sqlVacuna = "INSERT INTO historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) 
                                      VALUES (:idm, :idv, :fec)";
                        $stmtVacuna = $pdo->prepare($sqlVacuna);
                        $stmtVacuna->execute([
                            ':idm' => $idGato,
                            ':idv' => $idVacuna,
                            ':fec' => $fechaEncontrada
                        ]);
                    } catch (PDOException $e) {
                        error_log("Error insertando vacuna: " . $e->getMessage());
                    }
                }
            }
        }
    }
}

    // 6. FINALIZAR TRANSACCIÓN
    $pdo->commit();

    // Redirigir a una página de éxito
    header("Location: ../vistas/perfil_usuario.php?msg=registro_exitoso");
    exit();

} catch (Exception $e) {
    // Si algo falla, deshacemos todos los inserts
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("❌ ERROR CRÍTICO: " . $e->getMessage());
    error_log("❌ STACK TRACE: " . $e->getTraceAsString());
    
    die("Error crítico al guardar las mascotas: " . $e->getMessage());
}
?>