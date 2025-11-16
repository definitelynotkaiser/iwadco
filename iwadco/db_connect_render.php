<?php
/**
 * Database Connection for Render
 * 
 * This file uses environment variables for database configuration.
 * Set these in your Render dashboard under Environment Variables.
 * 
 * Required Environment Variables:
 * - DB_HOST: Database host (e.g., your-mysql-host.com)
 * - DB_USER: Database username
 * - DB_PASS: Database password
 * - DB_NAME: Database name
 * - DB_PORT: Database port (default: 3306 for MySQL)
 */

// Get database credentials from environment variables
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'iwadco2_db';
$port = getenv('DB_PORT') ?: 3306;

// Create connection with port specification
$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    // Log error but don't expose details in production
    error_log("Database connection failed: " . $conn->connect_error);
    
    // Show user-friendly error
    if (getenv('APP_ENV') === 'development') {
        die("Database connection failed: " . $conn->connect_error);
    } else {
        die("Database connection failed. Please contact the administrator.");
    }
}

// Set charset to utf8mb4 for proper character encoding
$conn->set_charset("utf8mb4");
?>

