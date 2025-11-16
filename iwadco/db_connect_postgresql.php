<?php
/**
 * PostgreSQL Database Connection for Render
 * 
 * This file uses PostgreSQL (Render's default database)
 * Set these environment variables in your Render dashboard:
 * - DB_HOST: Database host
 * - DB_USER: Database username
 * - DB_PASS: Database password
 * - DB_NAME: Database name
 * - DB_PORT: Database port (usually 5432 for PostgreSQL)
 */

// Get database credentials from environment variables
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'postgres';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'iwadco2_db';
$port = getenv('DB_PORT') ?: 5432;

// Create PostgreSQL connection
try {
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Log error but don't expose details in production
    error_log("Database connection failed: " . $e->getMessage());
    
    if (getenv('APP_ENV') === 'development' || empty(getenv('DB_HOST'))) {
        // Local development - show detailed error
        die("Database connection failed: " . $e->getMessage());
    } else {
        // Production - show generic message
        die("Database connection failed. Please contact the administrator.");
    }
}
?>

