<?php
session_start();

// Get current script location relative to root
$currentScript = $_SERVER['PHP_SELF'];

// Check where this script is being accessed from
$isRootIndex   = ($currentScript === '/index.php');
$isDashboard   = (strpos($currentScript, '/dashboard/') !== false);

// Session timeout limit (in seconds)
$sessionTimeout = 480;

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout)) {
    // Expired session: clear and destroy
    session_unset();
    session_destroy();

    if ($isDashboard) {
        header("Location: ../index.php?session_expired=1");
        exit;
    } elseif ($isRootIndex) {
        header("Location: index.php?session_expired=1");
        exit;
    }
}

// Update last activity timestamp if logged in
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
} else {
    // If user not logged in and on a protected dashboard page — redirect to root login
    if ($isDashboard) {
        header("Location: ../index.php?session_required=1");
        exit;
    }
    // If user is not logged in at root index, allow them to stay (since login modal is there)
}
?>
