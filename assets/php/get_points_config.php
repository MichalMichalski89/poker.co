<?php
header('Content-Type: application/json');

// Safely load points config
$configFile = 'points_config.php';

if (!file_exists($configFile)) {
    echo json_encode(["error" => "Points configuration file not found."]);
    exit;
}

// Load config
$config = require $configFile;

// Validate structure (optional, but good practice)
if (!is_array($config)) {
    echo json_encode(["error" => "Invalid points configuration format."]);
    exit;
}

// Output JSON
echo json_encode($config);
?>
