<?php
// export.php - Versión final con soporte PDF real (TCPDF)
include "config.php";

// Incluir TCPDF
require_once 'tcpdf/tcpdf.php';

// Obtener parámetros
$dateFrom = $_GET['dateFrom'] ?? '';
$dateTo = $_GET['dateTo'] ?? '';
$filename = $_GET['filename'] ?? '';
$filenameLogic = $_GET['filenameLogic'] ?? 'or';
$pcs = $_GET['pcs'] ?? '';
$event = $_GET['event'] ?? '';
$format = $_GET['format'] ?? 'excel';
$selected = $_GET['selected'] ?? '';

// Construir condiciones WHERE
$whereConditions = [];
$params = [];
$paramCount = 1;

if ($dateFrom && $dateTo) {
    $dateFromFull = $dateFrom . ' 00:00:00';
    $dateToFull = $dateTo . ' 23:59:59';
    $whereConditions[] = "fecha BETWEEN $" . $paramCount . " AND $" . ($paramCount + 1);
    $params[] = $dateFromFull;
    $params[] = $dateToFull;
    $paramCount += 2;
}

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

if ($event) {
    $whereConditions[] = "evento = $" . $paramCount;
    $params[] = $event;
    $paramCount++;
}

// Si se especifican registros seleccionados
if ($selected) {
    $selectedIds = explode(',', $selected);
    $idPlaceholders = [];
    foreach ($selectedIds as $id) {
        if (is_numeric($id)) {
            $idPlaceholders[] = '$' . $paramCount;
            $params[] = (int)$id;
            $paramCount++;
        }
    }
    if (!empty($idPlaceholders)) {
        $whereConditions[] = 'id IN (' . implode(',', $idPlaceholders) . ')';
    }
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Consulta para exportar (SIN LIMIT)
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
    ORDER BY fecha DESC, hora DESC
";

$result = pg_query_params($conn, $query, $params);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en consulta: ' . pg_last_error($conn)]);
    exit;
}

$rows = [];
while ($row = pg_fetch_assoc($result)) {
    $ml_total = 0;
    $m2_total = 0;
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
        $m2_total = ($ancho * $largo * $copias) / 10000;
    }

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

// Generar archivo según formato
switch ($format) {
    case 'csv':
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="riplog_export.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'ID', 'Evento', 'Archivo', 'Tamaño', 'Ancho (cm)', 'Largo (cm)', 'Copias', 'Fecha', 'Hora', 'PC', 'Sincronizado', 'Secuenciado', 'ML Total', 'M² Total'
        ]);
        foreach ($rows as $row) {
            fputcsv($output, [
                $row['id'],
                $row['evento'],
                $row['archivo'],
                $row['tamano'],
                $row['ancho'],
                $row['largo'],
                $row['copias'],
                $row['fecha'],
                $row['hora'],
                $row['pc_name'],
                $row['sincronizado'],
                $row['secuenciado'],
                $row['ml_total'],
                $row['m2_total']
            ]);
        }
        fclose($output);
        exit;

    case 'excel':
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="riplog_export.xlsx"');
        echo "<table border='1'>
            <thead>
                <tr>
                    <th>ID</th><th>Evento</th><th>Archivo</th><th>Tamaño</th><th>Ancho</th><th>Largo</th><th>Copias</th><th>Fecha</th><th>Hora</th><th>PC</th><th>Sincronizado</th><th>Secuenciado</th><th>ML Total</th><th>M² Total</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($rows as $row) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['evento']}</td>
                <td>" . htmlspecialchars($row['archivo']) . "</td>
                <td>{$row['tamano']}</td>
                <td>{$row['ancho']}</td>
                <td>{$row['largo']}</td>
                <td>{$row['copias']}</td>
                <td>{$row['fecha']}</td>
                <td>{$row['hora']}</td>
                <td>{$row['pc_name']}</td>
                <td>{$row['sincronizado']}</td>
                <td>{$row['secuenciado']}</td>
                <td>{$row['ml_total']}</td>
                <td>{$row['m2_total']}</td>
            </tr>";
        }
        echo "</tbody></table>";
        exit;

    case 'pdf':
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Exportación RipLog');
        $pdf->SetAuthor('Dashboard RipLog');
        $pdf->SetHeaderData('', 0, 'Exportación de Registros RipLog', '');
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->AddPage();

        $html = '
            <table border="1" cellpadding="4" style="font-size: 8pt; width: 100%;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="width: 5%;">ID</th>
                        <th style="width: 15%;">Evento</th>
                        <th style="width: 25%;">Archivo</th>
                        <th style="width: 10%;">Ancho</th>
                        <th style="width: 10%;">Largo</th>
                        <th style="width: 8%;">Copias</th>
                        <th style="width: 10%;">Fecha</th>
                        <th style="width: 10%;">PC</th>
                        <th style="width: 7%;">ML Total</th>
                        <th style="width: 7%;">M² Total</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>
                <td>' . $row['id'] . '</td>
                <td>' . htmlspecialchars($row['evento']) . '</td>
                <td>' . htmlspecialchars($row['archivo']) . '</td>
                <td>' . ($row['ancho'] ?: '-') . '</td>
                <td>' . ($row['largo'] ?: '-') . '</td>
                <td>' . $row['copias'] . '</td>
                <td>' . $row['fecha'] . ' ' . $row['hora'] . '</td>
                <td>' . htmlspecialchars($row['pc_name']) . '</td>
                <td>' . $row['ml_total'] . '</td>
                <td>' . $row['m2_total'] . '</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('riplog_export.pdf', 'D');
        exit;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Formato no soportado']);
        exit;
}
?>