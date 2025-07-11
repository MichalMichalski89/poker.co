<?php
$conn = require __DIR__ . "/conn.php";

if (!isset($_GET['venue_id']) || !is_numeric($_GET['venue_id'])) {
    echo "<p>Invalid venue ID.</p>";
    exit;
}

$venue_id = (int)$_GET['venue_id'];

$sql = "
SELECT u.id, u.username, u.first_name, u.last_name
FROM users u
JOIN player_venues pv ON u.id = pv.user_id
WHERE pv.venue_id = ?
ORDER BY u.username ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<p>Failed to prepare statement: " . htmlspecialchars($conn->error) . "</p>";
    exit;
}

$stmt->bind_param("i", $venue_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No players found for this venue.</p>";
    exit;
}

echo "<ul class='list-group'>";
while ($row = $result->fetch_assoc()) {
    $playerName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
    $userId = (int)$row['id'];
    echo "<li class='list-group-item draggable-player' data-userid='{$userId}'>" . $playerName . "</li>";
}
echo "</ul>";

$stmt->close();
$conn->close();
