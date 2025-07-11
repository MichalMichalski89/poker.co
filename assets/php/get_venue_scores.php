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

// Fetch existing results
$sql = "
  SELECT gr.id, ge.event_date, u.first_name, u.last_name, gr.position, gr.points
  FROM game_results gr
  JOIN game_events ge ON gr.game_id = ge.id
  JOIN users u ON gr.user_id = u.id
  WHERE ge.venue_id = ?
  ORDER BY ge.event_date DESC, gr.position ASC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $venue_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No scores recorded yet.</p>";
} else {
    echo '<table class="table table-sm table-bordered table-hover">';
    echo '<thead class="thead-light">
            <tr><th>Game Date</th><th>Player</th><th>Position</th><th>Points</th></tr>
          </thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        $playerName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
        $date       = htmlspecialchars($row['event_date']);
        $position   = (int)$row['position'];
        $points     = (int)$row['points'];
        echo "<tr>
                <td>{$date}</td>
                <td>{$playerName}</td>
                <td>{$position}</td>
                <td>{$points}</td>
              </tr>";
    }
    echo '</tbody></table>';
}

$stmt->close();
?>
