<?php
require("conn.php");

if (!isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    echo json_encode(['available' => '', 'attending' => '']);
    exit;
}

$game_id = (int)$_GET['game_id'];

// Get venue_id for this game
$stmt = $mysqli->prepare("SELECT venue_id FROM game_events WHERE id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$stmt->bind_result($venue_id);
$stmt->fetch();
$stmt->close();

$available = '';
$attending = '';

// Get IDs of players already in game_results for this game
$attending_ids = [];
$stmt = $mysqli->prepare("SELECT user_id FROM game_results WHERE game_event_id = ?");
$stmt->bind_param("i", $game_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $attending_ids[] = $row['user_id'];
}
$stmt->close();

// Fetch players from game_results ordered by position
if (!empty($attending_ids)) {
    $query = "SELECT u.id, u.first_name, u.last_name
              FROM game_results gr
              JOIN users u ON gr.user_id = u.id
              WHERE gr.game_event_id = ?
              ORDER BY gr.position ASC";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attending .= "<li class='list-group-item player-item d-flex align-items-center' data-userid='{$row['id']}'>
            <span class='move-handle mr-2'>☰</span> " . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "
        </li>";
    }
    $stmt->close();
}

// Fetch venue players excluding those already in game_results
$query = "SELECT u.id, u.first_name, u.last_name
          FROM users u
          JOIN player_venues pv ON u.id = pv.user_id
          WHERE pv.venue_id = ?";

if (!empty($attending_ids)) {
    $placeholders = implode(',', array_fill(0, count($attending_ids), '?'));
    $query .= " AND u.id NOT IN ($placeholders)";
}

$stmt = $mysqli->prepare($query);

if (!empty($attending_ids)) {
    $types = str_repeat('i', count($attending_ids));
    $stmt->bind_param("i" . $types, $venue_id, ...$attending_ids);
} else {
    $stmt->bind_param("i", $venue_id);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $available .= "<li class='list-group-item player-item d-flex align-items-center' data-userid='{$row['id']}'>
        <span class='move-handle mr-2'>☰</span> " . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "
    </li>";
}

$stmt->close();

// Return both lists as JSON
echo json_encode(['available' => $available, 'attending' => $attending]);
?>
