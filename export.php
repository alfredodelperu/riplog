<?php
include "config.php";

$format = $_GET['format'] ?? 'csv';
$selectedIds = $_GET['selected'] ?? '';
$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';
$filename = $_GET['filename'] ?? '';
$filenameLogic = $_GET['filenameLogic'] ?? 'or';
$pcs = $_GET['pcs'] ?? '';
$event = $_GET['event'] ?? '';

// Construir condiciones WHERE (igual que en api.php)
$whereConditions = [];
$params = [];
$paramCount = 1;

// Si hay IDs seleccionados, usarlos
if ($selectedIds) {
    $ids = array_map('intval', explode(',', $selectedIds));
    $idPlaceholders = [];
    
    foreach ($ids as $id) {
        $idPlaceholders[] = '$' . $paramCount;
        $params[] = $id;
        $paramCount++;
    }
    
    $whereConditions[] = 'id IN (' . implode(',', $idPlaceholders) . ')';
} else {
    // Aplicar filtros normales si no hay selección específica
    
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
}

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
        pc_name
    FROM riplog 
    $whereClause
    ORDER BY id DESC
";

$result = pg_query_params($conn, $query, $params);

if (!$result) {
    http_response_code(500);
    die('Error en la consulta: ' . pg_last_error($conn));
}

$data = [];
while ($row = pg_fetch_assoc($result)) {
    // Calcular ML Total
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
        
        $ml_total = ($dimension * $copias) / 100;
    }
    
    // Calcular M² Total
    $m2_total = 0;
    if ($row['ancho'] && $row['largo'] && $row['copias']) {
        $ancho = (float)$row['ancho'];
        $largo = (float)$row['largo'];
        $copias = (int)$row['copias'];
        
        $m2_total = ($ancho * $largo * $copias) / 10000;
    }
    
    $data[] = [
        'ID' => $row['id'],
        'Evento' => $row['evento'],
        'Archivo' => $row['archivo'],
        'Tamaño' => $row['tamano'],
        'Ancho (cm)' => $row['ancho'],
        'Largo (cm)' => $row['largo'],
        'Copias' => $row['copias'] ?: 1,
        'ML Total (m)' => round($ml_total, 2),
        'M² Total' => round($m2_total, 2),
        'PC' => $row['pc_name'],
        'Fecha' => $row['fecha'],
        'Hora' => $row['hora']
    ];
}

$timestamp = date('Y-m-d_H-i-s');
$filenamePrefix = $selectedIds ? 'impresiones_seleccionadas' : 'impresiones_filtradas';

switch ($format) {
    case 'csv':
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filenamePrefix . '_' . $timestamp . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fwrite($output, "\xEF\xBB\xBF");
        
        if (!empty($data)) {
            // Encabezados
            fputcsv($output, array_keys($data[0]), ';');
            
            // Datos
            foreach ($data as $row) {
                fputcsv($output, $row, ';');
            }
        }
        
        fclose($output);
        break;
        
    case 'excel':
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filenamePrefix . '_' . $timestamp . '.xls"');
        
        echo "\xEF\xBB\xBF"; // BOM para UTF-8
        echo "<table border='1'>";
        
        if (!empty($data)) {
            // Encabezados
            echo "<tr>";
            foreach (array_keys($data[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            
            // Datos
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
        }
        
        echo "</table>";
        break;
        
    case 'pdf':
        // Para PDF, necesitarías una librería como TCPDF o FPDF
        // Por simplicidad, genero HTML que se puede imprimir como PDF
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filenamePrefix . '_' . $timestamp . '.html"');
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Reporte de Impresiones</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; }
                h1 { color: #333; }
                .summary { margin: 20px 0; padding: 15px; background-color: #f9f9f9; }
            </style>
        </head>
        <body>
            <h1>📊 Reporte de Impresiones Full Color</h1>
            <div class="summary">
                <strong>Generado:</strong> ' . date('d/m/Y H:i:s') . '<br>
                <strong>Total de registros:</strong> ' . count($data) . '<br>
                <strong>Tipo:</strong> ' . ($selectedIds ? 'Registros seleccionados' : 'Filtros aplicados') . '
            </div>
            <table>';
        
        if (!empty($data)) {
            // Encabezados
            echo "<tr>";
            foreach (array_keys($data[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            
            // Datos
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
        }
        
        echo '</table>
        </body>
        </html>';
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Formato no válido']);
        break;
}

pg_close($conn);
?>