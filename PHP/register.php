<?php

function exception_handler($e) {
    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
    ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

    console_log("Exception ERROR. ref:  " . $e->getCode() );
    // . " Error message:" . $e->getMessage()
    // echo "<b>Something went wrong.</b>  <br><br>";
    // echo "<b>Exception in file: </b>" . $e->getFile() . "<br>";
    // echo "<b>Exception on line: </b>" . $e->getLine() . "<br>";
	// echo "<b>code:</b>" . $e->getCode() . "<br>";
    // echo "<b>previous:</b>" . $e->getPrevious() . "<br>";
    // echo "<b>message:</b>" . $e->getMessage() . "<br>";
}

set_exception_handler('exception_handler');

//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (! preg_match("/.{3,16}/",$_POST["new-username"]))  {
    die("A username between 3 and 16 characters is required");
}

if (! filter_var($_POST["new-email"], FILTER_VALIDATE_EMAIL))  {
    die("Invalid email");
}

if (! preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,16}/", $_POST["new-password"])) {
    die("Password must be between 8 and 16 characters. <br> It must contain at least one upercase, one lowercase letter, and one digit");
}

if ($_POST["new-password"] !== $_POST["new-password-confirm"]) {
    die("Password fields must match");
}

$newPasswordHash = password_hash($_POST["new-password"], PASSWORD_DEFAULT);

$mysqli = require "conn.php";

$sql = "INSERT INTO users (username, email, password_hash)
        VALUES (?, ?, ?)";

$stmt = $mysqli->stmt_init();

 if  ( ! $stmt->prepare($sql)) {
     die("SQL error: <br><br>" . $mysqli->error);
 } 

$stmt->bind_param("sss",
                    $_POST["new-username"],
                    $_POST["new-email"],
                    $newPasswordHash);

$stmt->execute();

header("Location: reg-success.html");
//echo $newPasswordHash;

//echo $mysqli->error;
// print_r($_POST);