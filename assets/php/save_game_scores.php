<?php
require("conn.php");

$gameId = $_POST['game_id'];
$playerIds = $_POST['player_ids']; // array of user IDs in finishing order

// Delete old results for this game
$stmt = $mysqli->prepare("DELETE FROM game_results WHERE game_event_id = ?");
$stmt->bind_param("i", $gameId);
$stmt->execute();
$stmt->close();

// Insert new results in order
$position = 1;
foreach ($playerIds as $userId) {
    $stmt = $mysqli->prepare("INSERT INTO game_results (game_event_id, user_id, position) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $gameId, $userId, $position);
    $stmt->execute();
    $position++;
}
$stmt->close();

echo "success";
?>
