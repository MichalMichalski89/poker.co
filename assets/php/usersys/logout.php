<?php
session_start();
session_unset();
session_destroy();

// Redirect back to root homepage
header("Location: ../../../index.php");
exit;
?>
