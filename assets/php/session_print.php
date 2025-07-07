<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Encode session data safely
$session_json = json_encode($_SESSION);

// Print JavaScript to console
echo "<script>console.log('PHP Session:', $session_json);</script>";
?>