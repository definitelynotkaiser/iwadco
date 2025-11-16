<?php
/**
 * Database Connection Switcher
 * 
 * This file automatically detects the environment and uses the appropriate
 * database configuration.
 * 
 * Usage: Replace 'include("db_connect.php")' with 'include("db_connect_switch.php")'
 * in all your PHP files for automatic environment detection.
 */

// Detect if we're on localhost or production
$is_local = (
    $_SERVER['HTTP_HOST'] === 'localhost' ||
    $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
    strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0 ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1:') === 0 ||
    strpos($_SERVER['HTTP_HOST'], '.local') !== false ||
    strpos($_SERVER['HTTP_HOST'], '.test') !== false
);

if ($is_local) {
    // Local development (XAMPP)
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'iwadco2_db';
} else {
    // Production - try to load from production config file
    if (file_exists(__DIR__ . '/db_connect_production.php')) {
        // Include production config if it exists
        include(__DIR__ . '/db_connect_production.php');
        return; // Exit early since production file should define $conn
    } else {
        // Fallback: use environment variables or default production settings
        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'your_username';
        $pass = getenv('DB_PASS') ?: 'your_password';
        $dbname = getenv('DB_NAME') ?: 'iwadco2_db';
    }
}

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    if ($is_local) {
        // Show detailed error in local development
        die("Connection failed: " . $conn->connect_error);
    } else {
        // Hide detailed error in production
        error_log("Database connection failed: " . $conn->connect_error);
        die("Database connection failed. Please contact the administrator.");
    }
}
?>

