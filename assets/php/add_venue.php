<?php
require "session_check.php";
$conn = require 'conn.php';

// Toast helper function
function setToast($type, $message) {
    $_SESSION['toast'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    setToast('danger', 'You must be logged in.');
    header("Location: ../../dashboard/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate required POST fields
$required_fields = ['region_id', 'name', 'location', 'latitude', 'longitude'];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        setToast('danger', 'All fields are required.');
        header("Location: ../../dashboard/index.php");
        exit;
    }
}

// Sanitize inputs
$region_id = (int)$_POST['region_id'];
$name = trim($_POST['name']);
$location = trim($_POST['location']);
$marker_color = isset($_POST['marker_color']) ? trim($_POST['marker_color']) : 'blue';
$latitude = (float)$_POST['latitude'];
$longitude = (float)$_POST['longitude'];

// Check if user is a regional manager for this region
$checkStmt = $conn->prepare("SELECT id FROM user_region_roles WHERE user_id = ? AND region_id = ?");
$checkStmt->bind_param("ii", $user_id, $region_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    setToast('danger', 'You don’t have permission for this region.');
    header("Location: ../../dashboard/index.php");
    exit;
}
$checkStmt->close();

// Prepare point for MySQL POINT data type as WKT string
$location_point = sprintf('POINT(%F %F)', $longitude, $latitude);

// Insert venue
$stmt = $conn->prepare("INSERT INTO venues (name, location, region_id, location_point, marker_color, created_at) VALUES (?, ?, ?, ST_GeomFromText(?), ?, NOW())");
$stmt->bind_param("ssiss", $name, $location, $region_id, $location_point, $marker_color);

if ($stmt->execute()) {
    setToast('success', 'Venue added successfully!');
} else {
    setToast('danger', 'Failed to add venue.');
}

$stmt->close();
$conn->close();

header("Location: ../../dashboard/index.php");
exit;
?>
