<?php
include "config.php";

$result = pg_query($conn, "SELECT * FROM riplog LIMIT 10");
while ($row = pg_fetch_assoc($result)) {
    echo $row['id'] . " - " . $row['evento'] . "<br>";
}
?>
