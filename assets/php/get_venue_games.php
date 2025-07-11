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

// Prepare dynamic WHERE clause
$where = "WHERE g.venue_id = ?";
$params = [$venue_id];
$types = "i";

if (isset($_GET['season_id'])) {
    $season_id = $_GET['season_id'];

    if ($season_id === "active") {
        $where .= " AND s.is_active = 1";
    } elseif (is_numeric($season_id)) {
        $where .= " AND g.season_id = ?";
        $params[] = intval($season_id);
        $types .= "i";
    }
}

// Query
$sql = "
    SELECT g.id, g.event_date, g.game_type, s.name AS season_name
    FROM game_events g
    LEFT JOIN seasons s ON g.season_id = s.id
    $where
    ORDER BY g.event_date ASC
";

$stmt = $mysqli->prepare($sql);

// Bind dynamic params
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No upcoming games scheduled.</p>";
} else {
    echo '<table class="table table-sm table-bordered table-hover">';
    echo '<thead class="thead-light"><tr><th>#</th><th>Date</th><th>Type</th><th>Season</th><th>Actions</th></tr></thead><tbody>';

    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $gameType = htmlspecialchars($row['game_type'] ?? '');
        $seasonName = htmlspecialchars($row['season_name'] ?? 'Unassigned');
        echo "<tr>";
        echo "<td>{$i}</td>";
        echo "<td>" . htmlspecialchars($row['event_date']) . "</td>";
        echo "<td>{$gameType}</td>";
        echo "<td>{$seasonName}</td>";
        echo "<td>
                <button class='btn btn-sm btn-outline-info'>View</button>
                <button class='btn btn-sm btn-outline-danger'>Delete</button>
              </td>";
        echo "</tr>";
        $i++;
    }

    echo "</tbody></table>";
}

$stmt->close();
?>
