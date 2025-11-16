<?php
/**
 * Smart Database Connection for Render (PostgreSQL) and Railway (MySQL)
 * 
 * Automatically detects which database type to use based on environment
 * - Render: Uses PostgreSQL (PDO)
 * - Railway: Uses MySQL (MySQLi)
 * - Local: Uses MySQL (MySQLi)
 */

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'iwadco2_db';
$port = getenv('DB_PORT') ?: 3306;

// Detect if we're using PostgreSQL (Render) or MySQL (Railway/Local)
$use_postgresql = getenv('DB_TYPE') === 'postgresql' || 
                  getenv('DATABASE_URL') !== false ||
                  (strpos($host, 'render.com') !== false) ||
                  $port == 5432;

if ($use_postgresql) {
    // PostgreSQL Connection (for Render)
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
        
        // Set connection type for compatibility layer
        $conn->db_type = 'postgresql';
        
    } catch (PDOException $e) {
        error_log("PostgreSQL connection failed: " . $e->getMessage());
        if (getenv('APP_ENV') === 'development') {
            die("Database connection failed: " . $e->getMessage());
        } else {
            die("Database connection failed. Please contact the administrator.");
        }
    }
} else {
    // MySQL Connection (for Railway/Local)
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    
    if ($conn->connect_error) {
        if (getenv('APP_ENV') === 'development' || empty(getenv('DB_HOST'))) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            error_log("Database connection failed: " . $conn->connect_error);
            die("Database connection failed. Please contact the administrator.");
        }
    }
    
    $conn->set_charset("utf8mb4");
    $conn->db_type = 'mysql';
}

// Compatibility functions for MySQLi -> PDO conversion
if (isset($conn->db_type) && $conn->db_type === 'postgresql') {
    // Add MySQLi-compatible methods for PostgreSQL PDO
    if (!function_exists('mysqli_query_compat')) {
        function mysqli_query_compat($conn, $query) {
            if ($conn->db_type === 'postgresql') {
                return $conn->query($query);
            }
            return $conn->query($query);
        }
    }
}
?>

