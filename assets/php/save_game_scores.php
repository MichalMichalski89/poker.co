<?php
require("conn.php");
session_start();

// Load points config
$POINTS_CONFIG = require("points_config.php");

// Points calculation function
function calculatePoints($position) {
    global $POINTS_CONFIG;
    return $POINTS_CONFIG['POINTS_TABLE'][$position] ?? $POINTS_CONFIG['PARTICIPATION_POINTS'];
}

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

// Validate game_id
if (!isset($_POST['game_id']) || !is_numeric($_POST['game_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid game ID."]);
    exit;
}

$game_id = intval($_POST['game_id']);
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id === 0) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit;
}

// Validate scores array
if (!isset($_POST['scores']) || !is_array($_POST['scores'])) {
    echo json_encode(["success" => false, "message" => "No scores provided."]);
    exit;
}

// Check Tournament Director permission for venue
$venueCheckSql = "
    SELECT v.id 
    FROM game_events g
    JOIN venues v ON g.venue_id = v.id
    JOIN user_venue_roles uvr ON uvr.venue_id = v.id
    WHERE g.id = ?
      AND uvr.user_id = ?
      AND uvr.role = 'tournament_director'
    LIMIT 1
";

$venueCheckStmt = $mysqli->prepare($venueCheckSql);
$venueCheckStmt->bind_param("ii", $game_id, $user_id);
$venueCheckStmt->execute();
$venueCheckStmt->store_result();

if ($venueCheckStmt->num_rows === 0) {
    $venueCheckStmt->close();
    echo json_encode(["success" => false, "message" => "You are not authorized to save scores for this game."]);
    exit;
}
$venueCheckStmt->close();

// Start transaction
$mysqli->begin_transaction();

try {
    // Delete existing results for this game
    $deleteStmt = $mysqli->prepare("DELETE FROM game_results WHERE game_event_id = ?");
    $deleteStmt->bind_param("i", $game_id);
    $deleteStmt->execute();
    $deleteStmt->close();

    // Insert new results with calculated points
    $insertStmt = $mysqli->prepare("INSERT INTO game_results (game_event_id, user_id, position, points) VALUES (?, ?, ?, ?)");

    foreach ($_POST['scores'] as $entry) {
        $user_id_entry = intval($entry['user_id']);
        $position = intval($entry['position']);
        $points = calculatePoints($position);

        $insertStmt->bind_param("iiii", $game_id, $user_id_entry, $position, $points);
        $insertStmt->execute();
    }

    $insertStmt->close();

    // Commit transaction
    $mysqli->commit();

    echo json_encode(["success" => true, "message" => "Scores and points saved successfully."]);

} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(["success" => false, "message" => "Error saving scores: " . $e->getMessage()]);
}

?>
