<?php
require 'conn.php';

header('Content-Type: application/json');

$result = $mysqli->query("
    SELECT 
        id, 
        name, 
        ST_X(location_point) AS lon, 
        ST_Y(location_point) AS lat, 
        location AS address, 
        marker_color, 
        region_id 
    FROM venues 
    WHERE location_point IS NOT NULL
");

$venues = [];

while ($row = $result->fetch_assoc()) {
    $venues[] = $row;
}

echo json_encode($venues);
?>
