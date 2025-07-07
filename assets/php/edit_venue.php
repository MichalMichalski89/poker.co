<?php
require "session_check.php";
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['toast'] = [
        'type' => 'danger',
        'message' => 'You must be logged in.'
    ];
    header("Location: ../../dashboard/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate required fields
$required = ['id', 'region_id', 'name', 'location', 'latitude', 'longitude'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['toast'] = [
            'type' => 'danger',
            'message' => 'All fields are required.'
        ];
        header("Location: ../../dashboard/index.php");
        exit;
    }
}

$venue_id = (int)$_POST['id'];
$region_id = (int)$_POST['region_id'];
$name = trim($_POST['name']);
$location = trim($_POST['location']);
$marker_color = trim($_POST['marker_color']);
$latitude = (float)$_POST['latitude'];
$longitude = (float)$_POST['longitude'];

$location_point = sprintf('POINT(%F %F)', $longitude, $latitude);

// Check permission
$checkStmt = $mysqli->prepare("
    SELECT venues.id 
    FROM venues 
    INNER JOIN user_region_roles 
        ON venues.region_id = user_region_roles.region_id
    WHERE venues.id = ? AND user_region_roles.user_id = ?
");
$checkStmt->bind_param("ii", $venue_id, $user_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    $_SESSION['toast'] = [
        'type' => 'danger',
        'message' => 'You do not have permission to edit this venue.'
    ];
    $checkStmt->close();
    header("Location: ../../dashboard/index.php");
    exit;
}
$checkStmt->close();

// Update venue
$updateStmt = $mysqli->prepare("
    UPDATE venues 
    SET name = ?, location = ?, region_id = ?, location_point = ST_GeomFromText(?), marker_color = ? 
    WHERE id = ?
");
$updateStmt->bind_param("ssissi", $name, $location, $region_id, $location_point, $marker_color, $venue_id);

if ($updateStmt->execute()) {
    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Venue updated successfully.'
    ];
} else {
    $_SESSION['toast'] = [
        'type' => 'danger',
        'message' => 'Failed to update venue.'
    ];
}
$updateStmt->close();

header("Location: ../../dashboard/index.php");
exit;
?>
