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

if (! preg_match("/.{3,16}/",$_POST["username"]))  {
    die("A username between 3 and 16 characters is required");
}

if (! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))  {
    die("Invalid email");
}

if (! preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,16}/", $_POST["password"])) {
    die("Password must be between 8 and 16 characters. <br> It must contain at least one upercase, one lowercase letter, and one digit");
}

if ($_POST["password"] !== $_POST["confirm-password"]) {
    die("Password fields must match");
}

$newPasswordHash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require "../conn.php";

$sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, phone)
        VALUES (?,?,?,?,?,?)";

$stmt = $mysqli->stmt_init();

 if  ( ! $stmt->prepare($sql)) {
     die("SQL error: <br><br>" . $mysqli->error);
 } 

$stmt->bind_param("ssssss",
                    $_POST["username"],
                    $_POST["email"],
                    $newPasswordHash,
                    $_POST["first_name"],
                    $_POST["last_name"],
                    $_POST["phone"],
                );
$stmt->execute();

header( "refresh:6;url=../../../index.html" );
  echo 'You registered successfully, you should now be able to log in <br><br> We will take you back to the Home Page in 6 secs. <br><br> Or, you can just click <a href="../../../index.html">here</a> now.';


//echo $newPasswordHash;
// echo ("<br><br>");
//echo $mysqli->error;
// print_r($_POST);
// sleep(3);
// header("Location: ../../../index.html");