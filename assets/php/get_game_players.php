<?php
require("conn.php");
session_start();

if (!isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    die(json_encode(["error" => "Invalid game ID."]));
}

$game_id = intval($_GET['game_id']);

// Fetch available players with last game date for sorting
$availableSql = "SELECT u.id, u.first_name, u.last_name, u.username,
                    MAX(ge.event_date) AS last_game
                FROM player_venues pv
                JOIN users u ON pv.user_id = u.id
                LEFT JOIN game_results gr ON gr.user_id = u.id
                LEFT JOIN game_events ge ON gr.game_event_id = ge.id
                WHERE pv.venue_id = (SELECT venue_id FROM game_events WHERE id = ?)
                  AND u.id NOT IN (SELECT user_id FROM game_results WHERE game_event_id = ?)
                GROUP BY u.id
                ORDER BY last_game DESC, u.last_name, u.first_name";

$availableStmt = $mysqli->prepare($availableSql);
$availableStmt->bind_param("ii", $game_id, $game_id);
$availableStmt->execute();
$availableResult = $availableStmt->get_result();

$availablePlayers = "";
while ($row = $availableResult->fetch_assoc()) {
    $username = htmlspecialchars($row['username']);
    $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);

    $availablePlayers .= "<li class='list-group-item player-item d-flex' data-userid='{$row['id']}'>
        <div class='flex-grow-1'>{$fullName} ({$username})</div>
      </li>";
}
$availableStmt->close();

// Fetch attending players (no extra data needed)
$attendingSql = "SELECT u.id, u.first_name, u.last_name, u.username
                FROM game_results gr
                JOIN users u ON gr.user_id = u.id
                WHERE gr.game_event_id = ?
                ORDER BY gr.position ASC";

$attendingStmt = $mysqli->prepare($attendingSql);
$attendingStmt->bind_param("i", $game_id);
$attendingStmt->execute();
$attendingResult = $attendingStmt->get_result();

$attendingPlayers = "";
while ($row = $attendingResult->fetch_assoc()) {
    $username = htmlspecialchars($row['username']);
    $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);

    $attendingPlayers .= "<li class='list-group-item player-item d-flex' data-userid='{$row['id']}'>
        <div class='flex-grow-1'>{$fullName} ({$username})</div>
      </li>";
}
$attendingStmt->close();

echo json_encode([
    "available" => $availablePlayers,
    "attending" => $attendingPlayers
]);
?>
