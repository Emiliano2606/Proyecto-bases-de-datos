<?php
// 1. INICIAR SESIÓN Y CONEXIÓN
session_start();

// ==============================================
// DEBUG COMPLETO - QUÉ DATOS ESTÁN LLEGANDO actuallllllllllllllll
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


   if (isset($_POST['nombre_perro'])) {
        $nombresPerros = (array)$_POST['nombre_perro'];

        foreach ($nombresPerros as $index => $nombre) {
            if (empty(trim($nombre))) continue;

            $sufijo = ($index === 0) ? "" : "_" . ($index + 1);

            // A. INSERTAR EN TABLA MASCOTAS
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

            // B. INSERTAR EN DETALLES_PERROS
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

            // C. VACUNAS PERROS
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

  // 5. PROCESAR GATOS
// 5. PROCESAR GATOS
// ==============================================
// 5. PROCESAR GATOS - VERSIÓN CORREGIDA COMPLETA
// ==============================================

error_log("=== INICIANDO PROCESAMIENTO GATOS ===");

// Método 1: Buscar campos forzados por JavaScript (PRIMERO - MÁS SEGURO)
$todosLosGatos = [];

// Buscar campos forzados (nombre_gato_forzado_1, nombre_gato_forzado_2, etc.)
for ($i = 1; $i <= 10; $i++) {
    $campoForzado = 'nombre_gato_forzado_' . $i;
    if (isset($_POST[$campoForzado]) && !empty(trim($_POST[$campoForzado]))) {
        $nombreLimpio = trim($_POST[$campoForzado]);
        $todosLosGatos[] = [
            'nombre' => $nombreLimpio,
            'indice' => $i - 1, // Empieza en 0
            'tipo' => 'forzado',
            'campo_origen' => $campoForzado
        ];
        error_log("✅ Gato forzado $i: '$nombreLimpio'");
    }
}

// Método 2: Buscar en array tradicional (segundo)
if (isset($_POST['nombre_gato'])) {
    $nombre_gato_data = $_POST['nombre_gato'];
    
    if (is_array($nombre_gato_data)) {
        foreach ($nombre_gato_data as $index => $nombre) {
            $nombreLimpio = trim($nombre);
            if (!empty($nombreLimpio)) {
                // Verificar si ya existe en los forzados
                $existe = false;
                foreach ($todosLosGatos as $gato) {
                    if ($gato['nombre'] === $nombreLimpio) {
                        $existe = true;
                        break;
                    }
                }
                
                if (!$existe) {
                    $todosLosGatos[] = [
                        'nombre' => $nombreLimpio,
                        'indice' => $index,
                        'tipo' => 'array_tradicional',
                        'campo_origen' => 'nombre_gato[]'
                    ];
                    error_log("✅ Gato array tradicional [$index]: '$nombreLimpio'");
                }
            }
        }
    } else {
        // Si es string (solo un gato)
        $nombreLimpio = trim($nombre_gato_data);
        if (!empty($nombreLimpio)) {
            $todosLosGatos[] = [
                'nombre' => $nombreLimpio,
                'indice' => 0,
                'tipo' => 'string_tradicional',
                'campo_origen' => 'nombre_gato'
            ];
            error_log("✅ Gato string tradicional: '$nombreLimpio'");
        }
    }
}

// Método 3: Buscar campos individuales (nombre_gato_2, nombre_gato_3, etc.)
for ($i = 2; $i <= 10; $i++) {
    $campoIndividual = "nombre_gato_$i";
    if (isset($_POST[$campoIndividual]) && !empty(trim($_POST[$campoIndividual]))) {
        $nombreLimpio = trim($_POST[$campoIndividual]);
        
        // Verificar si ya existe
        $existe = false;
        foreach ($todosLosGatos as $gato) {
            if ($gato['nombre'] === $nombreLimpio) {
                $existe = true;
                break;
            }
        }
        
        if (!$existe) {
            $todosLosGatos[] = [
                'nombre' => $nombreLimpio,
                'indice' => $i - 1,
                'tipo' => 'individual',
                'campo_origen' => $campoIndividual
            ];
            error_log("✅ Gato individual $i: '$nombreLimpio'");
        }
    }
}

// Ordenar por índice para mantener el orden
usort($todosLosGatos, function($a, $b) {
    return $a['indice'] - $b['indice'];
});

error_log("📊 Total gatos encontrados (todos métodos): " . count($todosLosGatos));

if (empty($todosLosGatos)) {
    error_log("⚠️ NO SE ENCONTRARON GATOS PARA PROCESAR");
} else {
    // Procesar cada gato encontrado
    foreach ($todosLosGatos as $gato) {
        $nombreLimpio = $gato['nombre'];
        $indice = $gato['indice'];
        $tipo = $gato['tipo'];
        
        error_log("\n✅ PROCESANDO GATO " . ($indice + 1) . ":");
        error_log("   Nombre: '$nombreLimpio'");
        error_log("   Índice: $indice");
        error_log("   Tipo: $tipo");
        error_log("   Campo origen: " . $gato['campo_origen']);
        
        // Determinar sufijo
        $sufijo = ($indice === 0) ? "" : "_" . ($indice + 1);
        error_log("   Sufijo asignado: '$sufijo'");
        
        // ====================
        // A. INSERTAR MASCOTA
        // ====================
        $sqlMascotaG = "INSERT INTO mascotas (fk_id_dueno, nombre, fecha_nacimiento, sexo, tipo_mascota, foto_url)
                        VALUES (:dueno, :nom, :fecha, :sexo, :tipo, :foto) RETURNING idmascota";
        $stmtMascotaG = $pdo->prepare($sqlMascotaG);
        
        // Buscar campos con el sufijo correcto
        $campoFecha = "fecha_nacimiento_gato" . $sufijo;
        $campoSexo = "sexo_gato" . $sufijo;
        
        // Si no encontramos con sufijo, buscar sin sufijo
        $fecha_v_g = null;
        $sexo_v_g = null;
        
        if (isset($_POST[$campoFecha]) && !empty(trim($_POST[$campoFecha]))) {
            $fecha_v_g = $_POST[$campoFecha];
        } else if ($sufijo !== '' && isset($_POST["fecha_nacimiento_gato"]) && !empty(trim($_POST["fecha_nacimiento_gato"]))) {
            // Usar el campo sin sufijo como alternativa
            $fecha_v_g = $_POST["fecha_nacimiento_gato"];
        }
        
        if (isset($_POST[$campoSexo]) && !empty(trim($_POST[$campoSexo]))) {
            $sexo_v_g = $_POST[$campoSexo];
        } else if ($sufijo !== '' && isset($_POST["sexo_gato"]) && !empty(trim($_POST["sexo_gato"]))) {
            // Usar el campo sin sufijo como alternativa
            $sexo_v_g = $_POST["sexo_gato"];
        }
        
        error_log("   Campo fecha: '$campoFecha' = " . ($fecha_v_g ?: 'null'));
        error_log("   Campo sexo: '$campoSexo' = " . ($sexo_v_g ?: 'null'));
        
        $stmtMascotaG->execute([
            ':dueno' => $fk_id_dueno,
            ':nom'   => $nombreLimpio,
            ':fecha' => $fecha_v_g,
            ':sexo'  => $sexo_v_g,
            ':tipo'  => 'Gato',
            ':foto'  => NULL
        ]);
        
        $idGato = $stmtMascotaG->fetchColumn();
        error_log("   ✅ Gato insertado con ID: $idGato");
        
        // ====================
        // B. INSERTAR DETALLES
        // ====================
        $sqlDetGato = "INSERT INTO detalles_gatos (
            fk_id_mascota, raza_gato, grupo_gato, registro_principal_gato, 
            tamano_gato, peso_gato, tipo_pelaje_especifico, caracteristicas_fisicas, 
            color_principal, color_secundario, tipo_pelo, patron_pelo, convive_animales, 
            tipo_alimento, marca_alimento, veces_come_dia, tratamientos_recibidos, 
            tiene_ruac, ruac_valor, tiene_chip, numero_chip_gato, tipo_chip_gato
        ) VALUES (
            :id, :raza, :grupo, :reg, :tam, :peso, :t_pelaje, :fisic, :c1, :c2, 
            :tpelo, :patron, :convive, :alim, :marca, :veces, :trat, :t_ruac, 
            :r_val, :t_chip, :n_chip, :tipo_chip
        )";
        
        $stmtDetG = $pdo->prepare($sqlDetGato);
        
        // Construir todos los nombres de campo con sufijo
        $camposDetalles = [
            'raza_del_gato' => ':raza',
            'grupo_gato' => ':grupo',
            'registro_principal_gato' => ':reg',
            'tamano_gato' => ':tam',
            'peso_gato' => ':peso',
            'tipopelaje_gato' => ':t_pelaje',
            'caracterfisicas_gato' => ':fisic',
            'color_principal_gato' => ':c1',
            'color_secundario_gato' => ':c2',
            'tipo_pelo_gato' => ':tpelo',
            'patron_pelo_gato' => ':patron',
            'convive_gato' => ':convive',
            'alimento_gato' => ':alim',
            'marca_alimento_gato' => ':marca',
            'veces_comida_gato' => ':veces',
            'tratamientos_gatito' => ':trat',
            'tiene_ruac_gato' => ':t_ruac',
            'ruac_gato' => ':r_val',
            'chip_del_gato' => ':t_chip',
            'numero_chip_gato' => ':n_chip',
            'tipo_chip_gato' => ':tipo_chip'
        ];
        
        $valoresDetalles = [':id' => $idGato];
        
        foreach ($camposDetalles as $campoBase => $placeholder) {
            $campoCompleto = $campoBase . $sufijo;
            $valor = null;
            
            // Primero buscar con sufijo
            if (isset($_POST[$campoCompleto]) && !empty(trim($_POST[$campoCompleto]))) {
                $valor = $_POST[$campoCompleto];
            } 
            // Si no hay sufijo o no se encontró con sufijo, buscar sin sufijo
            else if ($sufijo !== '' && isset($_POST[$campoBase]) && !empty(trim($_POST[$campoBase]))) {
                $valor = $_POST[$campoBase];
            }
            
            $valoresDetalles[$placeholder] = $valor;
            
            if ($valor) {
                error_log("   Campo '$campoCompleto' = '$valor'");
            }
        }
        
        $stmtDetG->execute($valoresDetalles);
        error_log("   ✅ Detalles insertados para gato $idGato");
        
        // ====================
        // C. PROCESAR VACUNAS
        // ====================
        $nombreClaveVacunas = "vacunas_gato" . $sufijo;
        error_log("   Buscando vacunas con clave: '$nombreClaveVacunas'");
        
        // También buscar vacunas sin sufijo como alternativa
        if (isset($_POST[$nombreClaveVacunas]) && is_array($_POST[$nombreClaveVacunas])) {
            $vacunasSeleccionadas = $_POST[$nombreClaveVacunas];
        } else if ($sufijo !== '' && isset($_POST['vacunas_gato']) && is_array($_POST['vacunas_gato'])) {
            // Usar vacunas sin sufijo como alternativa
            $vacunasSeleccionadas = $_POST['vacunas_gato'];
            $nombreClaveVacunas = 'vacunas_gato';
            error_log("   Usando vacunas sin sufijo como alternativa");
        } else {
            $vacunasSeleccionadas = [];
        }
        
        if (!empty($vacunasSeleccionadas)) {
            $vacunasCount = count($vacunasSeleccionadas);
            
            error_log("   ✅ Vacunas encontradas: $vacunasCount (" . implode(', ', $vacunasSeleccionadas) . ")");
            
            $vacunasInsertadas = 0;
            foreach ($vacunasSeleccionadas as $idVacuna) {
                $campoFecha = "fecha_{$idVacuna}{$sufijo}";
                $fechaEncontrada = $_POST[$campoFecha] ?? null;
                
                // Si no se encuentra con sufijo, buscar sin sufijo
                if (empty($fechaEncontrada) && $sufijo !== '') {
                    $campoFechaSinSufijo = "fecha_{$idVacuna}";
                    $fechaEncontrada = $_POST[$campoFechaSinSufijo] ?? null;
                }
                
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
                        
                        $vacunasInsertadas++;
                        error_log("     ✅ Vacuna $idVacuna insertada (fecha: $fechaEncontrada)");
                    } catch (PDOException $e) {
                        error_log("     ❌ Error vacuna $idVacuna: " . $e->getMessage());
                    }
                } else {
                    error_log("     ⚠️ Vacuna $idVacuna sin fecha (buscó en: fecha_{$idVacuna}{$sufijo})");
                }
            }
            
            error_log("   Total vacunas insertadas: $vacunasInsertadas/$vacunasCount");
        } else {
            error_log("   ⚠️ No hay vacunas seleccionadas para este gato");
        }
        
        error_log("--- Gato '$nombreLimpio' procesado correctamente ---\n");
    }
}

if (isset($_POST['tipo_mascota']) && $_POST['tipo_mascota'] === 'Ave') {
// ==============================================
// 6. PROCESAR AVES - VERSIÓN CORREGIDA PARA MÚLTIPLES AVES
// ==============================================

error_log("=== INICIANDO PROCESAMIENTO AVES ===");

// FUNCIÓN AUXILIAR - DECLARADA UNA SOLA VEZ FUERA DEL BUCLE
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

// 1. BUSCAR TODAS LAS AVES
$todosLasAves = [];

// Método 1: Buscar campos forzados
for ($i = 1; $i <= 10; $i++) {
    $campoForzado = 'nombre_ave_forzado_' . $i;
    if (isset($_POST[$campoForzado]) && !empty(trim($_POST[$campoForzado]))) {
        $nombreLimpio = trim($_POST[$campoForzado]);
        $todosLasAves[] = [
            'nombre' => $nombreLimpio,
            'indice' => $i - 1,
            'tipo' => 'forzado'
        ];
        error_log("✅ Ave forzada $i: '$nombreLimpio'");
    }
}

// Método 2: Buscar primera ave (sin sufijo)
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
        error_log("✅ Ave 1 (nombre_mascota): '$nombreLimpio'");
    }
}

// Método 3: Buscar aves adicionales (con sufijo _2, _3, etc.)
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
            error_log("✅ Ave $i ($campoAve): '$nombreLimpio'");
        }
    }
}

// Ordenar por índice
usort($todosLasAves, function($a, $b) {
    return $a['indice'] - $b['indice'];
});

error_log("📊 Total aves encontradas: " . count($todosLasAves));

if (empty($todosLasAves)) {
    error_log("⚠️ NO SE ENCONTRARON AVES PARA PROCESAR");
} else {
    // Procesar cada ave
    foreach ($todosLasAves as $ave) {
        $nombreLimpio = $ave['nombre'];
        $indice = $ave['indice'];
        
        // Determinar sufijo
        $sufijo = ($indice === 0) ? "" : "_" . ($indice + 1);
        
        error_log("\n✅ PROCESANDO AVE " . ($indice + 1) . " '$nombreLimpio' (sufijo: '$sufijo')");
        
        try {
            // ====================
            // A. OBTENER DATOS BÁSICOS
            // ====================
            $fecha_v_a = obtenerDatoAve("fecha_nacimiento_ave", $sufijo);
            $sexo_v_a = obtenerDatoAve("sexo_ave", $sufijo);
            
            // ====================
            // B. INSERTAR MASCOTA
            // ====================
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
            error_log("   ✅ Ave insertada con ID: $idAve");
            
            // ====================
            // C. INSERTAR DETALLES
            // ====================
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
            
            // Obtener todos los datos específicos de ESTA ave (con su sufijo)
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
            error_log("   ✅ Detalles insertados para ave $idAve");
            
            // ====================
            // D. PROCESAR VACUNAS
            // ====================
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
                        
                        error_log("     ✅ Vacuna $idVacuna insertada: $fechaVacuna");
                    } else {
                        error_log("     ⚠️ Vacuna $idVacuna sin fecha ($campoFecha)");
                    }
                }
            } else {
                error_log("   ℹ️ No hay vacunas para esta ave ($nombreClaveVacunas)");
            }
            
            error_log("--- Ave '$nombreLimpio' procesada correctamente ---\n");
            
        } catch (PDOException $e) {
            error_log("❌ ERROR procesando ave '$nombreLimpio': " . $e->getMessage());
        }
    }
}

error_log("=== FIN PROCESAMIENTO AVES ===");
error_log("Total aves procesadas exitosamente: " . count($todosLasAves));
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