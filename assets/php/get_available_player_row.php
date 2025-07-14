<?php
require("conn.php");
session_start();

if (!isset($_GET['user_id']) || !isset($_GET['venue_id'])) {
    die("Missing parameters.");
}

$user_id = intval($_GET['user_id']);
$venue_id = intval($_GET['venue_id']);

// Fetch player data
$sql = "SELECT u.id, u.first_name, u.last_name, u.username
        FROM player_venues pv
        JOIN users u ON pv.user_id = u.id
        WHERE pv.venue_id = ? AND u.id = ?
        LIMIT 1";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $venue_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Player not found.");
}

$row = $result->fetch_assoc();
$username = htmlspecialchars($row['username']);
$fullName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);

// Return clean row HTML
echo "<li class='list-group-item player-item d-flex' data-userid='{$row['id']}'>
        <div class='flex-grow-1'>{$fullName} ({$username})</div>
      </li>";

$stmt->close();
?>
