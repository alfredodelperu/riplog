
<?php
// este es el archivo api.php
include "config.php";
header('Content-Type: application/json');

// Obtener parámetros de filtros
$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';
$filename = $_GET['filename'] ?? '';
$filenameLogic = $_GET['filenameLogic'] ?? 'or';
$pcs = $_GET['pcs'] ?? '';
$event = $_GET['event'] ?? '';
$limit = $_GET['limit'] ?? 1000; // Aumentar límite por defecto

// Construir condiciones WHERE
$whereConditions = [];
$params = [];
$paramCount = 1;

// Filtro de fechas
if ($dateFrom && $dateTo) {
    $whereConditions[] = "DATE(fecha) BETWEEN $" . $paramCount . " AND $" . ($paramCount + 1);
    $params[] = $dateFrom;
    $params[] = $dateTo;
    $paramCount += 2;
}

// Filtro de nombre de archivo
if ($filename) {
    $terms = array_map('trim', explode(',', $filename));
    $filenameConditions = [];
    
    foreach ($terms as $term) {
        if (!empty($term)) {
            $filenameConditions[] = "LOWER(archivo) LIKE $" . $paramCount;
            $params[] = '%' . strtolower($term) . '%';
            $paramCount++;
        }
    }
    
    if (!empty($filenameConditions)) {
        $connector = ($filenameLogic === 'and') ? ' AND ' : ' OR ';
        $whereConditions[] = '(' . implode($connector, $filenameConditions) . ')';
    }
}

// Filtro de PCs
if ($pcs) {
    $pcList = array_map('trim', explode(',', $pcs));
    $pcPlaceholders = [];
    
    foreach ($pcList as $pc) {
        if (!empty($pc)) {
            $pcPlaceholders[] = '$' . $paramCount;
            $params[] = $pc;
            $paramCount++;
        }
    }
    
    if (!empty($pcPlaceholders)) {
        $whereConditions[] = 'pc_name IN (' . implode(',', $pcPlaceholders) . ')';
    }
}

// Filtro de evento
if ($event) {
    $whereConditions[] = "evento = $" . $paramCount;
    $params[] = $event;
    $paramCount++;
}

// Construir consulta
$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

$query = "
    SELECT 
        id, 
        evento, 
        archivo, 
        tamano, 
        ancho, 
        largo, 
        copias, 
        fecha,
        hora,
        pc_name,
        sincronizado,
        secuenciado
    FROM riplog 
    $whereClause
    ORDER BY id DESC 
    LIMIT $limit
";

$result = pg_query_params($conn, $query, $params);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en la consulta: ' . pg_last_error($conn),
        'query' => $query,
        'params' => $params
    ]);
    exit;
}

$rows = [];
while ($row = pg_fetch_assoc($result)) {
    // Calcular ML Total según las reglas especificadas
    $ml_total = 0;
    if ($row['ancho'] && $row['largo'] && $row['copias']) {
        $ancho = (float)$row['ancho'];
        $largo = (float)$row['largo'];
        $copias = (int)$row['copias'];
        
        if ($ancho >= 60 || $largo >= 60) {
            $dimension = max($ancho, $largo);
        } else {
            $dimension = min($ancho, $largo);
        }
        
        $ml_total = ($dimension * $copias) / 100; // Convertir cm a metros
    }
    
    // Calcular M² Total
    $m2_total = 0;
    if ($row['ancho'] && $row['largo'] && $row['copias']) {
        $ancho = (float)$row['ancho'];
        $largo = (float)$row['largo'];
        $copias = (int)$row['copias'];
        
        $m2_total = ($ancho * $largo * $copias) / 10000; // Convertir cm² a m²
    }
    
    // Formatear los datos
    $rows[] = [
        'id' => (int)$row['id'],
        'evento' => $row['evento'],
        'archivo' => $row['archivo'],
        'tamano' => $row['tamano'],
        'ancho' => $row['ancho'] ? (float)$row['ancho'] : null,
        'largo' => $row['largo'] ? (float)$row['largo'] : null,
        'copias' => $row['copias'] ? (int)$row['copias'] : 1,
        'fecha' => $row['fecha'],
        'hora' => $row['hora'],
        'pc_name' => $row['pc_name'],
        'sincronizado' => $row['sincronizado'] ? (int)$row['sincronizado'] : 0,
        'secuenciado' => $row['secuenciado'] ? (int)$row['secuenciado'] : 0,
        'ml_total' => round($ml_total, 2),
        'm2_total' => round($m2_total, 2)
    ];
}

// Estadísticas con los mismos filtros aplicados
$statsQuery = "
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN evento = 'RIP' THEN 1 END) as rip_count,
        COUNT(CASE WHEN evento = 'PRINT' THEN 1 END) as print_count,
        SUM(CASE WHEN copias IS NOT NULL THEN copias ELSE 1 END) as total_copies,
        COUNT(DISTINCT pc_name) as unique_pcs,
        COUNT(CASE WHEN sincronizado = 1 THEN 1 END) as synchronized_count,
        COUNT(CASE WHEN secuenciado = 1 THEN 1 END) as sequenced_count,
        -- Cálculo de ML Total
        SUM(CASE 
            WHEN ancho IS NOT NULL AND largo IS NOT NULL AND copias IS NOT NULL THEN
                CASE 
                    WHEN ancho >= 60 OR largo >= 60 THEN
                        (GREATEST(ancho, largo) * copias) / 100
                    ELSE
                        (LEAST(ancho, largo) * copias) / 100
                END
            ELSE 0
        END) as ml_total,
        -- Cálculo de M² Total
        SUM(CASE 
            WHEN ancho IS NOT NULL AND largo IS NOT NULL AND copias IS NOT NULL THEN
                (ancho * largo * copias) / 10000
            ELSE 0
        END) as m2_total
    FROM riplog
    $whereClause
";

$stats_result = pg_query_params($conn, $statsQuery, $params);

if (!$stats_result) {
    // Si hay error en stats, continuar con datos básicos
    $stats = [
        'total' => count($rows),
        'rip_count' => count(array_filter($rows, fn($r) => $r['evento'] === 'RIP')),
        'print_count' => count(array_filter($rows, fn($r) => $r['evento'] === 'PRINT')),
        'total_copies' => array_sum(array_column($rows, 'copias')),
        'unique_pcs' => count(array_unique(array_column($rows, 'pc_name'))),
        'synchronized_count' => count(array_filter($rows, fn($r) => $r['sincronizado'] === 1)),
        'sequenced_count' => count(array_filter($rows, fn($r) => $r['secuenciado'] === 1)),
        'ml_total' => array_sum(array_column($rows, 'ml_total')),
        'm2_total' => array_sum(array_column($rows, 'm2_total'))
    ];
} else {
    $stats = pg_fetch_assoc($stats_result);
}

// Obtener lista de todas las PCs para los filtros
$pcQuery = "SELECT DISTINCT pc_name FROM riplog WHERE pc_name IS NOT NULL ORDER BY pc_name";
$pcResult = pg_query($conn, $pcQuery);
$pcs_list = [];
while ($pcRow = pg_fetch_assoc($pcResult)) {
    $pcs_list[] = $pcRow['pc_name'];
}

echo json_encode([
    'data' => $rows,
    'stats' => [
        'total' => (int)$stats['total'],
        'rip_count' => (int)$stats['rip_count'],
        'print_count' => (int)$stats['print_count'],
        'total_copies' => (int)$stats['total_copies'],
        'unique_pcs' => (int)$stats['unique_pcs'],
        'synchronized_count' => (int)$stats['synchronized_count'],
        'sequenced_count' => (int)$stats['sequenced_count'],
        'ml_total' => round((float)$stats['ml_total'], 2),
        'm2_total' => round((float)$stats['m2_total'], 2)
    ],
    'pcs_list' => $pcs_list,
    'filters_applied' => [
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'filename' => $filename,
        'filenameLogic' => $filenameLogic,
        'pcs' => $pcs,
        'event' => $event
    ]
]);

pg_close($conn);
?>