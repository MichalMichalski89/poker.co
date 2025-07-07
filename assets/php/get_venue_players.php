<?php
$conn = require __DIR__ . "/conn.php";

if (!isset($_GET['venue_id']) || !is_numeric($_GET['venue_id'])) {
    echo "<p>Invalid venue ID.</p>";
    exit;
}

$venue_id = (int)$_GET['venue_id'];

$sql = "
SELECT u.id, u.username, u.first_name, u.last_name, u.email
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

// Output a table or list with player info
echo "<table class='table table-sm'>";
echo "<thead><tr><th>Name</th><th>Username</th><th>Email</th></tr></thead>";
echo "<tbody>";

while ($row = $result->fetch_assoc()) {
    $fullName = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
    $username = htmlspecialchars($row['username']);
    $email = htmlspecialchars($row['email']);

    echo "<tr>";
    echo "<td>{$fullName}</td>";
    echo "<td>{$username}</td>";
    echo "<td>{$email}</td>";
    echo "</tr>";
}

echo "</tbody></table>";

$stmt->close();
$conn->close();
