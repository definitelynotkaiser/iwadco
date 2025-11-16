<?php
/**
 * Database Connection
 * 
 * Supports both local development (XAMPP) and production (Railway/Render/Cloud)
 * Uses environment variables if available (for cloud platforms), otherwise uses local defaults
 * 
 * Railway: Automatically uses DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT from environment
 * Render: Same - uses environment variables
 * Local: Falls back to localhost defaults
 */

// Check if we're on Render (has environment variables) or local
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'iwadco2_db';
$port = getenv('DB_PORT') ?: 3306;

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    // In production, don't expose detailed errors
    if (getenv('APP_ENV') === 'development' || empty(getenv('DB_HOST'))) {
        // Local development - show detailed error
        die("Connection failed: " . $conn->connect_error);
    } else {
        // Production - log error, show generic message
        error_log("Database connection failed: " . $conn->connect_error);
        die("Database connection failed. Please contact the administrator.");
    }
}

// Set charset for proper character encoding
$conn->set_charset("utf8mb4");
?>
