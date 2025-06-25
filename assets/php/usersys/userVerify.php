<?php



$mysqli = require "../conn.php";

$sql = sprintf("SELECT * FROM users WHERE username ='%s'",
$mysqli->real_escape_string($_POST["username"]));

$result = $mysqli->query($sql);

header("Content-Type: application/json");

    echo json_encode($result->num_rows === 0);

?>