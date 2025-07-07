<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['venue_id'])) {
    header("Location: ../../dashboard/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$venue_id = (int)$_POST['venue_id'];

// Prevent duplicates
$check = $mysqli->prepare("SELECT * FROM player_venues WHERE user_id = ? AND venue_id = ?");
$check->bind_param("ii", $user_id, $venue_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $stmt = $mysqli->prepare("INSERT INTO player_venues (user_id, venue_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $venue_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../../dashboard/index.php");
exit;
?>
