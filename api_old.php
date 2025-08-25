<?php
include "config.php";
header('Content-Type: application/json');

// Consulta con todas las columnas
$result = pg_query($conn, "
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
    ORDER BY id DESC 
    LIMIT 100
");

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error en la consulta: ' . pg_last_error($conn)
    ]);
    exit;
}

$rows = [];
while ($row = pg_fetch_assoc($result)) {
    // Formatear los datos
    $rows[] = [
        'id' => (int)$row['id'],
        'evento' => $row['evento'],
        'archivo' => $row['archivo'],
        'tamano' => $row['tamano'],
        'ancho' => $row['ancho'] ? (float)$row['ancho'] : null,
        'largo' => $row['largo'] ? (float)$row['largo'] : null,
        'copias' => $row['copias'] ? (int)$row['copias'] : null,
        'fecha' => $row['fecha'],
        'hora' => $row['hora'],
        'pc_name' => $row['pc_name'],
        'sincronizado' => $row['sincronizado'] ? (int)$row['sincronizado'] : 0,
        'secuenciado' => $row['secuenciado'] ? (int)$row['secuenciado'] : 0
    ];
}

// Estadísticas adicionales
$stats_result = pg_query($conn, "
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN evento = 'RIP' THEN 1 END) as rip_count,
        COUNT(CASE WHEN evento = 'PRINT' THEN 1 END) as print_count,
        COUNT(CASE WHEN DATE(fecha) = CURRENT_DATE THEN 1 END) as today_count,
        SUM(CASE WHEN copias IS NOT NULL THEN copias ELSE 1 END) as total_copies,
        AVG(CASE WHEN ancho IS NOT NULL AND largo IS NOT NULL THEN ancho * largo END) as avg_area,
        COUNT(DISTINCT pc_name) as unique_pcs,
        COUNT(CASE WHEN sincronizado = 1 THEN 1 END) as synchronized_count,
        COUNT(CASE WHEN secuenciado = 1 THEN 1 END) as sequenced_count
    FROM riplog
");

$stats = pg_fetch_assoc($stats_result);

echo json_encode([
    'data' => $rows,
    'stats' => [
        'total' => (int)$stats['total'],
        'rip_count' => (int)$stats['rip_count'],
        'print_count' => (int)$stats['print_count'],
        'today_count' => (int)$stats['today_count'],
        'total_copies' => (int)$stats['total_copies'],
        'avg_area' => $stats['avg_area'] ? round((float)$stats['avg_area'], 2) : 0,
        'unique_pcs' => (int)$stats['unique_pcs'],
        'synchronized_count' => (int)$stats['synchronized_count'],
        'sequenced_count' => (int)$stats['sequenced_count']
    ]
]);

pg_close($conn);
?>