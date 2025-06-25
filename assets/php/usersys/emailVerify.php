<?php



$mysqli = require "../conn.php";

$sql = sprintf("SELECT * FROM users WHERE email ='%s'",
$mysqli->real_escape_string($_POST["email"]));

$result = $mysqli->query($sql);

header("Content-Type: application/json");

    echo json_encode($result->num_rows === 0);

?>