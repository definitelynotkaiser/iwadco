<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'iwadco2_db'; // ✅ ito dapat ang database name mo

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
