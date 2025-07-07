<?php
session_start();
header('Content-Type: application/json');

$conn = require "../conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter both username and password.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, username, first_name, last_name, password_hash FROM users WHERE username = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $db_username, $db_firstname, $db_lastname, $db_password_hash);
        $stmt->fetch();

        if (password_verify($password, $db_password_hash)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['firstname'] = $db_firstname;
            $_SESSION['lastname'] = $db_lastname;

            // Dashboard toast message
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Welcome back, ' . htmlspecialchars($db_firstname) . '!'
            ];

            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    exit;

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}
?>
