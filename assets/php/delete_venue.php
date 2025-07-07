<?php
require "session_check.php";
require 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['toast'] = [
        'type' => 'danger',
        'message' => 'You must be logged in.'
    ];
    header("Location: ../../dashboard/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate POST data
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $_SESSION['toast'] = [
        'type' => 'danger',
        'message' => 'Invalid venue ID.'
    ];
    header("Location: ../../dashboard/index.php");
    exit;
}

$venue_id = (int)$_POST['id'];

// Check if the user has permission to delete this venue
// (i.e. user must be a manager for the venue’s region)

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
        'message' => 'You do not have permission to delete this venue.'
    ];
    $checkStmt->close();
    header("Location: ../../dashboard/index.php");
    exit;
}
$checkStmt->close();

// Delete the venue
$deleteStmt = $mysqli->prepare("DELETE FROM venues WHERE id = ?");
$deleteStmt->bind_param("i", $venue_id);

if ($deleteStmt->execute()) {
    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Venue deleted successfully.'
    ];
} else {
    $_SESSION['toast'] = [
        'type' => 'danger',
        'message' => 'Failed to delete venue.'
    ];
}
$deleteStmt->close();

header("Location: ../../dashboard/index.php");
exit;
?>
