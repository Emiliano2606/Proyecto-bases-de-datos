<?php
session_start();



error_log("\n\n");
error_log("==========================================");
error_log("=== DEBUG DE DATOS RECIBIDOS - GATOS ===");
error_log("==========================================");

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

error_log("\n=== CONTEO DE GATOS ===");

$gatosEncontrados = 0;

if (isset($_POST['nombre_gato[]']) && !empty(trim($_POST['nombre_gato[]']))) {
    $gatosEncontrados++;
    error_log(" Gato 1 encontrado (nombre_gato[]): " . $_POST['nombre_gato[]']);
}

for ($i = 2; $i <= 10; $i++) {
    $campoNombre = "nombre_gato_$i";
    if (isset($_POST[$campoNombre]) && !empty(trim($_POST[$campoNombre]))) {
        $gatosEncontrados++;
        error_log(" Gato $i encontrado ($campoNombre): " . $_POST[$campoNombre]);
    }
}

error_log(message: "\n=== BUSQUEDA POR PATRONES ===");
foreach ($_POST as $key => $value) {
    if (preg_match('/nombre_gato/', $key)) {
        $valor = is_array($value) ? implode(',', $value) : $value;
        error_log("Patrón encontrado: $key = $valor");
    }
}

error_log("\n=== RESUMEN FINAL ===");
error_log("Total de gatos detectados: $gatosEncontrados");

if ($gatosEncontrados === 0) {
    error_log(" ALERTA: ¡NO SE ENCONTRARON GATOS EN LOS DATOS!");
    
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
    $fk_id_dueno = $_SESSION['idUsuario'] ?? null;

    if (!$fk_id_dueno) {
        die("Error: No se encontró una sesión de usuario activa. Por favor, regístrese primero.");
    }

    $pdo->beginTransaction(); 


   if (isset($_POST['nombre_perro'])) {
        $nombresPerros = (array)$_POST['nombre_perro'];

        foreach ($nombresPerros as $index => $nombre) {
            if (empty(trim($nombre))) continue;

            $sufijo = ($index === 0) ? "" : "_" . ($index + 1);

            $sqlMascota = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota) 
                           VALUES (:dueno, :nom, :fecha, :sexo, :tipo) RETURNING idmascota";

            $stmtMascota = $pdo->prepare($sqlMascota);
            $fecha_valor = !empty($_POST["fecha_nacimiento_perro$sufijo"]) ? $_POST["fecha_nacimiento_perro$sufijo"] : null;
            $sexo_valor  = !empty($_POST["sexo_perro$sufijo"]) ? $_POST["sexo_perro$sufijo"] : null;

            $stmtMascota->execute([
                ':dueno' => $fk_id_dueno,
                ':nom'   => $nombre,
                ':fecha' => $fecha_valor,
                ':sexo'  => $sexo_valor,
                ':tipo'  => 'Perro'
            ]);
            
            $idNuevoMascota = $stmtMascota->fetchColumn(); 

            $sqlDetalle = "INSERT INTO detalles_perros (
                fk_id_mascota, raza_perro, grupo_perro, seccion_perro, pais_perro, 
                color_principal, color_secundario, tipo_pelo, patron_pelo, 
                senas_particulares, tamano_perro, convive_animales, tipo_alimento, 
                marca_alimento, veces_come_dia, tratamientos_recibidos, 
                tiene_ruac, ruac_valor, tiene_chip, numero_chip_perro, tipo_chip_perro
            ) VALUES (
                :id, :raza, :grupo, :seccion, :pais, :c1, :c2, :pelo, :patron, 
                :senas, :tam, :convive, :alimento, :marca, :veces_come, :tratamientos, 
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

            $nombrePostVacunas = "vacunas_perro$sufijo";
            if (isset($_POST[$nombrePostVacunas])) {
                foreach ((array)$_POST[$nombrePostVacunas] as $idVacuna) {
                    $fechaCampo = "fecha_" . $idVacuna . $sufijo;
                    if (!empty($_POST[$fechaCampo])) {
                        $stmtVacuna = $pdo->prepare("INSERT INTO historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) VALUES (:idm, :idv, :fec)");
                        $stmtVacuna->execute([':idm' => $idNuevoMascota, ':idv' => $idVacuna, ':fec' => $_POST[$fechaCampo]]);
                    }
                }
            }
        }
    }

 // ==============================================
// PROCESAR GATOS 
// ==============================================
error_log("=== INICIANDO PROCESAMIENTO GATOS ===");

function obtenerDatoGato($campoBase, $sufijo) {
    $campoCompleto = $campoBase . $sufijo;
    if (isset($_POST[$campoCompleto]) && !empty(trim($_POST[$campoCompleto]))) {
        return trim($_POST[$campoCompleto]);
    }
    return null;
}

$todosLosGatos = [];
for ($i = 1; $i <= 10; $i++) {
    $campoForzado = 'nombre_gato_forzado_' . $i;
    if (isset($_POST[$campoForzado]) && !empty(trim($_POST[$campoForzado]))) {
        $todosLosGatos[] = [
            'nombre' => trim($_POST[$campoForzado]),
            'indice' => $i - 1
        ];
    }
}

foreach ($todosLosGatos as $gato) {
    $nombreLimpio = $gato['nombre'];
    $indice = $gato['indice'];
    $sufijo = ($indice === 0) ? "" : "_" . ($indice + 1);

    try {
        $fecha_n = obtenerDatoGato("fecha_nacimiento_gato", $sufijo);
        $sexo_n = obtenerDatoGato("sexo_gato", $sufijo);

        $sqlMascota = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota)
                       VALUES (:dueno, :nom, :fecha, :sexo, :tipo) RETURNING idmascota";
        $stmt = $pdo->prepare($sqlMascota);
        $stmt->execute([
            ':dueno' => $fk_id_dueno,
            ':nom'   => $nombreLimpio,
            ':fecha' => $fecha_n,
            ':sexo'  => $sexo_n,
            ':tipo'  => 'Gato'
        ]);
        $idGato = $stmt->fetchColumn();

        $sqlDet = "INSERT INTO detalles_gatos (
            fk_id_mascota, raza_gato, grupo_gato, registro_principal_gato, tamano_gato, 
            peso_gato, tipo_pelaje_especifico, caracteristicas_fisicas, color_principal, 
            color_secundario, tipo_pelo, patron_pelo, convive_animales, tipo_alimento, 
            marca_alimento, veces_come_dia, tratamientos_recibidos, tiene_ruac, 
            ruac_valor, tiene_chip, numero_chip_gato, tipo_chip_gato
        ) VALUES (
            :id, :raza, :grupo, :reg, :tam, :peso, :t_pelaje, :fisic, :c1, :c2, 
            :tpelo, :patron, :convive, :alim, :marca, :veces, :trat, :t_ruac, 
            :r_val, :t_chip, :n_chip, :tipo_chip
        )";

        $stmtDet = $pdo->prepare($sqlDet);
        $stmtDet->execute([
            ':id' => $idGato,
            ':raza' => obtenerDatoGato("raza_del_gato", $sufijo),
            ':grupo' => obtenerDatoGato("grupo_gato", $sufijo),
            ':reg' => obtenerDatoGato("registro_principal_gato", $sufijo),
            ':tam' => obtenerDatoGato("tamano_gato", $sufijo),
            ':peso' => obtenerDatoGato("peso_gato", $sufijo),
            ':t_pelaje' => obtenerDatoGato("tipopelaje_gato", $sufijo),
            ':fisic' => obtenerDatoGato("caracterfisicas_gato", $sufijo),
            ':c1' => obtenerDatoGato("color_principal_gato", $sufijo),
            ':c2' => obtenerDatoGato("color_secundario_gato", $sufijo),
            ':tpelo' => obtenerDatoGato("tipo_pelo_gato", $sufijo),
            ':patron' => obtenerDatoGato("patron_pelo_gato", $sufijo),
            ':convive' => obtenerDatoGato("convive_gato", $sufijo),
            ':alim' => obtenerDatoGato("alimento_gato", $sufijo),
            ':marca' => obtenerDatoGato("marca_alimento_gato", $sufijo),
            ':veces' => obtenerDatoGato("veces_comida_gato", $sufijo),
            ':trat' => obtenerDatoGato("tratamientos_gatito", $sufijo),
            ':t_ruac' => obtenerDatoGato("tiene_ruac_gato", $sufijo),
            ':r_val' => obtenerDatoGato("ruac_gato", $sufijo),
            ':t_chip' => obtenerDatoGato("chip_del_gato", $sufijo),
            ':n_chip' => obtenerDatoGato("numero_chip_gato", $sufijo),
            ':tipo_chip' => obtenerDatoGato("tipo_chip_gato", $sufijo)
        ]);

        $claveVacunas = "vacunas_gato" . $sufijo;
        if (isset($_POST[$claveVacunas]) && is_array($_POST[$claveVacunas])) {
            foreach ($_POST[$claveVacunas] as $idVacuna) {
                $campoFecha = "fecha_{$idVacuna}{$sufijo}";
                if (!empty($_POST[$campoFecha])) {
                    $sqlV = "INSERT INTO historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) 
                             VALUES (?, ?, ?)";
                    $pdo->prepare($sqlV)->execute([$idGato, $idVacuna, $_POST[$campoFecha]]);
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error en gato: " . $e->getMessage());
    }
}














if (isset($_POST['tipo_mascota']) && $_POST['tipo_mascota'] === 'Ave') {
// ==============================================
// . PROCESAR AVES -
// ==============================================

error_log("=== INICIANDO PROCESAMIENTO AVES ===");

function obtenerDatoAve($campoBase, $sufijo) {
    $campoCompleto = $campoBase . $sufijo;
    
    if (isset($_POST[$campoCompleto]) && !empty(trim($_POST[$campoCompleto]))) {
        $valor = trim($_POST[$campoCompleto]);
        error_log("   ✅ $campoCompleto = '$valor'");
        return $valor;
    } else {
        error_log("   ⚠️ $campoCompleto NO encontrado o vacío");
        return null;
    }
}

$todosLasAves = [];

for ($i = 1; $i <= 10; $i++) {
    $campoForzado = 'nombre_ave_forzado_' . $i;
    if (isset($_POST[$campoForzado]) && !empty(trim($_POST[$campoForzado]))) {
        $nombreLimpio = trim($_POST[$campoForzado]);
        $todosLasAves[] = [
            'nombre' => $nombreLimpio,
            'indice' => $i - 1,
            'tipo' => 'forzado'
        ];
        error_log(" Ave forzada $i: '$nombreLimpio'");
    }
}

if (isset($_POST['nombre_mascota']) && !empty(trim($_POST['nombre_mascota']))) {
    $nombreLimpio = trim($_POST['nombre_mascota']);
    
    $existe = false;
    foreach ($todosLasAves as $ave) {
        if ($ave['nombre'] === $nombreLimpio) $existe = true;
    }
    
    if (!$existe) {
        $todosLasAves[] = [
            'nombre' => $nombreLimpio,
            'indice' => 0,
            'tipo' => 'primera'
        ];
        error_log(" Ave 1 (nombre_mascota): '$nombreLimpio'");
    }
}

for ($i = 2; $i <= 10; $i++) {
    $campoAve = 'nombre_mascota_' . $i;
    
    if (isset($_POST[$campoAve]) && !empty(trim($_POST[$campoAve]))) {
        $nombreLimpio = trim($_POST[$campoAve]);
        
        $existe = false;
        foreach ($todosLasAves as $ave) {
            if ($ave['nombre'] === $nombreLimpio) $existe = true;
        }
        
        if (!$existe) {
            $todosLasAves[] = [
                'nombre' => $nombreLimpio,
                'indice' => $i - 1,
                'tipo' => 'adicional'
            ];
            error_log(" Ave $i ($campoAve): '$nombreLimpio'");
        }
    }
}

usort($todosLasAves, function($a, $b) {
    return $a['indice'] - $b['indice'];
});

error_log(" Total aves encontradas: " . count($todosLasAves));

if (empty($todosLasAves)) {
    error_log(" NO SE ENCONTRARON AVES PARA PROCESAR");
} else {
    foreach ($todosLasAves as $ave) {
        $nombreLimpio = $ave['nombre'];
        $indice = $ave['indice'];
        
        $sufijo = ($indice === 0) ? "" : "_" . ($indice + 1);
        
        error_log("\n PROCESANDO AVE " . ($indice + 1) . " '$nombreLimpio' (sufijo: '$sufijo')");
        
        try {
       
            $fecha_v_a = obtenerDatoAve("fecha_nacimiento_ave", $sufijo);
            $sexo_v_a = obtenerDatoAve("sexo_ave", $sufijo);
            
   
            $sqlMascotaA = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota, foto_url)
                            VALUES (:dueno, :nom, :fecha, :sexo, :tipo, :foto) RETURNING idmascota";
            $stmtMascotaA = $pdo->prepare($sqlMascotaA);
            
            $stmtMascotaA->execute([
                ':dueno' => $fk_id_dueno,
                ':nom'   => $nombreLimpio,
                ':fecha' => $fecha_v_a,
                ':sexo'  => $sexo_v_a,
                ':tipo'  => 'Ave',
                ':foto'  => NULL
            ]);
            
            $idAve = $stmtMascotaA->fetchColumn();
            error_log("    Ave insertada con ID: $idAve");

            $sqlDetAve = "INSERT INTO detalles_aves (
                fk_id_mascota, especie_ave, grupo_taxonomico, estatus_conservacion, 
                clasificacion_autoridades, tamano_ave, convive_animales, tipo_alimento, 
                marca_alimento, veces_come_dia, tratamientos_recibidos, tipo_jaula, 
                dimensiones_jaula, tipo_plumas, color_principal, color_secundario, 
                tiene_chip, numero_chip, tipo_chip
            ) VALUES (
                :id, :especie, :grupo_tax, :conservacion, :clasificacion, 
                :tamano, :convive, :alimento, :marca, :veces, :tratamientos, 
                :tipo_jaula, :dimensiones, :tipo_plumas, :color_prim, :color_sec, 
                :t_chip, :n_chip, :tipo_chip
            )";
            
            $stmtDetA = $pdo->prepare($sqlDetAve);
            
            $valoresDetalles = [
                ':id' => $idAve,
                ':especie' => obtenerDatoAve("especie_ave", $sufijo),
                ':grupo_tax' => obtenerDatoAve("grupo_taxonomico", $sufijo),
                ':conservacion' => obtenerDatoAve("estatus_conservacion", $sufijo),
                ':clasificacion' => obtenerDatoAve("clasificacion_autoridades", $sufijo),
                ':tamano' => obtenerDatoAve("tamano_ave", $sufijo),
                ':convive' => obtenerDatoAve("convive_ave", $sufijo),
                ':alimento' => obtenerDatoAve("alimento_ave", $sufijo),
                ':marca' => obtenerDatoAve("marca_alimento_ave", $sufijo),
                ':veces' => obtenerDatoAve("veces_comida_ave", $sufijo),
                ':tratamientos' => obtenerDatoAve("tratamientos_ave", $sufijo),
                ':tipo_jaula' => obtenerDatoAve("tipo_jaula_ave", $sufijo),
                ':dimensiones' => obtenerDatoAve("dimensiones_jaula_ave", $sufijo),
                ':tipo_plumas' => obtenerDatoAve("tipo_plumas_ave", $sufijo),
                ':color_prim' => obtenerDatoAve("color_principal_ave", $sufijo),
                ':color_sec' => obtenerDatoAve("color_secundario_ave", $sufijo),
                ':t_chip' => obtenerDatoAve("chip_del_ave", $sufijo),
                ':n_chip' => obtenerDatoAve("numero_chip_ave", $sufijo),
                ':tipo_chip' => obtenerDatoAve("tipo_chip_ave", $sufijo)
            ];
            
            $stmtDetA->execute($valoresDetalles);
            error_log("    Detalles insertados para ave $idAve");
            

            $nombreClaveVacunas = "vacunas_ave" . $sufijo;
            
            if (isset($_POST[$nombreClaveVacunas]) && is_array($_POST[$nombreClaveVacunas])) {
                $vacunasSeleccionadas = $_POST[$nombreClaveVacunas];
                $vacunasCount = count($vacunasSeleccionadas);
                
                error_log("   💉 Vacunas encontradas: $vacunasCount (" . implode(', ', $vacunasSeleccionadas) . ")");
                
                foreach ($vacunasSeleccionadas as $idVacuna) {
                    $campoFecha = "fecha_{$idVacuna}{$sufijo}";
                    
                    if (isset($_POST[$campoFecha]) && !empty(trim($_POST[$campoFecha]))) {
                        $fechaVacuna = trim($_POST[$campoFecha]);
                        
                        $sqlVacuna = "INSERT INTO historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) 
                                      VALUES (:idm, :idv, :fec)";
                        $stmtVacuna = $pdo->prepare($sqlVacuna);
                        $stmtVacuna->execute([
                            ':idm' => $idAve,
                            ':idv' => $idVacuna,
                            ':fec' => $fechaVacuna
                        ]);
                        
                        error_log("      Vacuna $idVacuna insertada: $fechaVacuna");
                    } else {
                        error_log("      Vacuna $idVacuna sin fecha ($campoFecha)");
                    }
                }
            } else {
                error_log("   ℹ No hay vacunas para esta ave ($nombreClaveVacunas)");
            }
            
            error_log("--- Ave '$nombreLimpio' procesada correctamente ---\n");
            
        } catch (PDOException $e) {
            error_log(" ERROR procesando ave '$nombreLimpio': " . $e->getMessage());
        }
    }
}

error_log("=== FIN PROCESAMIENTO AVES ===");
error_log("Total aves procesadas exitosamente: " . count($todosLasAves));
}





error_log("=== PROCESANDO SECCIÓN LAGARTOS ===");

for ($i = 1; $i <= 10; $i++) {
    $nombreForzado = $_POST["nombre_lagarto_forzado_$i"] ?? null;

    if ($nombreForzado) {
        try {
            $sqlM = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota) 
                     VALUES (:dueno, :nom, :fec, :sex, :tipo) RETURNING idmascota";
            $stmtM = $pdo->prepare($sqlM);
            $stmtM->execute([
                ':dueno' => $fk_id_dueno,
                ':nom'   => $nombreForzado,
                ':fec'   => $_POST["fecha_nacimiento_lag_forzado_$i"] ?: null,
                ':sex'   => $_POST["sexo_lag_forzado_$i"] ?: null,
                ':tipo'  => 'Lagarto'
            ]);
            $idLagarto = $stmtM->fetchColumn();

            $sqlD = "INSERT INTO detalles_lagartos (
                fk_id_mascota, especie_lagarto, tamano_lagarto, clasificacion_lagarto,
                estatus_lagarto, requerimientos_ambientales_lagarto, fuente_calor_lagarto,
                tipo_terrario_lagarto, dimensiones_terrario_lagarto, dieta_lagarto,
                marca_alimento_lagarto, veces_comida_lagarto, tratamientos_lagarto
            ) VALUES (
                :id, :esp, :tam, :cla, :est, :req, :fue, :ter, :dim, :die, :mar, :vec, :tra
            )";

            $stmtD = $pdo->prepare($sqlD);
            $stmtD->execute([
                ':id'  => $idLagarto,
                ':esp' => $_POST["especie_lagarto_lag_forzado_$i"] ?: 'Desconocido',
                ':tam' => $_POST["tamano_lagarto_lag_forzado_$i"] ?: 'N/A',
                ':cla' => $_POST["clasificacion_lagarto_lag_forzado_$i"] ?: 'N/A',
                ':est' => $_POST["estatus_lagarto_lag_forzado_$i"] ?: 'N/A',
                ':req' => $_POST["requerimientos_ambientales_lagarto_lag_forzado_$i"] ?: 'N/A',
                ':fue' => $_POST["fuente_calor_lagarto_lag_forzado_$i"] ?: 'N/A',
                ':ter' => $_POST["tipo_terrario_lagarto_lag_forzado_$i"] ?: null,
                ':dim' => $_POST["dimensiones_terrario_lagarto_lag_forzado_$i"] ?: null,
                ':die' => $_POST["dieta_lagarto_lag_forzado_$i"] ?: null,
                ':mar' => $_POST["marca_alimento_lagarto_lag_forzado_$i"] ?: null,
                ':vec' => $_POST["veces_comida_lagarto_lag_forzado_$i"] ?: null,
                ':tra' => $_POST["tratamientos_lagarto_lag_forzado_$i"] ?: null
            ]);

            $vacunas = $_POST["vacunas_lag_forzado_$i"] ?? [];
            foreach ($vacunas as $idVac) {
                $fechaVac = $_POST["fecha_vacuna_lag_forzado_{$idVac}_{$i}"] ?? null;
                if ($fechaVac) {
                    $sqlV = "INSERT INTO historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) 
                             VALUES (?, ?, ?)";
                    $pdo->prepare($sqlV)->execute([$idLagarto, $idVac, $fechaVac]);
                }
            }
            error_log(" Lagarto $i guardado con éxito.");

        } catch (Exception $e) {
            error_log(" Error en Lagarto $i: " . $e->getMessage());
        }
    }
}



error_log("=== INICIANDO PROCESAMIENTO SERPIENTES ===");

for ($i = 1; $i <= 10; $i++) {
    $nombreForzado = $_POST["nombre_serp_forzado_$i"] ?? null;

    if ($nombreForzado) {
        try {
            $sqlM = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota) 
                     VALUES (:dueno, :nom, :fec, :sex, :tipo) RETURNING idmascota";
            $stmtM = $pdo->prepare($sqlM);
            $stmtM->execute([
                ':dueno' => $fk_id_dueno,
                ':nom'   => $nombreForzado,
                ':fec'   => $_POST["fecha_nacimiento_serp_forzado_$i"] ?: null,
                ':sex'   => $_POST["sexo_serp_forzado_$i"] ?: null,
                ':tipo'  => 'Serpiente'
            ]);
            $idMascota = $stmtM->fetchColumn();

            $sqlD = "INSERT INTO detalles_serpientes (
                fk_id_mascota, especie_serpiente, tamano_serpiente, clasificacion_serpiente,
                estatus_serpiente, fuente_calor_serpiente, tipo_terrario_serpiente,
                alimentos_serpiente, marca_alimento_serpiente, veces_comida_serpiente, 
                tipo_tratamiento_serpiente, dimensiones_terrario_serpiente
            ) VALUES (
                :id, :esp, :tam, :cla, :est, :fue, :ter, :ali, :mar, :vec, :tra, :dim
            )";

            $stmtD = $pdo->prepare($sqlD);
            $stmtD->execute([
                ':id'  => $idMascota,
                ':esp' => $_POST["especie_serpiente_serp_forzado_$i"] ?: null,
                ':tam' => $_POST["tamano_serpiente_serp_forzado_$i"] ?: null,
                ':cla' => $_POST["clasificacion_serpiente_serp_forzado_$i"] ?: null,
                ':est' => $_POST["estatus_serpiente_serp_forzado_$i"] ?: null,
                ':fue' => $_POST["fuente_calor_serpiente_serp_forzado_$i"] ?: null,
                ':ter' => $_POST["tipo_terrario_serpiente_serp_forzado_$i"] ?: null,
                ':ali' => $_POST["alimentos_serpiente_serp_forzado_$i"] ?: null,
                ':mar' => $_POST["marca_alimento_serpiente_serp_forzado_$i"] ?: null,
                ':vec' => $_POST["veces_comida_serpiente_serp_forzado_$i"] ?: null,
                ':tra' => $_POST["tipo_tratamiento_serpiente_serp_forzado_$i"] ?: null,
                ':dim' => $_POST["dimensiones_terrario_serpiente_serp_forzado_$i"] ?: null
            ]);

            $vacunas = $_POST["vacunas_serp_forzado_$i"] ?? [];
            foreach ($vacunas as $idVac) {
                $fechaVac = $_POST["fecha_v_serp_forzado_{$idVac}_{$i}"] ?? null;
                if ($fechaVac) {
                    $sqlV = "INSERT INTO historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) 
                             VALUES (?, ?, ?)";
                    $pdo->prepare($sqlV)->execute([$idMascota, $idVac, $fechaVac]);
                }
            }
            error_log(" Serpiente $i guardada correctamente.");

        } catch (Exception $e) {
            error_log(" Error en Serpiente $i: " . $e->getMessage());
        }
    }
}




for ($i = 1; $i <= 10; $i++) {
    $nombreForzado = $_POST["nombre_tort_forzado_$i"] ?? null;

    if ($nombreForzado) {
        try {
            $sqlM = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota) 
                     VALUES (:dueno, :nom, :fec, :sex, :tipo) RETURNING idmascota";
            $stmtM = $pdo->prepare($sqlM);
            $stmtM->execute([
                ':dueno' => $fk_id_dueno,
                ':nom'   => $nombreForzado,
                ':fec'   => $_POST["fecha_nacimiento_tort_forzado_$i"] ?: null,
                ':sex'   => $_POST["sexo_tort_forzado_$i"] ?: null,
                ':tipo'  => 'Tortuga'
            ]);
            $idMascota = $stmtM->fetchColumn();

            $sqlD = "INSERT INTO detalles_tortugas (
                fk_id_mascota, especie_tortuga, tamano_tortuga, clasificacion_tortuga,
                estatus_tortuga, fuente_calor_tortuga, alimentos_tortuga, 
                tratamientos_tortuga, veces_comida_tortuga, tipo_terrario_tortuga, 
                dimensiones_terrario_tortuga
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmtD = $pdo->prepare($sqlD);
            $stmtD->execute([
                $idMascota,
                $_POST["especie_tortuga_tort_forzado_$i"] ?: null,
                $_POST["tamano_tortuga_tort_forzado_$i"] ?: null,
                $_POST["clasificacion_tortuga_tort_forzado_$i"] ?: null,
                $_POST["estatus_tortuga_tort_forzado_$i"] ?: null,
                $_POST["fuente_calor_tortuga_tort_forzado_$i"] ?: null,
                $_POST["alimentos_tortuga_tort_forzado_$i"] ?: null,
                $_POST["tratamientos_tortuga_tort_forzado_$i"] ?: null,
                $_POST["veces_comida_tortuga_tort_forzado_$i"] ?: null,
                $_POST["tipo_terrario_tortuga_tort_forzado_$i"] ?: null,
                $_POST["dimensiones_terrario_tortuga_tort_forzado_$i"] ?: null
            ]);

            $vacunasSeleccionadas = $_POST["vacunas_tort_forzado_$i"] ?? [];
            foreach ($vacunasSeleccionadas as $idVacuna) {
                $fechaVacuna = $_POST["fecha_v_tort_forzado_{$idVacuna}_{$i}"] ?? null;
                if ($fechaVacuna) {
                    $sqlV = "INSERT INTO historial_vacunacion (fk_id_mascota, fk_id_vacuna, fecha_aplicacion) 
                             VALUES (?, ?, ?)";
                    $pdo->prepare($sqlV)->execute([$idMascota, $idVacuna, $fechaVacuna]);
                }
            }

        } catch (Exception $e) {
            error_log("Error guardando tortuga $i: " . $e->getMessage());
        }
    }
}
















    $pdo->commit();

    header("Location: ../vistas/perfil_usuario.php?msg=registro_exitoso");
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log(" ERROR CRÍTICO: " . $e->getMessage());
    error_log(" STACK TRACE: " . $e->getTraceAsString());
    
    die("Error crítico al guardar las mascotas: " . $e->getMessage());
}
?>