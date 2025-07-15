<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conn.php';

header('Content-Type: application/json');

$sql = "
    SELECT 
        id, 
        name, 
        longitude AS lon, 
        latitude AS lat, 
        location AS address, 
        marker_color, 
        region_id 
    FROM venues 
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
";

$result = $mysqli->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => "SQL error: " . $mysqli->error]);
    exit;
}

$venues = [];

while ($row = $result->fetch_assoc()) {
    $venues[] = $row;
}

echo json_encode($venues);
