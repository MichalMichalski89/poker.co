<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['venue_id'])) {
    header("Location: ../../dashboard/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$venue_id = (int)$_POST['venue_id'];

$stmt = $mysqli->prepare("DELETE FROM player_venues WHERE user_id = ? AND venue_id = ?");
$stmt->bind_param("ii", $user_id, $venue_id);
$stmt->execute();
$stmt->close();

header("Location: ../../dashboard/index.php");
exit;
?>
