<?php

// function console_log($output, $with_script_tags = true) {
//     $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
// ');';
//     if ($with_script_tags) {
//         $js_code = '<script>' . $js_code . '</script>';
//     }
//     echo $js_code;
// }
// console_log("email verify running!!!");

$mysqli = require "conn.php";

$sql = sprintf("SELECT * FROM users WHERE email ='%s'",
$mysqli->real_escape_string($_POST["email"]));

$result = $mysqli->query($sql);

$user = $result->fetch_assoc();

$output["valid"] = false;

if ($user)  {
    if (password_verify($_POST["password"], $user["password_hash"])) {
        die ("hghghg");
    }
}

header("Content-Type: application/json");

    echo json_encode($result->num_rows === 0);

?>