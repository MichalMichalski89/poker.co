<?php
require("conn.php");
session_start();

if (!isset($_GET['venue_id']) || !is_numeric($_GET['venue_id'])) {
    die("Invalid venue ID.");
}

$venue_id = intval($_GET['venue_id']);
$user_id  = $_SESSION['user_id'] ?? 0;

// Check if user is TD for this venue
$sql = "SELECT COUNT(*) FROM user_venue_roles WHERE user_id = ? AND venue_id = ? AND role = 'tournament_director'";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $user_id, $venue_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count === 0) {
    die("Access denied.");
}

// Get filter option
$filter = $_GET['filter'] ?? 'last_game';

// Query logic based on filter
if ($filter === 'this_season') {
    $sql = "SELECT g.id, g.event_date, g.game_type, s.name AS season_name, v.name AS venue_name
            FROM game_events g
            JOIN seasons s ON g.season_id = s.id
            JOIN venues v ON g.venue_id = v.id
            WHERE g.venue_id = ?
              AND g.event_date <= CURDATE()
              AND s.is_active = 1
            ORDER BY g.event_date DESC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $venue_id);

} else { // default to 'last_game'
    $sql = "SELECT g.id, g.event_date, g.game_type, s.name AS season_name, v.name AS venue_name
            FROM game_events g
            JOIN seasons s ON g.season_id = s.id
            JOIN venues v ON g.venue_id = v.id
            WHERE g.venue_id = ?
              AND g.event_date <= CURDATE()
              AND s.is_active = 1
            ORDER BY g.event_date DESC
            LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $venue_id);
}

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No games to show with these filters.</p>";
} else {
    echo '<table class="table table-sm table-bordered table-hover">';
    echo '<thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Season</th>
                <th>Actions</th>
            </tr>
          </thead><tbody>';

    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $gameId     = $row['id'];
        $eventDate  = htmlspecialchars($row['event_date']);
        $gameType   = htmlspecialchars($row['game_type']);
        $seasonName = htmlspecialchars($row['season_name']);
        $venueName  = htmlspecialchars($row['venue_name']);

        // Check if scores exist for this game
        $scoreSql = "SELECT COUNT(*) FROM game_results WHERE game_event_id = ?";
        $scoreStmt = $mysqli->prepare($scoreSql);
        $scoreStmt->bind_param("i", $gameId);
        $scoreStmt->execute();
        $scoreStmt->bind_result($scoresCount);
        $scoreStmt->fetch();
        $scoreStmt->close();

        $actionButton = ($scoresCount > 0)
            ? "<button class='btn btn-sm btn-outline-warning edit-scores-btn' 
                    data-gameid='$gameId' 
                    data-date='$eventDate' 
                    data-type='$gameType'
                    data-venuename='$venueName'
                    data-venueid='$venue_id'>Edit Scores</button>"
            : "<button class='btn btn-sm btn-outline-primary add-scores-btn' 
                    data-gameid='$gameId' 
                    data-date='$eventDate' 
                    data-type='$gameType'
                    data-venuename='$venueName'
                    data-venueid='$venue_id'>Add Scores</button>";

        echo "<tr>
                <td>{$i}</td>
                <td>{$eventDate}</td>
                <td>{$gameType}</td>
                <td>{$seasonName}</td>
                <td>{$actionButton}</td>
              </tr>";
        $i++;
    }

    echo "</tbody></table>";
}

$stmt->close();
?>
