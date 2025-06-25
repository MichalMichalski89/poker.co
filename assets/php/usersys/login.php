<?php
echo "<script>console.log('session started');</script>"
session_start();
require '../conn.php'; // your PDO connection setup

// Get form data
$username = $_POST['username'];
$password = $_POST['password'];

// Lookup user by username only
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    // Password is correct — start session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['first_name'] = $user['first_name'];
    // $_SESSION['role'] = $user['role']; // if you have roles

    // Redirect to dashboard or home page
    header("Location: dashboard.php");
    exit;
} else {
    // Invalid login
    echo "Invalid username or password.";
}
?>
