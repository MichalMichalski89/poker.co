<?php

// function console_log($output, $with_script_tags = true) {
//     $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
// ');';
//     if ($with_script_tags) {
//         $js_code = '<script>' . $js_code . '</script>';
//     }
//     echo $js_code;
// }
// console_log("user verify running!!!");

$mysqli = require "conn.php";

$sql = sprintf("SELECT * FROM users WHERE username ='%s'",
$mysqli->real_escape_string($_POST["username"]));

$result = $mysqli->query($sql);

header("Content-Type: application/json");

    echo json_encode($result->num_rows === 0);

?>