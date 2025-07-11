<?php
require("conn.php");

$sql = "SELECT id, name FROM seasons ORDER BY id DESC";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
    }
} else {
    echo '<option disabled>No seasons found</option>';
}
?>
