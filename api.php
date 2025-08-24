<?php
include "config.php";
header('Content-Type: application/json');

$result = pg_query($conn, "SELECT id, evento, archivo, fecha FROM riplog ORDER BY id DESC LIMIT 50");
$rows = [];
while ($row = pg_fetch_assoc($result)) {
    $rows[] = $row;
}
echo json_encode($rows);
?>
