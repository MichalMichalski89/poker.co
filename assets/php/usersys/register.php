<?php
session_start();

function exception_handler($e) {
    error_log("Registration Error: " . $e->getMessage());
    header("Location: ../../../index.php?register_error=1");
    exit;
}
set_exception_handler('exception_handler');

// Validate inputs
if (!preg_match("/.{3,16}/", $_POST["username"])) {
    throw new Exception("A username between 3 and 16 characters is required.");
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    throw new Exception("Invalid email address.");
}

if (!preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,16}/", $_POST["password"])) {
    throw new Exception("Password must be 8-16 chars, with upper, lower and digit.");
}

if ($_POST["password"] !== $_POST["confirm-password"]) {
    throw new Exception("Password fields must match.");
}

$newPasswordHash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Connect to database
$mysqli = require "../conn.php";

// Prepare statement
$sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, phone)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->stmt_init();
if (!$stmt->prepare($sql)) {
    throw new Exception("SQL error: " . $mysqli->error);
}

$stmt->bind_param("ssssss",
    $_POST["username"],
    $_POST["email"],
    $newPasswordHash,
    $_POST["first_name"],
    $_POST["last_name"],
    $_POST["phone"]
);

$stmt->execute();

// Redirect on success
header("Location: ../../../index.php?registered=1");
exit;
?>
